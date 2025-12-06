<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    // Get task ID from request
    $task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : null;
    
    if (!$task_id) {
        throw new Exception('Task ID is required');
    }

    // Get task creator ID
    $creatorStmt = $conn->prepare("SELECT user_creator FROM tasks WHERE id = ?");
    $creatorStmt->execute([$task_id]);
    $taskCreator = $creatorStmt->fetchColumn();

    // Get last 5 users excluding task creator
    $stmt = $conn->prepare("SELECT id, name, avatar 
                           FROM users 
                           WHERE id != ?
                           ORDER BY id DESC 
                           LIMIT 5");
    $result = $stmt->execute([$taskCreator]);
    
    if (!$result) {
        throw new Exception('Failed to fetch users');
    }
    
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    echo json_encode($users);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
} 