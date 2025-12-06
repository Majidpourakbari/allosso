<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['task_id']) || !isset($_POST['user_id'])) {
        throw new Exception('Missing required fields');
    }

    $task_id = $_POST['task_id'];
    $user_id = $_POST['user_id'];
    $status = 1; // Default status value

    // Log the received data
    error_log("Adding user to task - Task ID: $task_id, User ID: $user_id");

    // Check if the user is the task creator
    $creatorStmt = $conn->prepare("SELECT user_creator FROM tasks WHERE id = ?");
    $creatorStmt->execute([$task_id]);
    $taskCreator = $creatorStmt->fetchColumn();
    
    if ($user_id == $taskCreator) {
        throw new Exception('Cannot add task creator to the task');
    }

    // Check if the user is already assigned to the task
    $checkStmt = $conn->prepare("SELECT id FROM task_users WHERE task_id = ? AND user_id = ?");
    $checkStmt->execute([$task_id, $user_id]);
    
    if ($checkStmt->rowCount() > 0) {
        throw new Exception('User is already assigned to this task');
    }

    // Insert user into task_users table with status
    $stmt = $conn->prepare("INSERT INTO task_users (task_id, user_id, status) VALUES (?, ?, ?)");
    $result = $stmt->execute([$task_id, $user_id, $status]);

    if (!$result) {
        error_log("Database error: " . print_r($stmt->errorInfo(), true));
        throw new Exception('Failed to add user to task');
    }

    // Get the user's information for the response
    $userStmt = $conn->prepare("SELECT id, name, avatar FROM users WHERE id = ?");
    $userStmt->execute([$user_id]);
    $user = $userStmt->fetch(PDO::FETCH_OBJ);

    if (!$user) {
        error_log("User not found - User ID: $user_id");
        throw new Exception('User not found');
    }

    // Get task information for notification
    $taskStmt = $conn->prepare("SELECT t.title, u.name as creator_name FROM tasks t JOIN users u ON t.user_creator = u.id WHERE t.id = ?");
    $taskStmt->execute([$task_id]);
    $taskInfo = $taskStmt->fetch(PDO::FETCH_OBJ);

    if ($taskInfo) {
        // Create notification for the added user
        $notification_message = "You have been added to task #{$task_id} - {$taskInfo->title} by {$taskInfo->creator_name}";
        $notification_stmt = $conn->prepare("INSERT INTO notifications (user_id, receiver_ids, message, date, time, users_read) VALUES (?, ?, ?, ?, ?, ?)");
        $notification_stmt->execute([
            $my_profile_id, // sender
            $user_id, // receiver
            $notification_message,
            date('Y-m-d'),
            date('H:i:s'),
            $my_profile_id // mark as read by sender
        ]);
    }

    echo json_encode([
        'success' => true,
        'user' => $user
    ]);
} catch (Exception $e) {
    error_log("Error in add_task_user.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 