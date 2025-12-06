<?php
require_once 'controlls/db/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_login']) || !isset($my_profile_id)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            throw new Exception('Invalid JSON data');
        }
        
        $column = $data['column'] ?? '';
        $title = $data['title'] ?? '';
        $content = $data['content'] ?? '';
        $noteId = $data['noteId'] ?? '';
        
        if (empty($column) || empty($title) || empty($content)) {
            throw new Exception('Missing required fields');
        }
        
        // Validate column
        $validColumns = ['saturday', 'work', 'friday'];
        if (!in_array($column, $validColumns)) {
            throw new Exception('Invalid column');
        }
        
        // Insert or update note in database
        if (empty($noteId)) {
            // Insert new note
            $stmt = $conn->prepare("
                INSERT INTO board_notes (column_name, title, content, created_by, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$column, $title, $content, $my_profile_id]);
            $noteId = $conn->lastInsertId();
        } else {
            // Update existing note
            $stmt = $conn->prepare("
                UPDATE board_notes 
                SET title = ?, content = ?, updated_at = NOW() 
                WHERE id = ? AND created_by = ?
            ");
            $stmt->execute([$title, $content, $noteId, $my_profile_id]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Note saved successfully',
            'noteId' => $noteId
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?> 