<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['note_id'])) {
        throw new Exception('Missing note ID');
    }

    $note_id = $_POST['note_id'];

    // Get file name before deleting
    $stmt = $conn->prepare("SELECT file_name FROM task_note WHERE id = ?");
    $stmt->execute([$note_id]);
    $file_name = $stmt->fetchColumn();

    // Delete the note
    $stmt = $conn->prepare("DELETE FROM task_note WHERE id = ?");
    $stmt->execute([$note_id]);

    // Delete the file if it exists
    if ($file_name) {
        $file_path = 'uploads/notes/' . $file_name;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 