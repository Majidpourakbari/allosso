<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
if (!isset($conn) || !$conn) {
    error_log("get_task_details.php: Database connection failed");
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Task ID is required']);
    exit;
}

$taskId = intval($_GET['id']);
error_log("get_task_details.php: Requested task ID: " . $taskId);

try {
    $stmt = $conn->prepare("SELECT t.*, u.name as creator_name, u.avatar as creator_avatar, 
                           GROUP_CONCAT(DISTINCT ur.name) as receiver_names,
                           GROUP_CONCAT(DISTINCT ur.avatar) as receiver_avatars,
                           DATE_FORMAT(t.date_create, '%Y-%m-%d %H:%i') as formatted_date
                           FROM tasks t 
                           LEFT JOIN users u ON t.user_creator = u.id 
                           LEFT JOIN task_users tu ON t.id = tu.task_id
                           LEFT JOIN users ur ON tu.user_id = ur.id 
                           WHERE t.id = ?
                           GROUP BY t.id");
    
    $stmt->execute([$taskId]);
    $task = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$task) {
        error_log("get_task_details.php: Task not found for ID: " . $taskId);
        echo json_encode(['error' => 'Task not found']);
        exit;
    }
    
    error_log("get_task_details.php: Found task: " . json_encode($task));
    
    // Get checklists for this task with hierarchical structure (only active items)
    $checklistStmt = $conn->prepare("
        SELECT tc.*, u.name as user_name, 
               p.content as parent_content,
               DATE_FORMAT(tc.date, '%Y-%m-%d') as formatted_date,
               DATE_FORMAT(tc.time, '%H:%i') as formatted_time
        FROM tasks_checklists tc
        LEFT JOIN users u ON tc.user_id = u.id
        LEFT JOIN tasks_checklists p ON tc.parent_id = p.id
        WHERE tc.task_id = ? AND tc.archive_status = 0
        ORDER BY tc.priority DESC, tc.parent_id ASC, tc.id ASC
    ");
    $checklistStmt->execute([$taskId]);
    $checklists = $checklistStmt->fetchAll(PDO::FETCH_OBJ);

    // Get archived checklists for this task (separate query)
    $archivedChecklistStmt = $conn->prepare("
        SELECT tc.*, u.name as user_name, 
               p.content as parent_content,
               DATE_FORMAT(tc.date, '%Y-%m-%d') as formatted_date,
               DATE_FORMAT(tc.time, '%H:%i') as formatted_time
        FROM tasks_checklists tc
        LEFT JOIN users u ON tc.user_id = u.id
        LEFT JOIN tasks_checklists p ON tc.parent_id = p.id
        WHERE tc.task_id = ? AND tc.archive_status = 1
        ORDER BY tc.priority DESC, tc.parent_id ASC, tc.id ASC
    ");
    $archivedChecklistStmt->execute([$taskId]);
    $archivedChecklists = $archivedChecklistStmt->fetchAll(PDO::FETCH_OBJ);

    // Organize checklists into hierarchical structure
    $hierarchicalChecklists = [];
    $archivedHierarchicalChecklists = [];
    $checklistMap = [];
    $archivedChecklistMap = [];
    
    // First pass: create a map of all active checklists
    foreach ($checklists as $checklist) {
        $checklistMap[$checklist->id] = $checklist;
        $checklist->children = [];
    }
    
    // Second pass: organize active checklists into hierarchy
    foreach ($checklists as $checklist) {
        if ($checklist->parent_id === null) {
            // This is a root level checklist
            $hierarchicalChecklists[] = $checklist;
        } else {
            // This is a child checklist
            if (isset($checklistMap[$checklist->parent_id])) {
                $checklistMap[$checklist->parent_id]->children[] = $checklist;
            }
        }
    }

    // First pass: create a map of all archived checklists
    foreach ($archivedChecklists as $checklist) {
        $archivedChecklistMap[$checklist->id] = $checklist;
        $checklist->children = [];
    }
    
    // Second pass: organize archived checklists into hierarchy
    foreach ($archivedChecklists as $checklist) {
        if ($checklist->parent_id === null) {
            // This is a root level checklist
            $archivedHierarchicalChecklists[] = $checklist;
        } else {
            // This is a child checklist
            if (isset($archivedChecklistMap[$checklist->parent_id])) {
                $archivedChecklistMap[$checklist->parent_id]->children[] = $checklist;
            }
        }
    }

    // Get notes for this task with proper user information
    $notesStmt = $conn->prepare("
        SELECT tn.*, u.name as user_name, u.avatar as user_avatar,
               DATE_FORMAT(tn.date, '%Y-%m-%d') as formatted_date,
               DATE_FORMAT(tn.time, '%H:%i') as formatted_time
        FROM task_note tn
        LEFT JOIN users u ON tn.user_id = u.id
        WHERE tn.task_id = ?
        ORDER BY tn.id DESC
    ");
    $notesStmt->execute([$taskId]);
    $notes = $notesStmt->fetchAll(PDO::FETCH_OBJ);

    // Get files for this task
    $filesStmt = $conn->prepare("
        SELECT tf.*, u.name as user_name,
               DATE_FORMAT(tf.date, '%Y-m-d') as formatted_date,
               DATE_FORMAT(tf.time, '%H:%i') as formatted_time
        FROM task_files tf
        LEFT JOIN users u ON tf.user_id = u.id
        WHERE tf.task_id = ?
        ORDER BY tf.id DESC
    ");
    $filesStmt->execute([$taskId]);
    $files = $filesStmt->fetchAll(PDO::FETCH_OBJ);

    // Get document links for this task
    $documentsStmt = $conn->prepare("
        SELECT td.*, u.name as user_name, u.avatar as user_avatar,
               DATE_FORMAT(td.date, '%Y-m-d') as formatted_date,
               DATE_FORMAT(td.time, '%H:%i') as formatted_time
        FROM task_document_links td
        LEFT JOIN users u ON td.user_id = u.id
        WHERE td.task_id = ?
        ORDER BY td.id DESC
    ");
    $documentsStmt->execute([$taskId]);
    $document_links = $documentsStmt->fetchAll(PDO::FETCH_OBJ);

    // Get task users
    $usersStmt = $conn->prepare("
        SELECT u.id, u.name, u.avatar, u.role
        FROM task_users tu
        JOIN users u ON tu.user_id = u.id
        WHERE tu.task_id = ?
        ORDER BY u.name
    ");
    $usersStmt->execute([$taskId]);
    $task_users = $usersStmt->fetchAll(PDO::FETCH_OBJ);

    // Map status values to text and classes
    $statusText = match($task->status) {
        '0' => 'To Do',
        '1' => 'In Progress',
        '2' => 'Review',
        '3' => 'Done',
        '4' => 'Archived',
        '5' => 'To Debug',
        default => 'Unknown'
    };
    
    $statusClass = match($task->status) {
        '0' => 'secondary',
        '1' => 'primary',
        '2' => 'warning',
        '3' => 'success',
        '4' => 'dark',
        '5' => 'danger',
        default => 'secondary'
    };
    
    $priorityClass = match($task->priority) {
        'Low' => 'info',
        'Medium' => 'primary',
        'High' => 'warning',
        'Critical' => 'danger',
        'Hotfix' => 'danger',
        'Urgent' => 'danger',
        default => 'secondary'
    };

    // Prepare response data
    $response = [
        'id' => $task->id,
        'title' => $task->title,
        'description' => $task->description,
        'category' => $task->category,
        'allo_section' => $task->allo_section,
        'label' => $task->label,
        'priority' => $task->priority,
        'status' => $task->status,
        'statusText' => $statusText,
        'statusClass' => $statusClass,
        'priorityClass' => $priorityClass,
        'objective' => $task->objective,
        'progress' => $task->progress,
        'date_start' => $task->date_start,
        'time_start' => $task->time_start,
        'date_finish' => $task->date_finish,
        'time_finish' => $task->time_finish,
        'risks' => $task->risks,
        'required_tools' => $task->required_tools,
        'budget' => $task->budget,
        'creator_name' => $task->creator_name,
        'creator_avatar' => $task->creator_avatar,
        'formatted_date' => $task->formatted_date,
        'checklists' => $hierarchicalChecklists,
        'archived_checklists' => $archivedHierarchicalChecklists,
        'notes' => $notes,
        'files' => $files,
        'document_links' => $document_links,
        'task_users' => $task_users,
        'receiver_info' => $task->receiver_names ? 
            implode('||', array_map(function($name, $avatar) {
                return ":$name:$avatar";
            }, explode(',', $task->receiver_names), explode(',', $task->receiver_avatars))) : null
    ];

    error_log("get_task_details.php: Sending response: " . json_encode($response));
    echo json_encode($response);

} catch (Exception $e) {
    error_log("get_task_details.php: Error occurred: " . $e->getMessage());
    error_log("get_task_details.php: Stack trace: " . $e->getTraceAsString());
    echo json_encode(['error' => $e->getMessage()]);
} 