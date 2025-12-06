<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['task_id']) || !isset($_FILES['file'])) {
        throw new Exception('Missing required fields');
    }

    $task_id = $_POST['task_id'];
    $user_id = $my_profile_id; // Use the logged-in user's ID
    $date = $_POST['date'] ?? date('Y-m-d');
    $time = $_POST['time'] ?? date('H:i:s');

    // Handle file upload
    $upload_dir = 'uploads/tasks/files/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file = $_FILES['file'];
    $original_name = $file['name'];
    $file_type = $file['type'];
    $file_size = $file['size'];
    $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
    $file_name = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $file_name;

    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Failed to upload file');
    }

    // Insert file record into database
    $stmt = $conn->prepare("INSERT INTO task_files (task_id, user_id, file_name, original_name, file_type, file_size, date, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$task_id, $user_id, $file_name, $original_name, $file_type, $file_size, $date, $time]);

    // Get task users for notification
    $task_users_stmt = $conn->prepare("SELECT user_id FROM task_users WHERE task_id = ?");
    $task_users_stmt->execute([$task_id]);
    $task_users = $task_users_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Add creator to the list if not already included
    if (!in_array($user_id, $task_users)) {
        $task_users[] = $user_id;
    }

    // Get task name and user name for notification
    $task_info_stmt = $conn->prepare("SELECT t.title, u.name as user_name FROM tasks t JOIN users u ON u.id = ? WHERE t.id = ?");
    $task_info_stmt->execute([$user_id, $task_id]);
    $task_info = $task_info_stmt->fetch(PDO::FETCH_OBJ);

    // Create notification for new file
    $notification_message = "{$task_info->user_name} uploaded a file to task #{$task_id} - {$task_info->title}: {$original_name}";
    $receiver_ids = implode(',', $task_users);
    $notification_stmt = $conn->prepare("INSERT INTO notifications (user_id, receiver_ids, message, date, time, users_read) VALUES (?, ?, ?, ?, ?, ?)");
    $notification_stmt->execute([$user_id, $receiver_ids, $notification_message, $date, $time, $user_id]);

    // Get the user's information for the response
    $userStmt = $conn->prepare("SELECT id, name, avatar FROM users WHERE id = ?");
    $userStmt->execute([$user_id]);
    $user = $userStmt->fetch(PDO::FETCH_OBJ);

    echo json_encode([
        'success' => true,
        'file' => [
            'id' => $conn->lastInsertId(),
            'file_name' => $file_name,
            'original_name' => $original_name,
            'file_type' => $file_type,
            'file_size' => $file_size,
            'date' => $date,
            'time' => $time,
            'user' => $user
        ]
    ]);
} catch (Exception $e) {
    error_log("Error in add_task_file.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 