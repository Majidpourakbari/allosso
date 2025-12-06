<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['task_id'])) {
        throw new Exception('Missing task ID');
    }

    $task_id = $_POST['task_id'];

    // Start transaction
    $conn->beginTransaction();

    // Delete related records first
    $tables = [
        'task_note',
        'task_document_links',
        'tasks_checklists',
        'task_users'
    ];

    foreach ($tables as $table) {
        $stmt = $conn->prepare("DELETE FROM $table WHERE task_id = ?");
        $stmt->execute([$task_id]);
    }

    // Delete the task
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 