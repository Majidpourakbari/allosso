<?php
// Start session
if(session_id() == '') {
    session_start();
}

// Database connection settings
$host = 'localhost';
$username = 'erp';
$password = 'A%O~Lm5xSNE4';
$dbname = 'erp';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ));
} catch (PDOException $e) {
    error_log('Database connection error: ' . $e->getMessage());
    header('Location: https://allo-sso.com/dashboard');
    exit;
}

// Telegram notification function
function Telelogin($user_id, $text) {
    $token = "7657734664:AAGpbkasmOSmXcldRWOeg5xpJSkkwl-cTWU";
    $url = "https://api.telegram.org/bot$token/sendMessage";
    
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT name FROM users WHERE chat_id = :chat_id");
        $stmt->execute(['chat_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($user) {
            $name = $user->name;
            $message = "Hi *$name* 👋\n\n$text\n-------------\n*This broadcast message is sent automatically by the bot, please don't reply to it!*";
            
            $keyboard = [
                [
                    ['text' => '🔐 Login to System', 'callback_data' => 'login_system'],
                    ['text' => '📞 Contact Support', 'callback_data' => 'contact_support']
                ],
                [
                    ['text' => '❓ Help & FAQ', 'callback_data' => 'help_faq'],
                    ['text' => '📋 Account Status', 'callback_data' => 'account_status']
                ],
                [
                    ['text' => '🔄 Reactivate Account', 'callback_data' => 'reactivate_account'],
                    ['text' => '📧 Reset Password', 'callback_data' => 'reset_password']
                ]
            ];
            
            $data = [
                "chat_id" => $user_id,
                "text" => $message,
                "parse_mode" => "Markdown",
                "reply_markup" => json_encode([
                    "inline_keyboard" => $keyboard
                ])
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
    } catch (Exception $e) {
        error_log('Telelogin error: ' . $e->getMessage());
    }
    return false;
}

// Check if user is already logged in
if(isset($_SESSION['user_login'])) {
    header('Location: https://www.allo-sso.com/erp/dashboard');
    exit;
}

// Check if allohash parameter is provided
$allohash = isset($_GET['allohash']) ? trim($_GET['allohash']) : (isset($_POST['allohash']) ? trim($_POST['allohash']) : null);

if ($allohash && !empty($allohash)) {
    try {
        // Query the erp database users table for matching allohash
        $stmt = $conn->prepare("SELECT * FROM users WHERE allohash = :allohash LIMIT 1");
        $stmt->execute(['allohash' => $allohash]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user) {
            // Check if user account is active
            if (isset($user->status) && $user->status == 1) {
                // Create session for the user
                $_SESSION['user_login'] = [
                    'id' => $user->id,
                ];
                
                // Send Telegram login notification if user has chat_id
                if (!empty($user->chat_id)) {
                    $loginMessage = "🔐 *Login Successful*\n\nYou have successfully logged into the AlloHub ERP system via SSO.\n\n📅 Login Time: " . date('Y-m-d H:i:s') . "\n🌐 IP Address: " . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown');
                    Telelogin($user->chat_id, $loginMessage);
                }
                
                // Redirect to ERP dashboard
                header('Location: https://www.allo-sso.com/erp/dashboard');
                exit;
            } else {
                // Account is not active, redirect to main site
                header('Location: https://allo-sso.com/dashboard');
                exit;
            }
        } else {
            // User not found in ERP database, redirect to main site
            header('Location: https://allo-sso.com/dashboard');
            exit;
        }
    } catch (PDOException $e) {
        // Database error occurred
        error_log('ERP Index PDO Error: ' . $e->getMessage());
        header('Location: https://allo-sso.com/dashboard');
        exit;
    } catch (Exception $e) {
        // Other error occurred
        error_log('ERP Index Error: ' . $e->getMessage());
        header('Location: https://allo-sso.com/dashboard');
        exit;
    }
} else {
    // No allohash provided, redirect to main site dashboard
    header('Location: https://allo-sso.com/dashboard');
    exit;
}
?>
