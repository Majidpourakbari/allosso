<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['file_id'])) {
        throw new Exception('Missing file ID');
    }

    $file_id = $_POST['file_id'];

    // Get file information before deleting
    $stmt = $conn->prepare("SELECT file_name FROM task_files WHERE id = ?");
    $stmt->execute([$file_id]);
    $file_name = $stmt->fetchColumn();

    if (!$file_name) {
        throw new Exception('File not found');
    }

    // Delete the file record from database
    $stmt = $conn->prepare("DELETE FROM task_files WHERE id = ?");
    $stmt->execute([$file_id]);

    // Delete the physical file
    $file_path = 'uploads/tasks/files/' . $file_name;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Error in delete_task_file.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 