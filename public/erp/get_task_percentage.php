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

    // Get total number of open tasks (status != 3 (Done) and != 4 (Archived))
    $totalOpenTasksStmt = $conn->prepare("
        SELECT COUNT(*) as total_open_tasks
        FROM tasks t
        LEFT JOIN task_users tu ON t.id = tu.task_id
        WHERE (t.user_creator = ? OR tu.user_id = ?)
        AND t.status NOT IN (3, 4)
    ");
    $totalOpenTasksStmt->execute([$my_profile_id, $my_profile_id]);
    $totalOpenTasks = $totalOpenTasksStmt->fetch(PDO::FETCH_OBJ)->total_open_tasks;

    if ($totalOpenTasks == 0) {
        echo json_encode([
            'success' => true,
            'percentage' => 0,
            'total_open_tasks' => 0,
            'message' => 'No open tasks found'
        ]);
        exit;
    }

    // Calculate percentage (each task represents 1/total_open_tasks * 100)
    $percentage = round((1 / $totalOpenTasks) * 100, 2);

    echo json_encode([
        'success' => true,
        'percentage' => $percentage,
        'total_open_tasks' => $totalOpenTasks,
        'message' => 'Task percentage calculated successfully'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 