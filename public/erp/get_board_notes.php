<?php
require_once 'controlls/db/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_login']) || !isset($my_profile_id)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Get all notes for the current user
        $stmt = $conn->prepare("
            SELECT id, column_name, title, content, created_at, updated_at
            FROM board_notes 
            WHERE created_by = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$my_profile_id]);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Group notes by column
        $groupedNotes = [
            'saturday' => [],
            'work' => [],
            'friday' => []
        ];
        
        foreach ($notes as $note) {
            $groupedNotes[$note['column_name']][] = [
                'id' => $note['id'],
                'title' => $note['title'],
                'content' => $note['content'],
                'created_at' => $note['created_at'],
                'updated_at' => $note['updated_at']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'notes' => $groupedNotes
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
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