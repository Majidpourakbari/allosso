<?php
require_once 'controlls/db/functions.php';

// Get the incoming update from Telegram
$update = json_decode(file_get_contents('php://input'), true);

if (!$update) {
    exit;
}

$token = "7657734664:AAGpbkasmOSmXcldRWOeg5xpJSkkwl-cTWU";

// Handle callback queries (button clicks)
if (isset($update['callback_query'])) {
    $callback_query = $update['callback_query'];
    $callback_data = $callback_query['callback_data'];
    $chat_id = $callback_query['message']['chat']['id'];
    $message_id = $callback_query['message']['message_id'];
    $user_id = $callback_query['from']['id'];
    $username = $callback_query['from']['username'] ?? '';
    $first_name = $callback_query['from']['first_name'] ?? '';
    $last_name = $callback_query['from']['last_name'] ?? '';
    
    // Get user name from database
    $stmt = $conn->prepare("SELECT name FROM users WHERE chat_id = :chat_id");
    $stmt->execute(['chat_id' => $chat_id]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);
    $user_name = $user ? $user->name : ($first_name . ' ' . $last_name);

    // Handle meeting responses
    if (strpos($callback_data, 'meeting_') === 0) {
        handleMeetingResponse($callback_data, $chat_id, $message_id, $user_name, $token);
    }
    // Handle voting responses
    elseif (strpos($callback_data, 'vote_') === 0) {
        handleVotingResponse($callback_data, $chat_id, $message_id, $user_name, $token);
    }

    // Answer callback query to remove loading state
    answerCallbackQuery($callback_query['id'], $token);
}

/**
 * Handle meeting accept/decline responses
 */
function handleMeetingResponse($callback_data, $chat_id, $message_id, $user_name, $token) {
    $parts = explode('_', $callback_data);
    if (count($parts) >= 3) {
        $action = $parts[1]; // accept or decline
        $meetingId = $parts[2];

        $response_text = '';
        if ($action === 'accept') {
            $response_text = "✅ *$user_name* has accepted the meeting invitation";
        } else {
            $response_text = "❌ *$user_name* has declined the meeting invitation";
        }

        // Send response message
        $url = "https://api.telegram.org/bot$token/sendMessage";
        $data = [
            "chat_id" => $chat_id,
            "text" => $response_text,
            "parse_mode" => "Markdown"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        // Update the original message to show the response
        updateMeetingMessage($chat_id, $message_id, $meetingId, $action, $user_name, $token);
    }
}

/**
 * Handle voting responses
 */
function handleVotingResponse($callback_data, $chat_id, $message_id, $user_name, $token) {
    $parts = explode('_', $callback_data);
    if (count($parts) >= 3) {
        $pollId = $parts[1];
        $optionId = $parts[2];

        // Get option text
        global $conn;
        $stmt = $conn->prepare("SELECT option_text FROM voting_options WHERE id = :option_id");
        $stmt->execute(['option_id' => $optionId]);
        $option = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$option) {
            return;
        }

        $selected_option = $option->option_text;
        $response_text = "🗳️ *$user_name* voted for: *$selected_option*";

        // Send response message
        $url = "https://api.telegram.org/bot$token/sendMessage";
        $data = [
            "chat_id" => $chat_id,
            "text" => $response_text,
            "parse_mode" => "Markdown"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        // Update the original message to show the vote
        updateVotingMessage($chat_id, $message_id, $pollId, $optionId, $user_name, $token);
    }
}

/**
 * Update meeting message to show responses
 */
function updateMeetingMessage($chat_id, $message_id, $meetingId, $action, $user_name, $token) {
    global $conn;
    
    // Store the response in database
    try {
        $stmt = $conn->prepare("INSERT INTO meeting_responses (meeting_id, chat_id, user_name, response) VALUES (:meeting_id, :chat_id, :user_name, :response) ON DUPLICATE KEY UPDATE response = :response");
        $stmt->execute([
            'meeting_id' => $meetingId,
            'chat_id' => $chat_id,
            'user_name' => $user_name,
            'response' => $action
        ]);
    } catch (Exception $e) {
        // Log error if needed
    }

    // Get meeting information
    $stmt = $conn->prepare("SELECT title, meeting_date, meeting_time FROM meetings WHERE id = :meeting_id");
    $stmt->execute(['meeting_id' => $meetingId]);
    $meeting = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$meeting) {
        return;
    }

    // Update the original message with response info
    $url = "https://api.telegram.org/bot$token/editMessageText";
    
    // Get existing responses for this meeting
    $stmt = $conn->prepare("SELECT user_name, response FROM meeting_responses WHERE meeting_id = :meeting_id ORDER BY created_at DESC");
    $stmt->execute(['meeting_id' => $meetingId]);
    $responses = $stmt->fetchAll(PDO::FETCH_OBJ);

    $message = "📅 *Meeting Information*\n\n";
    $message .= "📋 *Title:* {$meeting->title}\n";
    $message .= "📅 *Date:* {$meeting->meeting_date}\n";
    $message .= "🕐 *Time:* {$meeting->meeting_time}\n";
    $message .= "\n-------------\n";
    $message .= "*Responses:*\n";

    $accepted = [];
    $declined = [];
    foreach ($responses as $response) {
        if ($response->response === 'accept') {
            $accepted[] = $response->user_name;
        } else {
            $declined[] = $response->user_name;
        }
    }

    if (!empty($accepted)) {
        $message .= "✅ *Accepted:* " . implode(', ', $accepted) . "\n";
    }
    if (!empty($declined)) {
        $message .= "❌ *Declined:* " . implode(', ', $declined) . "\n";
    }

    $data = [
        "chat_id" => $chat_id,
        "message_id" => $message_id,
        "text" => $message,
        "parse_mode" => "Markdown"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

/**
 * Update voting message to show votes
 */
function updateVotingMessage($chat_id, $message_id, $pollId, $optionId, $user_name, $token) {
    global $conn;
    
    // Store the vote in database
    try {
        $stmt = $conn->prepare("INSERT INTO voting_responses (poll_id, option_id, chat_id, user_name) VALUES (:poll_id, :option_id, :chat_id, :user_name) ON DUPLICATE KEY UPDATE option_id = :option_id");
        $stmt->execute([
            'poll_id' => $pollId,
            'option_id' => $optionId,
            'chat_id' => $chat_id,
            'user_name' => $user_name
        ]);
    } catch (Exception $e) {
        // Log error if needed
    }

    // Get poll information
    $stmt = $conn->prepare("SELECT title FROM voting_polls WHERE id = :poll_id");
    $stmt->execute(['poll_id' => $pollId]);
    $poll = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$poll) {
        return;
    }

    // Update the original message with vote results
    $url = "https://api.telegram.org/bot$token/editMessageText";
    
    // Get vote results for this poll
    $stmt = $conn->prepare("
        SELECT vo.option_text, COUNT(vr.id) as count 
        FROM voting_options vo 
        LEFT JOIN voting_responses vr ON vo.id = vr.option_id 
        WHERE vo.poll_id = :poll_id 
        GROUP BY vo.id, vo.option_text 
        ORDER BY vo.option_order
    ");
    $stmt->execute(['poll_id' => $pollId]);
    $results = $stmt->fetchAll(PDO::FETCH_OBJ);

    $message = "🗳️ *Voting Poll Results*\n\n";
    $message .= "📋 *Title:* {$poll->title}\n";
    $message .= "\n-------------\n";
    $message .= "*Results:*\n";

    foreach ($results as $result) {
        $message .= "• {$result->option_text}: {$result->count} votes\n";
    }

    $data = [
        "chat_id" => $chat_id,
        "message_id" => $message_id,
        "text" => $message,
        "parse_mode" => "Markdown"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

/**
 * Answer callback query to remove loading state
 */
function answerCallbackQuery($callback_query_id, $token) {
    $url = "https://api.telegram.org/bot$token/answerCallbackQuery";
    $data = [
        "callback_query_id" => $callback_query_id
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
?> 