<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['term']) || !isset($_GET['task_id'])) {
        throw new Exception('Search term and task ID are required');
    }

    $searchTerm = '%' . $_GET['term'] . '%';
    $task_id = intval($_GET['task_id']);
    error_log("Searching users with term: " . $_GET['term']);

    // Get task creator ID
    $creatorStmt = $conn->prepare("SELECT user_creator FROM tasks WHERE id = ?");
    $creatorStmt->execute([$task_id]);
    $taskCreator = $creatorStmt->fetchColumn();

    // Search for users excluding task creator
    $stmt = $conn->prepare("SELECT id, name, avatar 
                           FROM users 
                           WHERE name LIKE ? 
                           AND id != ?
                           ORDER BY name ASC 
                           LIMIT 10");
    $result = $stmt->execute([$searchTerm, $taskCreator]);
    
    if (!$result) {
        error_log("Database error in search_users.php: " . print_r($stmt->errorInfo(), true));
        throw new Exception('Failed to search users');
    }
    
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    error_log("Found " . count($users) . " users matching the search term");

    echo json_encode($users);
} catch (Exception $e) {
    error_log("Error in search_users.php: " . $e->getMessage());
    echo json_encode([
        'error' => $e->getMessage()
    ]);
} 