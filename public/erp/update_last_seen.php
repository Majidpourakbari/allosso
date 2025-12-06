<?php
session_start();
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_login'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

try {
    $user_id = $my_profile_id;
    
    // Update last_seen timestamp and set user online (automatic activity tracking)
    $stmt = $conn->prepare("UPDATE users SET last_seen = NOW(), online_status = 1 WHERE id = :id");
    $result = $stmt->execute(['id' => $user_id]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Last seen updated and user set online automatically'
        ]);
    } else {
        throw new Exception('Failed to update last seen');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 