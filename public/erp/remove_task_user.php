<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['task_id']) || !isset($_POST['user_id'])) {
        throw new Exception('Missing required fields');
    }

    $task_id = $_POST['task_id'];
    $user_id = $_POST['user_id'];

    // Log the received data
    error_log("Removing user from task - Task ID: $task_id, User ID: $user_id");

    // Check if the user is assigned to the task
    $checkStmt = $conn->prepare("SELECT id FROM task_users WHERE task_id = ? AND user_id = ?");
    $checkStmt->execute([$task_id, $user_id]);
    
    if ($checkStmt->rowCount() === 0) {
        error_log("User not found in task_users - Task ID: $task_id, User ID: $user_id");
        throw new Exception('User is not assigned to this task');
    }

    // Delete user from task_users table
    $stmt = $conn->prepare("DELETE FROM task_users WHERE task_id = ? AND user_id = ?");
    $result = $stmt->execute([$task_id, $user_id]);

    if (!$result) {
        error_log("Database error in remove_task_user.php: " . print_r($stmt->errorInfo(), true));
        throw new Exception('Failed to remove user from task');
    }

    if ($stmt->rowCount() === 0) {
        error_log("No rows affected when trying to remove user - Task ID: $task_id, User ID: $user_id");
        throw new Exception('User is not assigned to this task');
    }

    error_log("Successfully removed user from task - Task ID: $task_id, User ID: $user_id");
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Error in remove_task_user.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 