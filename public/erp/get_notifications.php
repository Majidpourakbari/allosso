<?php
session_start();
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

if (!isset($my_profile_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

try {
    $user_id = $my_profile_id;
    
    // Get last 5 notifications for the current user
    $stmt = $conn->prepare("
        SELECT n.*, u.name as user_name, u.avatar 
        FROM notifications n 
        LEFT JOIN users u ON n.user_id = u.id 
        WHERE n.receiver_ids LIKE :user_id_pattern
        OR n.receiver_ids IS NULL
        ORDER BY n.date DESC, n.time DESC 
        LIMIT 5
    ");
    
    $stmt->execute(['user_id_pattern' => "%{$user_id}%"]);
    $notifications = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Get total unread notifications count for current user
    $count_stmt = $conn->prepare("
        SELECT COUNT(*) as unread_count 
        FROM notifications 
        WHERE (
            users_read IS NULL 
            OR users_read = '' 
            OR NOT FIND_IN_SET(:my_profile_id, users_read)
        )
        AND (
            receiver_ids LIKE :user_id_pattern
            OR receiver_ids IS NULL
        )
    ");
    
    $count_stmt->execute([
        'my_profile_id' => $my_profile_id,
        'user_id_pattern' => "%{$user_id}%"
    ]);
    $unread_count = $count_stmt->fetch(PDO::FETCH_OBJ)->unread_count;
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unread_count
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 