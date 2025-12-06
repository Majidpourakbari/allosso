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
$meeting_id = $_POST['meeting_id'] ?? null;
$response = $_POST['response'] ?? null; // 'accept' or 'decline'
$user_id = $my_profile_id;

// Validate input
if (!$meeting_id || !$response || !in_array($response, ['accept', 'decline'])) {
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
    
    // Check if meeting exists
    $stmt = $conn->prepare("SELECT id, title FROM meetings WHERE id = :meeting_id");
    $stmt->execute(['meeting_id' => $meeting_id]);
    $meeting = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$meeting) {
        echo json_encode(['success' => false, 'message' => 'Meeting not found']);
        exit;
    }
    
    // Store the response in database
    $stmt = $conn->prepare("INSERT INTO meeting_responses (meeting_id, chat_id, user_name, response) VALUES (:meeting_id, :chat_id, :user_name, :response) ON DUPLICATE KEY UPDATE response = :response, created_at = NOW()");
    $stmt->execute([
        'meeting_id' => $meeting_id,
        'chat_id' => $chat_id,
        'user_name' => $user_name,
        'response' => $response
    ]);
    
    // Get updated response counts
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_responses,
            SUM(CASE WHEN response = 'accept' THEN 1 ELSE 0 END) as accept_count,
            SUM(CASE WHEN response = 'decline' THEN 1 ELSE 0 END) as decline_count
        FROM meeting_responses 
        WHERE meeting_id = :meeting_id
    ");
    $stmt->execute(['meeting_id' => $meeting_id]);
    $counts = $stmt->fetch(PDO::FETCH_OBJ);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Response saved successfully',
        'data' => [
            'meeting_id' => $meeting_id,
            'response' => $response,
            'user_name' => $user_name,
            'total_responses' => $counts->total_responses,
            'accept_count' => $counts->accept_count,
            'decline_count' => $counts->decline_count
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 