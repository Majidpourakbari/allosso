<?php
session_start();

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$isAjax) {
    header('Content-Type: text/html; charset=utf-8');
} else {
    header('Content-Type: application/json');
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'controlls/db/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$apiKey = '7200f91678d5daab461c48376cd6773c';
$allossoApiUrl = 'https://www.allo-sso.com/api/v1/external-auth';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['allohash'])) {
    $allohash = urldecode($_GET['allohash']);
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    $ch = curl_init('https://www.allo-sso.com/api/v1/verify-allohash');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-API-Key: ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['allohash' => $allohash]));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        if ($isAjax) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Connection error: ' . $curlError
            ]);
        } else {
            header('Location: /login?error=connection_error');
        }
        exit;
    }
    
    $data = json_decode($response, true);
    
    if ($data && $data['success'] && $httpCode === 200) {
        $allossoUser = $data['data'];
        $email = $allossoUser['email'];
        
        try {
            $user_query = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $user_query->bindParam(':email', $email);
            $user_query->execute();
            $allolancerUser = $user_query->fetch(PDO::FETCH_OBJ);
            
            if (!$allolancerUser) {
                $tempPassword = password_hash($allossoUser['allohash'], PASSWORD_BCRYPT);
                
                $insert_user = $conn->prepare("
                    INSERT INTO users (username, password, status, name, role, email, account_type, allohash, created_at) 
                    VALUES (:username, :password, 1, :name, 'user', :email, 'freelancer', :allohash, NOW())
                ");
                $insert_user->bindParam(':username', $email);
                $insert_user->bindParam(':password', $tempPassword);
                $insert_user->bindParam(':name', $allossoUser['name']);
                $insert_user->bindParam(':email', $email);
                $insert_user->bindParam(':allohash', $allohash);
                $insert_user->execute();
                
                $allolancerUserId = $conn->lastInsertId();
                
                $user_query = $conn->prepare("SELECT * FROM users WHERE id = :id");
                $user_query->bindParam(':id', $allolancerUserId);
                $user_query->execute();
                $allolancerUser = $user_query->fetch(PDO::FETCH_OBJ);
            } else {
                if (!isset($allolancerUser->allohash) || $allolancerUser->allohash !== $allohash) {
                    $update_allohash = $conn->prepare("UPDATE users SET allohash = :allohash WHERE id = :id");
                    $update_allohash->bindParam(':allohash', $allohash);
                    $update_allohash->bindParam(':id', $allolancerUser->id);
                    $update_allohash->execute();
                }
            }
            
            $update_login = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $update_login->bindParam(':id', $allolancerUser->id);
            $update_login->execute();
            
            $_SESSION['user_login'] = [
                'id' => $allolancerUser->id,
                'name' => $allolancerUser->name ?? $allossoUser['name'],
                'email' => $allolancerUser->email,
                'role' => $allolancerUser->role ?? 'user',
                'avatar' => $allolancerUser->avatar ?? null,
                'login_time' => date('Y-m-d H:i:s')
            ];
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'user' => [
                            'id' => $allolancerUser->id,
                            'name' => $allolancerUser->name ?? $allossoUser['name'],
                            'email' => $allolancerUser->email,
                            'role' => $allolancerUser->role ?? 'user',
                            'avatar' => $allolancerUser->avatar ?? null
                        ],
                        'redirect_url' => 'dashboard'
                    ]
                ]);
            } else {
                header('Location: /dashboard');
            }
        } catch (PDOException $e) {
            if ($isAjax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Database error: ' . $e->getMessage()
                ]);
            } else {
                header('Location: /login?error=database_error');
            }
        }
    } else {
        $errorMessage = $data['message'] ?? 'Invalid allohash';
        if ($isAjax) {
            http_response_code($httpCode ?: 401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $errorMessage,
                'debug' => [
                    'http_code' => $httpCode,
                    'response' => $response
                ]
            ]);
        } else {
            header('Location: /login?error=invalid_allohash');
        }
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    if (empty($email) || empty($password)) {
        if ($isAjax) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Email and password are required'
            ]);
        } else {
            header('Location: /login?error=missing_fields');
        }
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if ($isAjax) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Invalid email format'
            ]);
        } else {
            header('Location: /login?error=invalid_email');
        }
        exit;
    }
    
    $ch = curl_init($allossoApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-API-Key: ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'email' => strtolower($email),
        'password' => $password
    ]));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        if ($isAjax) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Connection error: ' . $curlError
            ]);
        } else {
            header('Location: /login?error=connection_error');
        }
        exit;
    }
    
    $data = json_decode($response, true);
    
    if ($data && $data['success'] && $httpCode === 200) {
        $allossoUser = $data['data'];
        $email = strtolower($email);
        
        try {
            $user_query = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $user_query->bindParam(':email', $email);
            $user_query->execute();
            $allolancerUser = $user_query->fetch(PDO::FETCH_OBJ);
            
            if (!$allolancerUser) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $allohash = $allossoUser['allohash'];
                
                $insert_user = $conn->prepare("
                    INSERT INTO users (username, password, status, name, role, email, account_type, allohash, created_at) 
                    VALUES (:username, :password, 1, :name, 'user', :email, 'freelancer', :allohash, NOW())
                ");
                $insert_user->bindParam(':username', $email);
                $insert_user->bindParam(':password', $hashedPassword);
                $insert_user->bindParam(':name', $allossoUser['name']);
                $insert_user->bindParam(':email', $email);
                $insert_user->bindParam(':allohash', $allohash);
                $insert_user->execute();
                
                $allolancerUserId = $conn->lastInsertId();
                
                $user_query = $conn->prepare("SELECT * FROM users WHERE id = :id");
                $user_query->bindParam(':id', $allolancerUserId);
                $user_query->execute();
                $allolancerUser = $user_query->fetch(PDO::FETCH_OBJ);
            } else {
                $allohash = $allossoUser['allohash'];
                if (!$allolancerUser->allohash || $allolancerUser->allohash !== $allohash) {
                    $update_allohash = $conn->prepare("UPDATE users SET allohash = :allohash WHERE id = :id");
                    $update_allohash->bindParam(':allohash', $allohash);
                    $update_allohash->bindParam(':id', $allolancerUser->id);
                    $update_allohash->execute();
                }
            }
            
            $update_login = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $update_login->bindParam(':id', $allolancerUser->id);
            $update_login->execute();
            
            $_SESSION['user_login'] = [
                'id' => $allolancerUser->id,
                'name' => $allolancerUser->name ?? $allossoUser['name'],
                'email' => $allolancerUser->email,
                'role' => $allolancerUser->role ?? 'user',
                'avatar' => $allolancerUser->avatar ?? null,
                'login_time' => date('Y-m-d H:i:s')
            ];
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'user' => [
                            'id' => $allolancerUser->id,
                            'name' => $allolancerUser->name ?? $allossoUser['name'],
                            'email' => $allolancerUser->email,
                            'role' => $allolancerUser->role ?? 'user',
                            'avatar' => $allolancerUser->avatar ?? null
                        ],
                        'redirect_url' => 'dashboard'
                    ]
                ]);
            } else {
                header('Location: /dashboard');
            }
        } catch (PDOException $e) {
            if ($isAjax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Database error: ' . $e->getMessage()
                ]);
            } else {
                header('Location: /login?error=database_error');
            }
        }
    } else {
        $errorMessage = $data['message'] ?? 'Authentication failed';
        if ($isAjax) {
            http_response_code($httpCode ?: 401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $errorMessage
            ]);
        } else {
            header('Location: /login?error=authentication_failed');
        }
    }
    exit;
}

http_response_code(405);
echo json_encode([
    'success' => false,
    'error' => 'Method not allowed. Use POST or GET method.'
]);
?>

