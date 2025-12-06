<?php
require_once 'controlls/db/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
session_start();
if (!isset($my_profile_id)) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$poll_id = $_POST['poll_id'] ?? null;
$option_id = $_POST['option_id'] ?? null;
$user_id = $my_profile_id;

// Validate input
if (!$poll_id || !$option_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Get user information
    $stmt = $conn->prepare("SELECT name, chat_id FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    $user_name = $user->name;
    $chat_id = $user->chat_id;
    
    // Check if poll and option exist
    $stmt = $conn->prepare("
        SELECT vp.id as poll_id, vp.title as poll_title, vo.id as option_id, vo.option_text 
        FROM voting_polls vp 
        JOIN voting_options vo ON vp.id = vo.poll_id 
        WHERE vp.id = :poll_id AND vo.id = :option_id
    ");
    $stmt->execute(['poll_id' => $poll_id, 'option_id' => $option_id]);
    $poll_data = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$poll_data) {
        echo json_encode(['success' => false, 'message' => 'Poll or option not found']);
        exit;
    }
    
    // Store the vote in database
    $stmt = $conn->prepare("INSERT INTO voting_responses (poll_id, option_id, chat_id, user_name) VALUES (:poll_id, :option_id, :chat_id, :user_name) ON DUPLICATE KEY UPDATE option_id = :option_id, created_at = NOW()");
    $stmt->execute([
        'poll_id' => $poll_id,
        'option_id' => $option_id,
        'chat_id' => $chat_id,
        'user_name' => $user_name
    ]);
    
    // Get updated vote counts for all options
    $stmt = $conn->prepare("
        SELECT 
            vo.id as option_id,
            vo.option_text,
            COUNT(vr.id) as vote_count
        FROM voting_options vo 
        LEFT JOIN voting_responses vr ON vo.id = vr.option_id 
        WHERE vo.poll_id = :poll_id 
        GROUP BY vo.id, vo.option_text 
        ORDER BY vo.option_order
    ");
    $stmt->execute(['poll_id' => $poll_id]);
    $vote_counts = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Get total votes
    $stmt = $conn->prepare("SELECT COUNT(*) as total_votes FROM voting_responses WHERE poll_id = :poll_id");
    $stmt->execute(['poll_id' => $poll_id]);
    $total_votes = $stmt->fetch(PDO::FETCH_OBJ)->total_votes;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Vote saved successfully',
        'data' => [
            'poll_id' => $poll_id,
            'poll_title' => $poll_data->poll_title,
            'option_id' => $option_id,
            'option_text' => $poll_data->option_text,
            'user_name' => $user_name,
            'total_votes' => $total_votes,
            'vote_counts' => $vote_counts
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 