<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['task_id'])) {
    echo json_encode(['error' => 'Task ID is required']);
    exit;
}

$taskId = intval($_GET['task_id']);
$excludeId = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : null;

try {
    // Get all checklists for this task that can be parents
    $sql = "SELECT id, content, parent_id FROM tasks_checklists WHERE task_id = ?";
    $params = [$taskId];
    
    if ($excludeId) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
    }
    
    $sql .= " ORDER BY parent_id ASC, id ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $checklists = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Organize into hierarchical structure
    $hierarchicalChecklists = [];
    $checklistMap = [];
    
    // First pass: create a map of all checklists
    foreach ($checklists as $checklist) {
        $checklistMap[$checklist->id] = $checklist;
        $checklist->children = [];
    }
    
    // Second pass: organize into hierarchy
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
    
    // Flatten the hierarchical structure for dropdown display
    function flattenForDropdown($checklists, $level = 0) {
        $flat = [];
        foreach ($checklists as $checklist) {
            $prefix = str_repeat('─', $level);
            $flat[] = [
                'id' => $checklist->id,
                'content' => $prefix . ($prefix ? ' ' : '') . $checklist->content,
                'level' => $level
            ];
            
            if ($checklist->children && count($checklist->children) > 0) {
                $flat = array_merge($flat, flattenForDropdown($checklist->children, $level + 1));
            }
        }
        return $flat;
    }
    
    $dropdownOptions = flattenForDropdown($hierarchicalChecklists);
    
    echo json_encode([
        'success' => true,
        'checklists' => $dropdownOptions
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 