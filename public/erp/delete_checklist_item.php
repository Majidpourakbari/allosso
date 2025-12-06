<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['checklist_id'])) {
    echo json_encode(['success' => false, 'message' => 'Checklist ID is required']);
    exit;
}

$checklist_id = $_POST['checklist_id'];

try {
    // Start transaction
    $conn->beginTransaction();
    
    // First, get all child checklists recursively
    function getChildChecklists($conn, $parent_id) {
        $children = [];
        $stmt = $conn->prepare("SELECT id FROM tasks_checklists WHERE parent_id = ?");
        $stmt->execute([$parent_id]);
        $child_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($child_ids as $child_id) {
            $children[] = $child_id;
            // Recursively get children of children
            $children = array_merge($children, getChildChecklists($conn, $child_id));
        }
        
        return $children;
    }
    
    // Get all child checklists
    $child_checklists = getChildChecklists($conn, $checklist_id);
    $all_checklists_to_delete = array_merge([$checklist_id], $child_checklists);
    
    // Get file paths for all checklists to be deleted
    $placeholders = str_repeat('?,', count($all_checklists_to_delete) - 1) . '?';
    $stmt = $conn->prepare("SELECT file_path, audio_path FROM tasks_checklists WHERE id IN ($placeholders)");
    $stmt->execute($all_checklists_to_delete);
    $checklists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Delete the files if they exist
    foreach ($checklists as $checklist) {
        if ($checklist['file_path']) {
            $file_path = 'uploads/checklists/files/' . $checklist['file_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        if ($checklist['audio_path']) {
            $audio_path = 'uploads/checklists/audio/' . $checklist['audio_path'];
            if (file_exists($audio_path)) {
                unlink($audio_path);
            }
        }
    }

    // Delete all checklists (parent and children)
    $stmt = $conn->prepare("DELETE FROM tasks_checklists WHERE id IN ($placeholders)");
    $result = $stmt->execute($all_checklists_to_delete);

    if ($result) {
        // Commit transaction
        $conn->commit();
        
        $deleted_count = count($all_checklists_to_delete);
        $message = $deleted_count > 1 ? 
            "Checklist item and {$deleted_count} child items deleted successfully" : 
            "Checklist item deleted successfully";
            
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        // Rollback transaction
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to delete checklist item']);
    }
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 