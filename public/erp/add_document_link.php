<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['task_id']) || !isset($_POST['title']) || !isset($_POST['url'])) {
        throw new Exception('Missing required fields');
    }

    $task_id = $_POST['task_id'];
    $title = $_POST['title'];
    $url = $_POST['url'];
    $description = $_POST['description'] ?? null;
    $date = $_POST['date'];
    $time = $_POST['time'];
    $user_id = $my_profile_id; // Use the logged-in user's ID

    // Insert document link into database
    $stmt = $conn->prepare("INSERT INTO task_document_links (task_id, user_id, title, url, description, date, time) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$task_id, $user_id, $title, $url, $description, $date, $time]);

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

    // Create notification for new document link
    $notification_message = "{$task_info->user_name} added a document link to task #{$task_id} - {$task_info->title}: {$title}";
    $receiver_ids = implode(',', $task_users);
    $notification_stmt = $conn->prepare("INSERT INTO notifications (user_id, receiver_ids, message, date, time, users_read) VALUES (?, ?, ?, ?, ?, ?)");
    $notification_stmt->execute([$user_id, $receiver_ids, $notification_message, $date, $time, $user_id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 