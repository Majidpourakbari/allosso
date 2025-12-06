<?php
session_start();
require_once 'controlls/db/functions.php';

// Set JSON header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        if (!isset($_SESSION['user_login'])) {
            throw new Exception('User not logged in');
        }

        $user_id = $my_profile_id;

        // Get user's online status
        $stmt = $conn->prepare("SELECT online_status FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        if ($result) {
            echo json_encode([
                'success' => true,
                'status' => (int)$result->online_status
            ]);
        } else {
            throw new Exception('User not found');
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