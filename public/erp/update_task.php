<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $taskId = $_POST['task_id'] ?? null;
    if (!$taskId) {
        throw new Exception('Task ID is required');
    }

    // Prepare the update fields
    $updateFields = [
        'title' => $_POST['title'] ?? null,
        'description' => $_POST['description'] ?? null,
        'category' => $_POST['category'] ?? null,
        'allo_section' => $_POST['allo_section'] ?? null,
        'label' => $_POST['label'] ?? null,
        'priority' => $_POST['priority'] ?? null,
        'status' => $_POST['status'] ?? null,
        'objective' => $_POST['objective'] ?? null,
        'progress' => $_POST['progress'] ?? null,
        'date_start' => $_POST['date_start'] ?? null,
        'time_start' => $_POST['time_start'] ?? null,
        'date_finish' => $_POST['date_finish'] ?? null,
        'time_finish' => $_POST['time_finish'] ?? null,
        'risks' => $_POST['risks'] ?? null,
        'required_tools' => $_POST['required_tools'] ?? null,
        'budget' => $_POST['budget'] ?? null
    ];

    // Build the SQL update statement
    $sql = "UPDATE tasks SET ";
    $params = [];
    $updates = [];

    foreach ($updateFields as $field => $value) {
        // Only update fields that have a value (not null and not empty string)
        // Exception: status and progress can be "0" which is a valid value
        if ($value !== null && ($value !== '' || in_array($field, ['status', 'progress']))) {
            $updates[] = "$field = ?";
            $params[] = $value;
        }
    }

    if (empty($updates)) {
        throw new Exception('No fields to update');
    }

    $sql .= implode(', ', $updates);
    $sql .= " WHERE id = ?";
    $params[] = $taskId;

    // Execute the update
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        // If status was updated, create notification
        if (isset($updateFields['status'])) {
            // Get task users for notification
            $task_users_stmt = $conn->prepare("SELECT user_id FROM task_users WHERE task_id = ?");
            $task_users_stmt->execute([$taskId]);
            $task_users = $task_users_stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Add creator to the list if not already included
            if (!in_array($my_profile_id, $task_users)) {
                $task_users[] = $my_profile_id;
            }

            // Get task name and user name for notification
            $task_info_stmt = $conn->prepare("SELECT t.title, u.name as user_name FROM tasks t JOIN users u ON u.id = ? WHERE t.id = ?");
            $task_info_stmt->execute([$my_profile_id, $taskId]);
            $task_info = $task_info_stmt->fetch(PDO::FETCH_OBJ);

            // Map status to text
            $statusText = match($updateFields['status']) {
                '0' => 'To Do',
                '1' => 'In Progress',
                '2' => 'Review',
                '3' => 'Done',
                '4' => 'Archived',
                '5' => 'To Debug',
                default => 'Unknown'
            };

            // Create notification for status change
            $notification_message = "{$task_info->user_name} changed the status of task #{$taskId} - {$task_info->title} to: {$statusText}";
            $receiver_ids = implode(',', $task_users);
            $notification_stmt = $conn->prepare("INSERT INTO notifications (user_id, receiver_ids, message, date, time, users_read) VALUES (?, ?, ?, ?, ?, ?)");
            $notification_stmt->execute([
                $my_profile_id, 
                $receiver_ids, 
                $notification_message, 
                date('Y-m-d'), 
                date('H:i:s'), 
                $my_profile_id
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made to the task']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 