<?php
session_start();
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    // Get user ID from session
    $user_id = $my_profile_id ?? null;
    if (!$user_id) {
        throw new Exception('User not authenticated');
    }

    // Check if file was uploaded
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error');
    }

    $file = $_FILES['avatar'];
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
    }

    // Create uploads directory if it doesn't exist
    $upload_dir = 'uploads/profiles/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . $user_id . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to save uploaded file');
    }

    // Update avatar filename in database
    $sql = "UPDATE users SET avatar = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$filename, $user_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Avatar updated successfully',
        'avatar_url' => $upload_dir . $filename
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 