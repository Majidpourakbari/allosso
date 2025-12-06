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
    // Get all users except current user
    $stmt = $conn->prepare("
        SELECT id, name, avatar, online_status  
        FROM users 
        WHERE id != :current_user_id 
        ORDER BY online_status DESC, name ASC
    ");
    
    $stmt->execute([
        'current_user_id' => $my_profile_id
    ]);
    
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Format the response
    $formatted_users = array_map(function($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ?: 'default-avatar.png',
            'is_online' => (bool)$user->online_status,
            'last_seen' => $user->last_seen
        ];
    }, $users);
    
    echo json_encode([
        'success' => true,
        'users' => $formatted_users
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 