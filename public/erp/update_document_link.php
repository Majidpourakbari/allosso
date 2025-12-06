<?php
include 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['document_id']) || !isset($_POST['title']) || !isset($_POST['url'])) {
        throw new Exception('Missing required fields');
    }

    $document_id = $_POST['document_id'];
    $title = $_POST['title'];
    $url = $_POST['url'];
    $description = $_POST['description'] ?? null;

    // Update document link in database
    $stmt = $conn->prepare("UPDATE task_document_links SET title = ?, url = ?, description = ? WHERE id = ?");
    $stmt->execute([$title, $url, $description, $document_id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 