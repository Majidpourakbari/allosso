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

    // Get current user data
    $stmt = $conn->prepare("SELECT email, chat_id FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $current_user = $stmt->fetch(PDO::FETCH_OBJ);

    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $chat_id = $_POST['chat_id'] ?? '';
    $password = $_POST['password'] ?? '';

    // Start building the update query
    $updates = [];
    $params = [];

    if (!empty($name)) {
        $updates[] = "name = ?";
        $params[] = $name;
    }

    // Only allow email update if current email is empty
    if (!empty($email)) {
        if (empty($current_user->email)) {
            $updates[] = "email = ?";
            $params[] = $email;
        } else if ($email !== $current_user->email) {
            throw new Exception('Email cannot be changed once set');
        }
    }

    if (!empty($phone_number)) {
        $updates[] = "phone_number = ?";
        $params[] = $phone_number;
    }

    // Only allow chat_id update if current chat_id is empty
    if (!empty($chat_id)) {
        if (empty($current_user->chat_id)) {
            $updates[] = "chat_id = ?";
            $params[] = $chat_id;
        } else if ($chat_id !== $current_user->chat_id) {
            throw new Exception('Chat ID cannot be changed once set');
        }
    }

    if (!empty($password)) {
        $updates[] = "password = ?";
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }

    if (empty($updates)) {
        throw new Exception('No fields to update');
    }

    // Add user_id to params
    $params[] = $user_id;

    // Build and execute the query
    $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    // Fetch updated user data
    $stmt = $conn->prepare("SELECT name, email, phone_number, chat_id FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $updated_data = $stmt->fetch(PDO::FETCH_OBJ);

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully',
        'updated_data' => $updated_data
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 