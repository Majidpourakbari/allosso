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
    // Get unread message counts for each sender
    $stmt = $conn->prepare("
        SELECT 
            sender_id,
            COUNT(*) as unread_count,
            u.name as sender_name,
            u.avatar as sender_avatar
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE receiver_id = :receiver_id 
        AND is_read = FALSE
        GROUP BY sender_id
    ");
    
    $stmt->execute([
        'receiver_id' => $my_profile_id
    ]);
    
    $unread_counts = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Format the response
    $formatted_counts = array_map(function($count) {
        return [
            'sender_id' => $count->sender_id,
            'unread_count' => (int)$count->unread_count,
            'sender_name' => $count->sender_name,
            'sender_avatar' => $count->sender_avatar
        ];
    }, $unread_counts);
    
    echo json_encode([
        'success' => true,
        'unread_counts' => $formatted_counts
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 