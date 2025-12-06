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
    $user_id = $my_profile_id;
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['my_profile_id'])) {
        throw new Exception('my_profile_id is required');
    }
    
    $my_profile_id = $data['my_profile_id'];
    
    if (isset($data['notification_id'])) {
        // Mark single notification as read
        $stmt = $conn->prepare("
            UPDATE notifications 
            SET users_read = CASE 
                WHEN users_read IS NULL OR users_read = '' THEN :my_profile_id
                WHEN FIND_IN_SET(:my_profile_id, users_read) = 0 THEN CONCAT(users_read, ',', :my_profile_id)
                ELSE users_read
            END
            WHERE id = :notification_id
        ");
        
        $result = $stmt->execute([
            'notification_id' => $data['notification_id'],
            'my_profile_id' => $my_profile_id
        ]);
        
        $message = 'Notification marked as read';
    } elseif (isset($data['task_id'])) {
        // Mark notifications as read for a specific task
        $stmt = $conn->prepare("
            UPDATE notifications 
            SET users_read = CASE 
                WHEN users_read IS NULL OR users_read = '' THEN :my_profile_id
                WHEN FIND_IN_SET(:my_profile_id, users_read) = 0 THEN CONCAT(users_read, ',', :my_profile_id)
                ELSE users_read
            END
            WHERE receiver_ids = :my_profile_id 
            AND message LIKE CONCAT('%added to task #', :task_id, '%')
            AND (users_read IS NULL 
                 OR users_read = '' 
                 OR FIND_IN_SET(:my_profile_id, users_read) = 0)
        ");
        
        $result = $stmt->execute([
            'task_id' => $data['task_id'],
            'my_profile_id' => $my_profile_id
        ]);
        
        $message = 'Task notifications marked as read';
    } else {
        // Mark all notifications as read
        $stmt = $conn->prepare("
            UPDATE notifications 
            SET users_read = CASE 
                WHEN users_read IS NULL OR users_read = '' THEN :my_profile_id
                WHEN FIND_IN_SET(:my_profile_id, users_read) = 0 THEN CONCAT(users_read, ',', :my_profile_id)
                ELSE users_read
            END
        ");
        
        $result = $stmt->execute([
            'my_profile_id' => $my_profile_id
        ]);
        $message = 'All notifications marked as read';
    }
    
    if (!$result) {
        throw new Exception('Failed to update notifications');
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 