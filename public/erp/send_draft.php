<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($my_profile_id)) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

try {
    $draftId = $_POST['draftId'] ?? '';
    $messageType = $_POST['messageType'] ?? '';
    
    if (empty($draftId) || empty($messageType)) {
        throw new Exception('Draft ID and message type are required');
    }

    if ($messageType === 'meeting') {
        // Get meeting draft details
        $stmt = $conn->prepare("SELECT * FROM meetings WHERE id = ? AND created_by = ? AND is_draft = TRUE");
        $stmt->execute([$draftId, $my_profile_id]);
        $meeting = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$meeting) {
            throw new Exception('Meeting draft not found or you do not have permission to send it');
        }

        // Get users to send to
        $users = getUsersForBroadcast($meeting->broadcast_type, $my_profile_id);
        
        if (empty($users)) {
            throw new Exception('No users found to send the meeting to');
        }

        // Send meeting messages
        $success = true;
        $errors = [];
        $userIds = [];

        foreach ($users as $user) {
            $userIds[] = $user->id;
            $result = sendMeetingMessage($user->chat_id, $meeting->id, $meeting->title, $meeting->meeting_date, $meeting->meeting_time, $meeting->creator_name, $meeting->description);
            if (!$result) {
                $errors[] = "Failed to send meeting message to chat ID: {$user->chat_id}";
                $success = false;
            }
        }

        // Mark draft as sent
        $stmt = $conn->prepare("UPDATE meetings SET is_draft = FALSE WHERE id = ?");
        $stmt->execute([$draftId]);

        // Create notification
        createMeetingNotification($meeting->title, $meeting->creator_name, $userIds);

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Meeting sent successfully' : 'Some messages failed to send',
            'errors' => $errors
        ]);

    } elseif ($messageType === 'voting') {
        // Get voting poll draft details
        $stmt = $conn->prepare("SELECT * FROM voting_polls WHERE id = ? AND created_by = ? AND is_draft = TRUE");
        $stmt->execute([$draftId, $my_profile_id]);
        $poll = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$poll) {
            throw new Exception('Voting poll draft not found or you do not have permission to send it');
        }

        // Get voting options
        $stmt = $conn->prepare("SELECT * FROM voting_options WHERE poll_id = ? ORDER BY option_order");
        $stmt->execute([$draftId]);
        $options = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        if (empty($options)) {
            throw new Exception('No voting options found for this poll');
        }

        // Get users to send to
        $users = getUsersForBroadcast($poll->broadcast_type, $my_profile_id);
        
        if (empty($users)) {
            throw new Exception('No users found to send the voting poll to');
        }

        // Send voting messages
        $success = true;
        $errors = [];
        $userIds = [];
        $optionTexts = array_map(function($option) { return $option->option_text; }, $options);

        foreach ($users as $user) {
            $userIds[] = $user->id;
            $result = sendVotingMessage($user->chat_id, $poll->id, $poll->title, $poll->description, $optionTexts);
            if (!$result) {
                $errors[] = "Failed to send voting message to chat ID: {$user->chat_id}";
                $success = false;
            }
        }

        // Mark draft as sent
        $stmt = $conn->prepare("UPDATE voting_polls SET is_draft = FALSE WHERE id = ?");
        $stmt->execute([$draftId]);

        // Create notification
        createVotingNotification($poll->title, $userIds);

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Voting poll sent successfully' : 'Some messages failed to send',
            'errors' => $errors
        ]);

    } else {
        throw new Exception('Invalid message type');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Get users for broadcast based on type
 */
function getUsersForBroadcast($broadcastType, $excludeUserId) {
    global $conn;
    
    if ($broadcastType === 'all') {
        $stmt = $conn->prepare("SELECT id, name, chat_id FROM users WHERE chat_id IS NOT NULL AND id != ?");
        $stmt->execute([$excludeUserId]);
    } else {
        // For selected users, this would need to be handled differently
        // For now, return all users except the sender
        $stmt = $conn->prepare("SELECT id, name, chat_id FROM users WHERE chat_id IS NOT NULL AND id != ?");
        $stmt->execute([$excludeUserId]);
    }
    
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

/**
 * Send meeting message via Telegram
 */
function sendMeetingMessage($chatId, $meetingId, $title, $date, $time, $creator, $description) {
    $message = "📅 *Meeting Invitation*\n\n";
    $message .= "📋 *Title:* {$title}\n";
    $message .= "📅 *Date:* {$date}\n";
    $message .= "🕐 *Time:* {$time}\n";
    $message .= "👤 *Creator:* {$creator}\n";
    
    if (!empty($description)) {
        $message .= "\n📝 *Description:*\n{$description}\n";
    }
    
    $message .= "\nPlease respond to this invitation:";
    
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '✅ Accept', 'callback_data' => "meeting_accept_{$meetingId}"],
                ['text' => '❌ Decline', 'callback_data' => "meeting_decline_{$meetingId}"]
            ]
        ]
    ];
    
    return sendTelegramMessage($chatId, $message, $keyboard);
}

/**
 * Send voting message via Telegram
 */
function sendVotingMessage($chatId, $pollId, $title, $description, $options) {
    $message = "🗳️ *Voting Poll*\n\n";
    $message .= "📋 *Title:* {$title}\n";
    
    if (!empty($description)) {
        $message .= "\n📝 *Description:*\n{$description}\n";
    }
    
    $message .= "\nPlease vote for your preferred option:";
    
    $keyboard = ['inline_keyboard' => []];
    foreach ($options as $index => $option) {
        $keyboard['inline_keyboard'][] = [
            ['text' => $option, 'callback_data' => "vote_{$pollId}_{$index}"]
        ];
    }
    
    return sendTelegramMessage($chatId, $message, $keyboard);
}

/**
 * Send message to Telegram
 */
function sendTelegramMessage($chatId, $message, $keyboard = null) {
    $botToken = 'YOUR_BOT_TOKEN'; // Replace with actual bot token
    
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];
    
    if ($keyboard) {
        $data['reply_markup'] = json_encode($keyboard);
    }
    
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
        $my_profile_id
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
        $my_profile_id
    ]);
}
?> 