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

// Define upload directories
define('VOICE_UPLOAD_DIR', 'uploads/voice_messages/');
define('FILE_UPLOAD_DIR', 'uploads/files/');

// Create upload directories if they don't exist
if (!file_exists(VOICE_UPLOAD_DIR)) {
    mkdir(VOICE_UPLOAD_DIR, 0777, true);
}
if (!file_exists(FILE_UPLOAD_DIR)) {
    mkdir(FILE_UPLOAD_DIR, 0777, true);
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'send':
            $receiver_id = $_POST['receiver_id'] ?? null;
            $message = $_POST['message'] ?? '';
            $reply_to_id = $_POST['reply_to_id'] ?? null;
            
            if (!$receiver_id || !$message) {
                throw new Exception('Missing required parameters');
            }
            
            // Insert message into database
            $stmt = $conn->prepare("
                INSERT INTO messages (sender_id, receiver_id, reply_to_id, message, message_type, created_at, is_read) 
                VALUES (:sender_id, :receiver_id, :reply_to_id, :message, 'text', NOW(), FALSE)
            ");
            
            $result = $stmt->execute([
                'sender_id' => $my_profile_id,
                'receiver_id' => $receiver_id,
                'reply_to_id' => $reply_to_id,
                'message' => $message
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Message sent successfully'
                ]);
            } else {
                throw new Exception('Failed to send message');
            }
            break;
            
        case 'send_voice':
            $receiver_id = $_POST['receiver_id'] ?? null;
            $voice_message = $_FILES['voice_message'] ?? null;
            
            if (!$receiver_id || !$voice_message) {
                throw new Exception('Missing required parameters');
            }
            
            // Generate unique filename
            $filename = uniqid() . '.webm';
            
            // Move uploaded file
            if (move_uploaded_file($voice_message['tmp_name'], VOICE_UPLOAD_DIR . $filename)) {
                // Get audio duration using FFmpeg
                $duration = 0;
                if (function_exists('exec')) {
                    $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg(VOICE_UPLOAD_DIR . $filename);
                    $duration = (int)exec($cmd);
                }
                
                // Insert voice message into database
                $stmt = $conn->prepare("
                    INSERT INTO messages (sender_id, receiver_id, message_type, file_name, file_size, created_at, is_read) 
                    VALUES (:sender_id, :receiver_id, 'voice', :file_name, :file_size, NOW(), FALSE)
                ");
                
                $result = $stmt->execute([
                    'sender_id' => $my_profile_id,
                    'receiver_id' => $receiver_id,
                    'file_name' => $filename,
                    'file_size' => $voice_message['size']
                ]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Voice message sent successfully'
                    ]);
                } else {
                    // Clean up file if database insert fails
                    unlink(VOICE_UPLOAD_DIR . $filename);
                    throw new Exception('Failed to save voice message');
                }
            } else {
                throw new Exception('Failed to upload voice message');
            }
            break;
            
        case 'send_file':
            $receiver_id = $_POST['receiver_id'] ?? null;
            $file = $_FILES['file'] ?? null;
            
            if (!$receiver_id || !$file) {
                throw new Exception('Missing required parameters');
            }
            
            // Generate unique filename
            $filename = uniqid() . '_' . basename($file['name']);
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], FILE_UPLOAD_DIR . $filename)) {
                // Insert file message into database
                $stmt = $conn->prepare("
                    INSERT INTO messages (sender_id, receiver_id, message_type, file_name, file_size, file_type, created_at, is_read) 
                    VALUES (:sender_id, :receiver_id, 'file', :file_name, :file_size, :file_type, NOW(), FALSE)
                ");
                
                $result = $stmt->execute([
                    'sender_id' => $my_profile_id,
                    'receiver_id' => $receiver_id,
                    'file_name' => $filename,
                    'file_size' => $file['size'],
                    'file_type' => $file['type']
                ]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'File sent successfully'
                    ]);
                } else {
                    // Clean up file if database insert fails
                    unlink(FILE_UPLOAD_DIR . $filename);
                    throw new Exception('Failed to save file message');
                }
            } else {
                throw new Exception('Failed to upload file');
            }
            break;
            
        case 'get_messages':
            $other_user_id = $_POST['user_id'] ?? null;
            
            if (!$other_user_id) {
                throw new Exception('Missing user ID');
            }
            
            // Get messages between current user and other user with reply information
            // Order by pinned status first (pinned messages at top), then by creation date
            $stmt = $conn->prepare("
                SELECT m.*, 
                       u.name as sender_name,
                       u.avatar as sender_avatar,
                       rm.message as reply_message,
                       rm.message_type as reply_message_type,
                       rm.file_name as reply_file_name,
                       ru.name as reply_sender_name
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                LEFT JOIN messages rm ON m.reply_to_id = rm.id
                LEFT JOIN users ru ON rm.sender_id = ru.id
                WHERE (m.sender_id = :user1 AND m.receiver_id = :user2)
                   OR (m.sender_id = :user2 AND m.receiver_id = :user1)
                ORDER BY m.is_pinned DESC, m.created_at ASC
                LIMIT 50
            ");
            
            $stmt->execute([
                'user1' => $my_profile_id,
                'user2' => $other_user_id
            ]);
            
            $messages = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            // Mark messages as read
            $update_stmt = $conn->prepare("
                UPDATE messages 
                SET is_read = TRUE 
                WHERE sender_id = :sender_id 
                AND receiver_id = :receiver_id 
                AND is_read = FALSE
            ");
            
            $update_stmt->execute([
                'sender_id' => $other_user_id,
                'receiver_id' => $my_profile_id
            ]);
            
            // Format messages for response
            $formatted_messages = array_map(function($message) {
                // Generate file path based on message type
                $file_path = null;
                if ($message->message_type === 'voice') {
                    $file_path = VOICE_UPLOAD_DIR . $message->file_name;
                } elseif ($message->message_type === 'file') {
                    $file_path = FILE_UPLOAD_DIR . $message->file_name;
                }
                
                // Format reply information
                $reply_info = null;
                if ($message->reply_to_id) {
                    $reply_content = $message->reply_message;
                    if ($message->reply_message_type === 'voice') {
                        $reply_content = 'Voice message';
                    } elseif ($message->reply_message_type === 'file') {
                        $reply_content = 'File: ' . $message->reply_file_name;
                    }
                    
                    $reply_info = [
                        'id' => $message->reply_to_id,
                        'sender_name' => $message->reply_sender_name,
                        'content' => $reply_content
                    ];
                }
                
                return [
                    'id' => $message->id,
                    'content' => $message->message,
                    'message_type' => $message->message_type,
                    'file_name' => $message->file_name,
                    'file_path' => $file_path,
                    'file_size' => $message->file_size,
                    'file_type' => $message->file_type,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender_name,
                    'sender_avatar' => $message->sender_avatar,
                    'created_at' => $message->created_at,
                    'is_sent' => $message->sender_id == $my_profile_id,
                    'is_pinned' => (bool)$message->is_pinned,
                    'reply_to_id' => $message->reply_to_id,
                    'reply_to_message' => $reply_info
                ];
            }, $messages);
            
            echo json_encode([
                'success' => true,
                'messages' => $formatted_messages
            ]);
            break;
            
        case 'mark_read':
            $sender_id = $_POST['sender_id'] ?? null;
            
            if (!$sender_id) {
                throw new Exception('Missing sender ID');
            }
            
            $stmt = $conn->prepare("
                UPDATE messages 
                SET is_read = TRUE 
                WHERE sender_id = :sender_id 
                AND receiver_id = :receiver_id 
                AND is_read = FALSE
            ");
            
            $result = $stmt->execute([
                'sender_id' => $sender_id,
                'receiver_id' => $my_profile_id
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Messages marked as read'
                ]);
            } else {
                throw new Exception('Failed to mark messages as read');
            }
            break;
            
        case 'pin_message':
            $message_id = $_POST['message_id'] ?? null;
            
            if (!$message_id) {
                throw new Exception('Missing message ID');
            }
            
            // Check if message exists and user has permission to pin it
            $stmt = $conn->prepare("
                SELECT id, sender_id, receiver_id 
                FROM messages 
                WHERE id = :message_id 
                AND (sender_id = :user_id OR receiver_id = :user_id)
            ");
            
            $stmt->execute([
                'message_id' => $message_id,
                'user_id' => $my_profile_id
            ]);
            
            $message = $stmt->fetch(PDO::FETCH_OBJ);
            
            if (!$message) {
                throw new Exception('Message not found or access denied');
            }
            
            // Pin the message
            $update_stmt = $conn->prepare("
                UPDATE messages 
                SET is_pinned = TRUE 
                WHERE id = :message_id
            ");
            
            $result = $update_stmt->execute([
                'message_id' => $message_id
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Message pinned successfully'
                ]);
            } else {
                throw new Exception('Failed to pin message');
            }
            break;
            
        case 'unpin_message':
            $message_id = $_POST['message_id'] ?? null;
            
            if (!$message_id) {
                throw new Exception('Missing message ID');
            }
            
            // Check if message exists and user has permission to unpin it
            $stmt = $conn->prepare("
                SELECT id, sender_id, receiver_id 
                FROM messages 
                WHERE id = :message_id 
                AND (sender_id = :user_id OR receiver_id = :user_id)
            ");
            
            $stmt->execute([
                'message_id' => $message_id,
                'user_id' => $my_profile_id
            ]);
            
            $message = $stmt->fetch(PDO::FETCH_OBJ);
            
            if (!$message) {
                throw new Exception('Message not found or access denied');
            }
            
            // Unpin the message
            $update_stmt = $conn->prepare("
                UPDATE messages 
                SET is_pinned = FALSE 
                WHERE id = :message_id
            ");
            
            $result = $update_stmt->execute([
                'message_id' => $message_id
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Message unpinned successfully'
                ]);
            } else {
                throw new Exception('Failed to unpin message');
            }
            break;
            
        case 'get_pinned_messages':
            $other_user_id = $_POST['user_id'] ?? null;
            
            if (!$other_user_id) {
                throw new Exception('Missing user ID');
            }
            
            // Get pinned messages between current user and other user
            $stmt = $conn->prepare("
                SELECT m.*, 
                       u.name as sender_name,
                       u.avatar as sender_avatar
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                WHERE ((m.sender_id = :user1 AND m.receiver_id = :user2)
                   OR (m.sender_id = :user2 AND m.receiver_id = :user1))
                AND m.is_pinned = TRUE
                ORDER BY m.created_at DESC
            ");
            
            $stmt->execute([
                'user1' => $my_profile_id,
                'user2' => $other_user_id
            ]);
            
            $pinned_messages = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            // Format pinned messages for response
            $formatted_pinned_messages = array_map(function($message) {
                // Generate file path based on message type
                $file_path = null;
                if ($message->message_type === 'voice') {
                    $file_path = VOICE_UPLOAD_DIR . $message->file_name;
                } elseif ($message->message_type === 'file') {
                    $file_path = FILE_UPLOAD_DIR . $message->file_name;
                }
                
                return [
                    'id' => $message->id,
                    'content' => $message->message,
                    'message_type' => $message->message_type,
                    'file_name' => $message->file_name,
                    'file_path' => $file_path,
                    'file_size' => $message->file_size,
                    'file_type' => $message->file_type,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender_name,
                    'sender_avatar' => $message->sender_avatar,
                    'created_at' => $message->created_at,
                    'is_sent' => $message->sender_id == $my_profile_id
                ];
            }, $pinned_messages);
            
            echo json_encode([
                'success' => true,
                'pinned_messages' => $formatted_pinned_messages,
                'count' => count($formatted_pinned_messages)
            ]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 