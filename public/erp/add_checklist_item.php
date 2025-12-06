<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $taskId = $_POST['task_id'] ?? null;
    $content = $_POST['content'] ?? null;
    $date = $_POST['date'] ?? date('Y-m-d');
    $time = $_POST['time'] ?? date('H:i:s');
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;
    $status = $_POST['status'] ?? '0';
    $parentId = $_POST['parent_id'] ?? null;
    $priority = $_POST['priority'] ?? '2'; // Default to Medium priority
    $archiveStatus = $_POST['archive_status'] ?? '0'; // Default to Active
    $label = $_POST['label'] ?? null; // Default to no label

    // Convert empty string to null for parent_id and label
    if ($parentId === '') {
        $parentId = null;
    }
    if ($label === '') {
        $label = null;
    }

    if (!$taskId || !$content) {
        throw new Exception('Task ID and content are required');
    }

    // Validate parent_id if provided
    if ($parentId) {
        $parentCheck = $conn->prepare("SELECT id FROM tasks_checklists WHERE id = ? AND task_id = ?");
        $parentCheck->execute([$parentId, $taskId]);
        if (!$parentCheck->fetch()) {
            throw new Exception('Invalid parent checklist selected');
        }
    }

    $filePath = null;
    $audioPath = null;
    
    // Handle file upload if present
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/checklists/files/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $fileName = uniqid() . '_' . basename($_FILES['file']['name']);
        $targetPath = $uploadDir . $fileName;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            $filePath = $fileName; // Save only the filename
        } else {
            throw new Exception('Failed to upload file');
        }
    }

    // Handle audio upload if present
    if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/checklists/audio/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $fileName = uniqid() . '.wav';
        $targetPath = $uploadDir . $fileName;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['audio']['tmp_name'], $targetPath)) {
            $audioPath = $fileName; // Save only the filename
        } else {
            throw new Exception('Failed to upload audio file');
        }
    } elseif (isset($_POST['recorded_audio']) && !empty($_POST['recorded_audio'])) {
        $uploadDir = 'uploads/checklists/audio/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $fileName = uniqid() . '.wav';
        $targetPath = $uploadDir . $fileName;
        
        // Save the recorded audio blob
        if (file_put_contents($targetPath, $_POST['recorded_audio'])) {
            $audioPath = $fileName; // Save only the filename
        } else {
            throw new Exception('Failed to save recorded audio');
        }
    }

    // Insert the new checklist item with priority, archive_status and label
    $stmt = $conn->prepare("INSERT INTO tasks_checklists (
        task_id, parent_id, content, date, time, user_id, status, priority, archive_status, label,
        start_date, end_date, file_path, audio_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $taskId,
        $parentId,
        $content, 
        $date, 
        $time, 
        $my_profile_id, 
        $status,
        $priority,
        $archiveStatus,
        $label,
        $startDate,
        $endDate,
        $filePath,
        $audioPath
    ]);

    if ($stmt->rowCount() > 0) {
        // Get task users for notification
        $task_users_stmt = $conn->prepare("SELECT DISTINCT user_id FROM task_users WHERE task_id = ?");
        $task_users_stmt->execute([$taskId]);
        $task_users = $task_users_stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get task creator
        $task_creator_stmt = $conn->prepare("SELECT user_creator FROM tasks WHERE id = ?");
        $task_creator_stmt->execute([$taskId]);
        $task_creator = $task_creator_stmt->fetch(PDO::FETCH_COLUMN);
        
        // Add creator to the list if not already included
        if ($task_creator && !in_array($task_creator, $task_users)) {
            $task_users[] = $task_creator;
        }

        // Get task name and user name for notification
        $task_info_stmt = $conn->prepare("SELECT t.title, u.name as user_name FROM tasks t JOIN users u ON u.id = ? WHERE t.id = ?");
        $task_info_stmt->execute([$my_profile_id, $taskId]);
        $task_info = $task_info_stmt->fetch(PDO::FETCH_OBJ);

        if ($task_info) {
            // Create notification for new checklist item
            $notification_message = "{$task_info->user_name} added a checklist item to task #{$taskId} - {$task_info->title}: {$content}";
            $receiver_ids = implode(',', $task_users);
            $notification_stmt = $conn->prepare("INSERT INTO notifications (user_id, receiver_ids, message, date, time, users_read) VALUES (?, ?, ?, ?, ?, ?)");
            $notification_stmt->execute([$my_profile_id, $receiver_ids, $notification_message, $date, $time, $my_profile_id]);
        }

        echo json_encode(['success' => true, 'message' => 'Checklist item added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add checklist item']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 