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
        
        $noteId = $data['noteId'] ?? '';
        
        if (empty($noteId)) {
            throw new Exception('Note ID is required');
        }
        
        // Delete note from database (only if created by current user)
        $stmt = $conn->prepare("
            DELETE FROM board_notes 
            WHERE id = ? AND created_by = ?
        ");
        $result = $stmt->execute([$noteId, $my_profile_id]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('Note not found or you do not have permission to delete it');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Note deleted successfully'
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