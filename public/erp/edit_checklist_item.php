<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $checklistId = $_POST['checklist_id'] ?? null;
    $content = $_POST['content'] ?? null;
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;
    $parentId = $_POST['parent_id'] ?? null;
    $label = $_POST['label'] ?? null;

    // Convert empty string to null for parent_id and label
    if ($parentId === '') {
        $parentId = null;
    }
    if ($label === '') {
        $label = null;
    }

    if (!$checklistId || !$content) {
        throw new Exception('Checklist ID and content are required');
    }

    // Get the current checklist item to check for existing files and task_id
    $stmt = $conn->prepare("SELECT file_path, audio_path, task_id FROM tasks_checklists WHERE id = ?");
    $stmt->execute([$checklistId]);
    $currentChecklist = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentChecklist) {
        throw new Exception('Checklist item not found');
    }

    $taskId = $currentChecklist['task_id'];
    $filePath = $currentChecklist['file_path'];
    $audioPath = $currentChecklist['audio_path'];

    // Validate parent_id if provided (prevent circular references)
    if ($parentId) {
        // Check if parent exists and belongs to the same task
        $parentCheck = $conn->prepare("SELECT id FROM tasks_checklists WHERE id = ? AND task_id = ?");
        $parentCheck->execute([$parentId, $taskId]);
        if (!$parentCheck->fetch()) {
            throw new Exception('Invalid parent checklist selected');
        }
        
        // Prevent setting itself as parent
        if ($parentId == $checklistId) {
            throw new Exception('Cannot set checklist as its own parent');
        }
        
        // Check for circular references (prevent setting a child as parent)
        $circularCheck = $conn->prepare("
            WITH RECURSIVE checklist_tree AS (
                SELECT id, parent_id FROM tasks_checklists WHERE id = ?
                UNION ALL
                SELECT tc.id, tc.parent_id 
                FROM tasks_checklists tc 
                INNER JOIN checklist_tree ct ON tc.id = ct.parent_id
            )
            SELECT COUNT(*) as count FROM checklist_tree WHERE id = ?
        ");
        $circularCheck->execute([$parentId, $checklistId]);
        $circularResult = $circularCheck->fetch(PDO::FETCH_ASSOC);
        if ($circularResult['count'] > 0) {
            throw new Exception('Cannot set parent: would create circular reference');
        }
    }

    // Handle file removal if requested
    if (isset($_POST['remove_file']) && $_POST['remove_file'] === '1') {
        if ($filePath) {
            $fullFilePath = 'uploads/checklists/files/' . $filePath;
            if (file_exists($fullFilePath)) {
                unlink($fullFilePath);
            }
            $filePath = null;
        }
    }
    // Handle new file upload if present
    elseif (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/checklists/files/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception('Failed to create file upload directory');
            }
        }
        
        // Delete old file if exists
        if ($filePath) {
            $oldFilePath = $uploadDir . $filePath;
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }
        
        // Generate unique filename
        $fileName = uniqid() . '_' . basename($_FILES['file']['name']);
        $targetPath = $uploadDir . $fileName;
        
        // Move uploaded file
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            throw new Exception('Failed to upload file: ' . error_get_last()['message']);
        }
        $filePath = $fileName;
    }

    // Handle audio removal if requested
    if (isset($_POST['remove_audio']) && $_POST['remove_audio'] === '1') {
        if ($audioPath) {
            $fullAudioPath = 'uploads/checklists/audio/' . $audioPath;
            if (file_exists($fullAudioPath)) {
                unlink($fullAudioPath);
            }
            $audioPath = null;
        }
    }
    // Handle new audio upload if present
    elseif (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/checklists/audio/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception('Failed to create audio upload directory');
            }
        }
        
        // Delete old audio if exists
        if ($audioPath) {
            $oldAudioPath = $uploadDir . $audioPath;
            if (file_exists($oldAudioPath)) {
                unlink($oldAudioPath);
            }
        }
        
        // Generate unique filename
        $fileName = uniqid() . '.wav';
        $targetPath = $uploadDir . $fileName;
        
        // Move uploaded file
        if (!move_uploaded_file($_FILES['audio']['tmp_name'], $targetPath)) {
            throw new Exception('Failed to upload audio file: ' . error_get_last()['message']);
        }
        $audioPath = $fileName;
    } elseif (isset($_POST['recorded_audio']) && !empty($_POST['recorded_audio'])) {
        $uploadDir = 'uploads/checklists/audio/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception('Failed to create audio upload directory');
            }
        }
        
        // Delete old audio if exists
        if ($audioPath) {
            $oldAudioPath = $uploadDir . $audioPath;
            if (file_exists($oldAudioPath)) {
                unlink($oldAudioPath);
            }
        }
        
        // Generate unique filename
        $fileName = uniqid() . '.wav';
        $targetPath = $uploadDir . $fileName;
        
        // Save the recorded audio blob
        if (!file_put_contents($targetPath, $_POST['recorded_audio'])) {
            throw new Exception('Failed to save recorded audio: ' . error_get_last()['message']);
        }
        $audioPath = $fileName;
    }

    // Update the checklist item with parent_id and label
    $stmt = $conn->prepare("UPDATE tasks_checklists 
                           SET content = ?, 
                               parent_id = ?,
                               start_date = ?, 
                               end_date = ?,
                               file_path = ?,
                               audio_path = ?,
                               label = ?
                           WHERE id = ?");
    
    if (!$stmt->execute([
        $content,
        $parentId,
        $startDate,
        $endDate,
        $filePath,
        $audioPath,
        $label,
        $checklistId
    ])) {
        throw new Exception('Database update failed: ' . implode(' ', $stmt->errorInfo()));
    }

    if ($stmt->rowCount() > 0) {
        // Get task users for notification
        $task_users_stmt = $conn->prepare("
            SELECT DISTINCT tu.user_id 
            FROM task_users tu 
            JOIN tasks_checklists tc ON tc.task_id = tu.task_id 
            WHERE tc.id = ?
        ");
        $task_users_stmt->execute([$checklistId]);
        $task_users = $task_users_stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get task creator
        $task_creator_stmt = $conn->prepare("
            SELECT t.user_creator 
            FROM tasks t 
            JOIN tasks_checklists tc ON tc.task_id = t.id 
            WHERE tc.id = ?
        ");
        $task_creator_stmt->execute([$checklistId]);
        $task_creator = $task_creator_stmt->fetch(PDO::FETCH_COLUMN);
        
        // Add creator to the list if not already included
        if ($task_creator && !in_array($task_creator, $task_users)) {
            $task_users[] = $task_creator;
        }

        // Get task info and user name for notification
        $task_info_stmt = $conn->prepare("
            SELECT t.title, u.name as user_name 
            FROM tasks t 
            JOIN tasks_checklists tc ON tc.task_id = t.id 
            JOIN users u ON u.id = ? 
            WHERE tc.id = ?
        ");
        $task_info_stmt->execute([$my_profile_id, $checklistId]);
        $task_info = $task_info_stmt->fetch(PDO::FETCH_OBJ);

        if ($task_info) {
            // Create notification for checklist item update
            $notification_message = "{$task_info->user_name} updated a checklist item in task #{$checklistId} - {$task_info->title}: {$content}";
            $receiver_ids = implode(',', $task_users);
            $notification_stmt = $conn->prepare("INSERT INTO notifications (user_id, receiver_ids, message, date, time, users_read) VALUES (?, ?, ?, CURDATE(), CURTIME(), ?)");
            $notification_stmt->execute([$my_profile_id, $receiver_ids, $notification_message, $my_profile_id]);
        }

        echo json_encode(['success' => true, 'message' => 'Checklist item updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made to the checklist item']);
    }

} catch (Exception $e) {
    error_log('Checklist item update error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 