<?php
session_start();
require_once 'controlls/db/functions.php';

// Set JSON header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_SESSION['user_login'])) {
            throw new Exception('User not logged in');
        }

        $user_id = $my_profile_id;
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

        // Update user's online status
        $stmt = $conn->prepare("UPDATE users SET online_status = :status WHERE id = :id");
        $result = $stmt->execute([
            'status' => $status,
            'id' => $user_id
        ]);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update status');
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
} 