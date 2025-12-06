<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($my_profile_id)) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

try {
    $messageType = $_POST['messageType'] ?? '';
    
    if ($messageType === 'meeting') {
        $title = $_POST['meetingTitle'] ?? '';
        $date = $_POST['meetingDate'] ?? '';
        $time = $_POST['meetingTime'] ?? '';
        $creator = $_POST['meetingCreator'] ?? '';
        $description = $_POST['meetingDescription'] ?? '';
        $broadcastType = $_POST['broadcastType'] ?? '';
        $selectedUsers = $_POST['users'] ?? [];

        if (empty($title) || empty($date) || empty($time) || empty($creator) || empty($broadcastType)) {
            throw new Exception('All required fields must be filled');
        }

        // Save meeting draft to database
        $meetingId = saveMeetingDraftToDatabase($title, $date, $time, $creator, $description, $broadcastType, $selectedUsers);

        echo json_encode([
            'success' => true,
            'message' => 'Meeting draft saved successfully',
            'draft_id' => $meetingId
        ]);

    } elseif ($messageType === 'voting') {
        $title = $_POST['votingTitle'] ?? '';
        $description = $_POST['votingDescription'] ?? '';
        $options = $_POST['votingOptions'] ?? [];
        $broadcastType = $_POST['broadcastType'] ?? '';
        $selectedUsers = $_POST['users'] ?? [];

        if (empty($title) || empty($options) || count($options) < 2 || empty($broadcastType)) {
            throw new Exception('Voting title, at least 2 options, and broadcast type are required');
        }

        // Save voting poll draft to database
        $pollId = saveVotingPollDraftToDatabase($title, $description, $broadcastType, $options, $selectedUsers);

        echo json_encode([
            'success' => true,
            'message' => 'Voting poll draft saved successfully',
            'draft_id' => $pollId
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
 * Save meeting draft to database
 */
function saveMeetingDraftToDatabase($title, $date, $time, $creator, $description, $broadcastType, $selectedUsers) {
    global $conn, $my_profile_id;
    
    $stmt = $conn->prepare("INSERT INTO meetings (title, meeting_date, meeting_time, creator_name, description, broadcast_type, created_by, is_draft) VALUES (:title, :date, :time, :creator, :description, :broadcast_type, :created_by, TRUE)");
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
 * Save voting poll draft to database
 */
function saveVotingPollDraftToDatabase($title, $description, $broadcastType, $options, $selectedUsers) {
    global $conn, $my_profile_id;
    
    // Start transaction
    $conn->beginTransaction();
    
    try {
        // Insert voting poll draft
        $stmt = $conn->prepare("INSERT INTO voting_polls (title, description, broadcast_type, created_by, is_draft) VALUES (:title, :description, :broadcast_type, :created_by, TRUE)");
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
?> 