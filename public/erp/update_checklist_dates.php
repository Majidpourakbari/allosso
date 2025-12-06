<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $checklistId = $_POST['checklist_id'] ?? null;
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;
    $startTime = $_POST['start_time'] ?? '00:00:00';
    $endTime = $_POST['end_time'] ?? '23:59:59';

    if (!$checklistId || !$startDate) {
        throw new Exception('Checklist ID and start date are required');
    }

    // Combine date and time
    $startDateTime = $startDate . ' ' . $startTime;
    $endDateTime = $endDate . ' ' . $endTime;

    // Update the checklist dates and times
    $stmt = $conn->prepare("UPDATE tasks_checklists 
                           SET start_date = ?, 
                               end_date = ?,
                               start_time = ?,
                               end_time = ? 
                           WHERE id = ?");
    
    $stmt->execute([$startDate, $endDate, $startTime, $endTime, $checklistId]);

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

        // Get checklist and task info for notification
        $checklist_info_stmt = $conn->prepare("
            SELECT tc.content, t.title, u.name as user_name 
            FROM tasks_checklists tc 
            JOIN tasks t ON tc.task_id = t.id 
            JOIN users u ON u.id = ? 
            WHERE tc.id = ?
        ");
        $checklist_info_stmt->execute([$my_profile_id, $checklistId]);
        $checklist_info = $checklist_info_stmt->fetch(PDO::FETCH_OBJ);

        if ($checklist_info) {
            // Create notification for date update
            $notification_message = "{$checklist_info->user_name} updated dates for checklist item in task #{$checklistId} - {$checklist_info->title}: {$checklist_info->content}";
            $receiver_ids = implode(',', $task_users);
            $notification_stmt = $conn->prepare("INSERT INTO notifications (user_id, receiver_ids, message, date, time, users_read) VALUES (?, ?, ?, CURDATE(), CURTIME(), ?)");
            $notification_stmt->execute([$my_profile_id, $receiver_ids, $notification_message, $my_profile_id]);
        }

        echo json_encode(['success' => true, 'message' => 'Checklist dates and times updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made to the checklist']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 