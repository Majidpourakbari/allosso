<?php
session_start();
include_once 'controlls/db/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_login']) || !isset($my_profile_id)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $my_profile_id;

try {
    // Get total tasks for the user (creator or assigned)
    $totalTasksQuery = "SELECT COUNT(DISTINCT t.id) as total 
                        FROM tasks t 
                        LEFT JOIN task_users tu ON t.id = tu.task_id 
                        WHERE t.user_creator = ? OR tu.user_id = ?";
    $stmt = $conn->prepare($totalTasksQuery);
    $stmt->execute([$user_id, $user_id]);
    $totalTasks = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get pending checklists (status = 0)
    $pendingChecklistsQuery = "SELECT COUNT(tc.id) as pending_checklists 
                               FROM tasks_checklists tc
                               INNER JOIN tasks t ON tc.task_id = t.id
                               LEFT JOIN task_users tu ON t.id = tu.task_id 
                               WHERE (t.user_creator = ? OR tu.user_id = ?) AND tc.status = 0";
    $stmt = $conn->prepare($pendingChecklistsQuery);
    $stmt->execute([$user_id, $user_id]);
    $pendingChecklists = $stmt->fetch(PDO::FETCH_ASSOC)['pending_checklists'];
    
    // Get completed checklists (status = 1)
    $completedChecklistsQuery = "SELECT COUNT(tc.id) as completed_checklists 
                                 FROM tasks_checklists tc
                                 INNER JOIN tasks t ON tc.task_id = t.id
                                 LEFT JOIN task_users tu ON t.id = tu.task_id 
                                 WHERE (t.user_creator = ? OR tu.user_id = ?) AND tc.status = 1";
    $stmt = $conn->prepare($completedChecklistsQuery);
    $stmt->execute([$user_id, $user_id]);
    $completedChecklists = $stmt->fetch(PDO::FETCH_ASSOC)['completed_checklists'];
    
    // Get pending tasks (status = 0 - To Do or status = 2 - Review)
    $pendingTasksQuery = "SELECT COUNT(DISTINCT t.id) as pending 
                          FROM tasks t 
                          LEFT JOIN task_users tu ON t.id = tu.task_id 
                          WHERE (t.user_creator = ? OR tu.user_id = ?) AND (t.status = 0 OR t.status = 2)";
    $stmt = $conn->prepare($pendingTasksQuery);
    $stmt->execute([$user_id, $user_id]);
    $pendingTasks = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_tasks' => (int)$totalTasks,
            'pending_checklists' => (int)$pendingChecklists,
            'completed_checklists' => (int)$completedChecklists,
            'pending_tasks' => (int)$pendingTasks
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred',
        'debug' => $e->getMessage()
    ]);
}
?> 