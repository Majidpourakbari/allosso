<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $checklistId = $_POST['checklist_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$checklistId || !isset($status)) {
        throw new Exception('Checklist ID and status are required');
    }

    // Get user info for finished_by
    $userStmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $userStmt->execute([$my_profile_id]);
    $user = $userStmt->fetch(PDO::FETCH_OBJ);

    // Update the checklist status and completion info
    if ($status === '1') {
        // When marking as complete, set finished_by and finished_at
        $stmt = $conn->prepare("UPDATE tasks_checklists 
                               SET status = ?, 
                                   finished_by = ?, 
                                   finished_at = NOW() 
                               WHERE id = ?");
        $stmt->execute([$status, $user->name, $checklistId]);
    } else {
        // When marking as incomplete, clear finished_by and finished_at
        $stmt = $conn->prepare("UPDATE tasks_checklists 
                               SET status = ?, 
                                   finished_by = NULL, 
                                   finished_at = NULL 
                               WHERE id = ?");
        $stmt->execute([$status, $checklistId]);
    }

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Checklist status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made to the checklist']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 