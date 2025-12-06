<?php
session_start();
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_login'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

try {
    $messageType = $_POST['messageType'] ?? '';
    $broadcastType = $_POST['broadcastType'] ?? '';
    $selectedUsers = $_POST['users'] ?? [];

    if (empty($messageType)) {
        throw new Exception('Message type is required');
    }

    if (empty($broadcastType)) {
        throw new Exception('Broadcast type is required');
    }

    $success = true;
    $errors = [];

    if ($broadcastType === 'all') {
        // Get all users with chat_id
        $stmt = $conn->prepare("SELECT chat_id, id FROM users WHERE chat_id IS NOT NULL");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $userIds = array_column($users, 'id');
    } else {
        // Send to selected users
        if (empty($selectedUsers)) {
            throw new Exception('Please select at least one user');
        }
        
        // Get user IDs for selected chat_ids
        $placeholders = str_repeat('?,', count($selectedUsers) - 1) . '?';
        $stmt = $conn->prepare("SELECT id FROM users WHERE chat_id IN ($placeholders)");
        $stmt->execute($selectedUsers);
        $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $users = array_map(function($chat_id) {
            return (object)['chat_id' => $chat_id];
        }, $selectedUsers);
    }

    // Handle different message types
    if ($messageType === 'meeting') {
        $meetingTitle = $_POST['meetingTitle'] ?? '';
        $meetingDate = $_POST['meetingDate'] ?? '';
        $meetingTime = $_POST['meetingTime'] ?? '';
        $meetingCreator = $_POST['meetingCreator'] ?? '';
        $meetingDescription = $_POST['meetingDescription'] ?? '';

        if (empty($meetingTitle) || empty($meetingDate) || empty($meetingTime) || empty($meetingCreator)) {
            throw new Exception('All meeting fields are required');
        }

        // Save meeting to database
        $meetingId = saveMeetingToDatabase($meetingTitle, $meetingDate, $meetingTime, $meetingCreator, $meetingDescription, $broadcastType);

        // Create notification for meeting
        createMeetingNotification($meetingTitle, $meetingCreator, $userIds);

        foreach ($users as $user) {
            $result = sendMeetingMessage($user->chat_id, $meetingId, $meetingTitle, $meetingDate, $meetingTime, $meetingCreator, $meetingDescription);
            if (!$result) {
                $errors[] = "Failed to send meeting message to chat ID: {$user->chat_id}";
                $success = false;
            }
        }
    } elseif ($messageType === 'voting') {
        $votingTitle = $_POST['votingTitle'] ?? '';
        $votingDescription = $_POST['votingDescription'] ?? '';
        $votingOptions = $_POST['votingOptions'] ?? [];

        if (empty($votingTitle) || empty($votingOptions) || count($votingOptions) < 2) {
            throw new Exception('Voting title and at least 2 options are required');
        }

        // Save voting poll to database
        $pollId = saveVotingPollToDatabase($votingTitle, $votingDescription, $broadcastType, $votingOptions);

        // Create notification for voting poll
        createVotingNotification($votingTitle, $userIds);

        foreach ($users as $user) {
            $result = sendVotingMessage($user->chat_id, $pollId, $votingTitle, $votingDescription, $votingOptions);
            if (!$result) {
                $errors[] = "Failed to send voting message to chat ID: {$user->chat_id}";
                $success = false;
            }
        }
    } else {
        throw new Exception('Invalid message type');
    }

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => ucfirst($messageType) . ' message sent successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Some messages failed to send',
            'errors' => $errors
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Save meeting information to database
 */
function saveMeetingToDatabase($title, $date, $time, $creator, $description, $broadcastType) {
    global $conn, $my_profile_id;
    
    $stmt = $conn->prepare("INSERT INTO meetings (title, meeting_date, meeting_time, creator_name, description, broadcast_type, created_by) VALUES (:title, :date, :time, :creator, :description, :broadcast_type, :created_by)");
    $stmt->execute([
        'title' => $title,
        'date' => $date,
        'time' => $time,
        'creator' => $creator,
        'description' => $description,
        'broadcast_type' => $broadcastType,
        'created_by' => $my_profile_id
    ]);
    
    return $conn->lastInsertId();
}

/**
 * Save voting poll to database
 */
function saveVotingPollToDatabase($title, $description, $broadcastType, $options) {
    global $conn, $my_profile_id;
    
    // Start transaction
    $conn->beginTransaction();
    
    try {
        // Insert voting poll
        $stmt = $conn->prepare("INSERT INTO voting_polls (title, description, broadcast_type, created_by) VALUES (:title, :description, :broadcast_type, :created_by)");
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'broadcast_type' => $broadcastType,
            'created_by' => $my_profile_id
        ]);
        
        $pollId = $conn->lastInsertId();
        
        // Insert voting options
        $stmt = $conn->prepare("INSERT INTO voting_options (poll_id, option_text, option_order) VALUES (:poll_id, :option_text, :option_order)");
        
        foreach ($options as $index => $option) {
            if (!empty(trim($option))) {
                $stmt->execute([
                    'poll_id' => $pollId,
                    'option_text' => trim($option),
                    'option_order' => $index + 1
                ]);
            }
        }
        
        $conn->commit();
        return $pollId;
        
    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }
}

/**
 * Send meeting information with accept/decline buttons
 */
function sendMeetingMessage($chat_id, $meetingId, $title, $date, $time, $creator, $description = '') {
    $token = "7657734664:AAGpbkasmOSmXcldRWOeg5xpJSkkwl-cTWU";
    $url = "https://api.telegram.org/bot$token/sendMessage";

    // Get user info from the database using chat_id
    global $conn;
    $stmt = $conn->prepare("SELECT name FROM users WHERE chat_id = :chat_id");
    $stmt->execute(['chat_id' => $chat_id]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$user) {
        return false;
    }

    $name = $user->name;

    // Format the meeting message
    $message = "Hi *$name* 👋\n\n";
    $message .= "📅 *Meeting Information*\n\n";
    $message .= "📋 *Title:* $title\n";
    $message .= "📅 *Date:* $date\n";
    $message .= "🕐 *Time:* $time\n";
    $message .= "👤 *Creator:* $creator\n";
    
    if (!empty($description)) {
        $message .= "\n📝 *Description:*\n$description\n";
    }
    
    $message .= "\n-------------\n";
    $message .= "*Please respond with your attendance:*";

    // Create inline keyboard with accept/decline buttons
    $keyboard = [
        'inline_keyboard' => [
            [
                [
                    'text' => '✅ Accept',
                    'callback_data' => 'meeting_accept_' . $meetingId
                ],
                [
                    'text' => '❌ Decline',
                    'callback_data' => 'meeting_decline_' . $meetingId
                ]
            ]
        ]
    ];

    // Prepare data to send to Telegram API
    $data = [
        "chat_id" => $chat_id,
        "text" => $message,
        "parse_mode" => "Markdown",
        "reply_markup" => json_encode($keyboard)
    ];

    // Send the request to Telegram API using curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}

/**
 * Send voting poll with custom options
 */
function sendVotingMessage($chat_id, $pollId, $title, $description = '', $options = []) {
    $token = "7657734664:AAGpbkasmOSmXcldRWOeg5xpJSkkwl-cTWU";
    $url = "https://api.telegram.org/bot$token/sendMessage";

    // Get user info from the database using chat_id
    global $conn;
    $stmt = $conn->prepare("SELECT name FROM users WHERE chat_id = :chat_id");
    $stmt->execute(['chat_id' => $chat_id]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$user) {
        return false;
    }

    $name = $user->name;

    // Format the voting message
    $message = "Hi *$name* 👋\n\n";
    $message .= "🗳️ *Voting Poll*\n\n";
    $message .= "📋 *Title:* $title\n";
    
    if (!empty($description)) {
        $message .= "\n📝 *Description:*\n$description\n";
    }
    
    $message .= "\n-------------\n";
    $message .= "*Please select your vote:*";

    // Get voting options from database
    $stmt = $conn->prepare("SELECT id, option_text FROM voting_options WHERE poll_id = :poll_id ORDER BY option_order");
    $stmt->execute(['poll_id' => $pollId]);
    $dbOptions = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Create inline keyboard with voting options
    $keyboard_rows = [];
    foreach ($dbOptions as $option) {
        $keyboard_rows[] = [
            [
                'text' => $option->option_text,
                'callback_data' => 'vote_' . $pollId . '_' . $option->id
            ]
        ];
    }

    $keyboard = [
        'inline_keyboard' => $keyboard_rows
    ];

    // Prepare data to send to Telegram API
    $data = [
        "chat_id" => $chat_id,
        "text" => $message,
        "parse_mode" => "Markdown",
        "reply_markup" => json_encode($keyboard)
    ];

    // Send the request to Telegram API using curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}

/**
 * Create notification for meeting broadcast
 */
function createMeetingNotification($meetingTitle, $meetingCreator, $userIds) {
    global $conn, $my_profile_id;
    
    if (empty($userIds)) {
        return;
    }
    
    $notification_message = "{$meetingCreator} sent a meeting invitation: {$meetingTitle}";
    $receiver_ids = implode(',', $userIds);
    
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, receiver_ids, message, date, time, users_read) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $my_profile_id,
        $receiver_ids,
        $notification_message,
        date('Y-m-d'),
        date('H:i:s'),
        $my_profile_id // mark as read by sender
    ]);
}

/**
 * Create notification for voting poll broadcast
 */
function createVotingNotification($votingTitle, $userIds) {
    global $conn, $my_profile_id;
    
    if (empty($userIds)) {
        return;
    }
    
    $notification_message = "A new voting poll has been created: {$votingTitle}";
    $receiver_ids = implode(',', $userIds);
    
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, receiver_ids, message, date, time, users_read) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $my_profile_id,
        $receiver_ids,
        $notification_message,
        date('Y-m-d'),
        date('H:i:s'),
        $my_profile_id // mark as read by sender
    ]);
}
?> 