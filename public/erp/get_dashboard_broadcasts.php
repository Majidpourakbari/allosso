<?php
// Start session first
if (session_id() == '') {
    session_start();
}

// Include functions with correct path
require_once __DIR__ . '/controlls/db/functions.php';

header('Content-Type: application/json');

// Debug: Check what variables are available
error_log("Debug: my_profile_id = " . (isset($my_profile_id) ? $my_profile_id : 'NOT SET'));
error_log("Debug: my_profile_chat_id = " . (isset($my_profile_chat_id) ? $my_profile_chat_id : 'NOT SET'));
error_log("Debug: Session user_login = " . (isset($_SESSION['user_login']) ? 'SET' : 'NOT SET'));

// Check if user is logged in and variables are available
if (!isset($my_profile_id) || !isset($my_profile_chat_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in or session expired',
        'debug' => [
            'my_profile_id_set' => isset($my_profile_id),
            'my_profile_chat_id_set' => isset($my_profile_chat_id),
            'session_user_login_set' => isset($_SESSION['user_login'])
        ]
    ]);
    exit;
}

try {
    // Get current user's chat_id for filtering
    $user_chat_id = $my_profile_chat_id;
    
    error_log("Debug: Using chat_id = " . $user_chat_id);
    
    // Debug: Check if tables exist
    $debug_stmt = $conn->prepare("SHOW TABLES LIKE 'voting_polls'");
    $debug_stmt->execute();
    $table_exists = $debug_stmt->fetch();
    
    if (!$table_exists) {
        throw new Exception('Voting polls table does not exist');
    }
    
    // Debug: Check voting polls count
    $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM voting_polls");
    $count_stmt->execute();
    $poll_count = $count_stmt->fetch(PDO::FETCH_OBJ)->count;
    
    error_log("Debug: Found {$poll_count} voting polls in database");
    
    // Get latest 5 meetings where user hasn't responded yet
    $meetings_stmt = $conn->prepare("
        SELECT 
            m.id,
            m.title,
            m.meeting_date,
            m.meeting_time,
            m.creator_name,
            m.description,
            m.broadcast_type,
            m.created_at,
            COUNT(mr.id) as response_count,
            SUM(CASE WHEN mr.response = 'accept' THEN 1 ELSE 0 END) as accept_count,
            SUM(CASE WHEN mr.response = 'decline' THEN 1 ELSE 0 END) as decline_count
        FROM meetings m
        LEFT JOIN meeting_responses mr ON m.id = mr.meeting_id
        WHERE m.id NOT IN (
            SELECT DISTINCT meeting_id 
            FROM meeting_responses 
            WHERE chat_id = ?
        )
        GROUP BY m.id
        ORDER BY m.created_at DESC
        LIMIT 5
    ");
    $meetings_stmt->execute([$user_chat_id]);
    $meetings = $meetings_stmt->fetchAll(PDO::FETCH_OBJ);

    // Get latest 5 voting polls where user hasn't voted yet
    $voting_stmt = $conn->prepare("
        SELECT 
            vp.id,
            vp.title,
            vp.description,
            vp.broadcast_type,
            vp.created_at,
            COUNT(DISTINCT vo.id) as option_count,
            COUNT(vr.id) as vote_count
        FROM voting_polls vp
        LEFT JOIN voting_options vo ON vp.id = vo.poll_id
        LEFT JOIN voting_responses vr ON vo.id = vr.option_id
        WHERE vp.id NOT IN (
            SELECT DISTINCT vo2.poll_id 
            FROM voting_options vo2
            INNER JOIN voting_responses vr2 ON vo2.id = vr2.option_id
            WHERE vr2.chat_id = ?
        )
        GROUP BY vp.id
        ORDER BY vp.created_at DESC
        LIMIT 5
    ");
    $voting_stmt->execute([$user_chat_id]);
    $voting_polls = $voting_stmt->fetchAll(PDO::FETCH_OBJ);
    
    error_log("Debug: Retrieved " . count($voting_polls) . " voting polls from database");

    // Format the data for display
    $formatted_meetings = [];
    foreach ($meetings as $meeting) {
        $formatted_meetings[] = [
            'id' => $meeting->id,
            'title' => $meeting->title,
            'date' => $meeting->meeting_date,
            'time' => $meeting->meeting_time,
            'creator' => $meeting->creator_name,
            'description' => $meeting->description,
            'broadcast_type' => $meeting->broadcast_type,
            'created_at' => $meeting->created_at,
            'response_count' => (int)$meeting->response_count,
            'accept_count' => (int)$meeting->accept_count,
            'decline_count' => (int)$meeting->decline_count,
            'type' => 'meeting'
        ];
    }

    $formatted_voting = [];
    foreach ($voting_polls as $poll) {
        // Get voting options for this poll
        $options_stmt = $conn->prepare("
            SELECT 
                vo.id,
                vo.option_text,
                vo.option_order,
                COUNT(vr.id) as vote_count
            FROM voting_options vo
            LEFT JOIN voting_responses vr ON vo.id = vr.option_id
            WHERE vo.poll_id = ?
            GROUP BY vo.id
            ORDER BY vo.option_order
        ");
        $options_stmt->execute([$poll->id]);
        $options = $options_stmt->fetchAll(PDO::FETCH_OBJ);
        
        error_log("Debug: Poll ID {$poll->id} has " . count($options) . " options");

        $formatted_options = [];
        foreach ($options as $option) {
            $formatted_options[] = [
                'id' => $option->id,
                'text' => $option->option_text,
                'order' => $option->option_order,
                'vote_count' => (int)$option->vote_count
            ];
        }

        $formatted_voting[] = [
            'id' => $poll->id,
            'title' => $poll->title,
            'description' => $poll->description,
            'broadcast_type' => $poll->broadcast_type,
            'created_at' => $poll->created_at,
            'option_count' => (int)$poll->option_count,
            'vote_count' => (int)$poll->vote_count,
            'options' => $formatted_options,
            'type' => 'voting'
        ];
    }

    echo json_encode([
        'success' => true,
        'meetings' => $formatted_meetings,
        'voting_polls' => $formatted_voting
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 