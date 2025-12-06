<?php
// Prevent any output before JSON response
ob_start();
session_start();
require_once 'controlls/db/functions.php';

// Clear any previous output
ob_clean();

// Set JSON header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        if (!$email || !$password) {
            throw new Exception('Email and password are required');
        }

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user) {
            if (password_verify($password, $user->password)) {
                if ($user->status == 1) {
                    $_SESSION['user_login'] = [
                        'id' => $user->id,
                    ];
                    
                    // Send Telegram login notification if user has chat_id
                    if (!empty($user->chat_id)) {
                        $loginMessage = "🔐 *Login Successful*\n\nYou have successfully logged into the AlloHub ERP system.\n\n📅 Login Time: " . date('Y-m-d H:i:s') . "\n🌐 IP Address: " . $_SERVER['REMOTE_ADDR'];
                        Telelogin($user->chat_id, $loginMessage);
                    }
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Login successful'
                    ]);
                    exit;
                } else {
                    session_unset();
                    session_destroy();
                    echo json_encode([
                        'success' => false,
                        'message' => 'Your account is not active'
                    ]);
                    exit;
                }
            }
        }

        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
// End output buffering and send
ob_end_flush(); 