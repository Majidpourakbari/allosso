<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['priority']) || !isset($_POST['category']) || !isset($_POST['allo_section'])) {
        throw new Exception('Missing required fields');
    }

    $title = $_POST['title'];
    $group_id = $_POST['group_id'] ?? 1; // Default to group 1 if not provided
    $task_phase = $_POST['task_phase'] ?? 'Planning'; // Default phase if not provided
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $category = $_POST['category'];
    $allo_section = $_POST['allo_section'];
    $label = $_POST['label'] ?? null;
    $date_create = $_POST['date_create'];
    $time_create = $_POST['time_create'];
    $status = $_POST['status'];
    $user_creator = $_POST['user_creator'];

    // Insert task into database
    $stmt = $conn->prepare("INSERT INTO tasks (title, group_id, task_phase, description, priority, category, allo_section, label, date_create, time_create, status, user_creator) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $group_id, $task_phase, $description, $priority, $category, $allo_section, $label, $date_create, $time_create, $status, $user_creator]);

    // Get the newly created task ID
    $task_id = $conn->lastInsertId();

    // Get all users assigned to this task (including creator)
    $users_stmt = $conn->prepare("SELECT user_id FROM task_users WHERE task_id = ?");
    $users_stmt->execute([$task_id]);
    $task_users = $users_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Add creator to the list if not already included
    if (!in_array($user_creator, $task_users)) {
        $task_users[] = $user_creator;
    }

    // Get creator's name for notification
    $creator_stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $creator_stmt->execute([$user_creator]);
    $creator_name = $creator_stmt->fetchColumn();

    // Create notification for new task
    $notification_message = "{$creator_name} created new task #{$task_id} - {$title}";
    $receiver_ids = implode(',', $task_users);
    $notification_stmt = $conn->prepare("INSERT INTO notifications (user_id, receiver_ids, message, date, time, users_read) VALUES (?, ?, ?, ?, ?, ?)");
    $notification_stmt->execute([$user_creator, $receiver_ids, $notification_message, $date_create, $time_create, $user_creator]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 