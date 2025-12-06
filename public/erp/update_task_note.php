<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['note_id']) || !isset($_POST['note'])) {
        throw new Exception('Missing required fields');
    }

    $note_id = $_POST['note_id'];
    $note = $_POST['note'];

    // Handle file upload if present
    $file_name = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/notes/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Get current file name to delete old file
        $stmt = $conn->prepare("SELECT file_name FROM task_note WHERE id = ?");
        $stmt->execute([$note_id]);
        $old_file = $stmt->fetchColumn();

        if ($old_file && file_exists($upload_dir . $old_file)) {
            unlink($upload_dir . $old_file);
        }

        $file_extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $file_name;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $upload_path)) {
            throw new Exception('Failed to upload file');
        }

        // Update with new file
        $stmt = $conn->prepare("UPDATE task_note SET note = ?, file_name = ? WHERE id = ?");
        $stmt->execute([$note, $file_name, $note_id]);
    } else {
        // Update without changing file
        $stmt = $conn->prepare("UPDATE task_note SET note = ? WHERE id = ?");
        $stmt->execute([$note, $note_id]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 