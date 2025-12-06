<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['document_id'])) {
        throw new Exception('Missing document ID');
    }

    $document_id = $_POST['document_id'];

    // Delete document link from database
    $stmt = $conn->prepare("DELETE FROM task_document_links WHERE id = ?");
    $stmt->execute([$document_id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 