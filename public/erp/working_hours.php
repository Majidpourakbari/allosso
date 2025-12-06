<?php
session_start();
require_once 'controlls/db/functions.php';

// Set JSON header
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to convert ISO datetime to MySQL datetime format
function convertToMySQLDateTime($isoDateTime) {
    try {
        $date = new DateTime($isoDateTime);
        // Convert to UTC for storage
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        throw new Exception('Invalid datetime format: ' . $e->getMessage());
    }
}

// Function to convert MySQL datetime to ISO format
function convertToISODateTime($mysqlDateTime) {
    try {
        $date = new DateTime($mysqlDateTime, new DateTimeZone('UTC'));
        // Convert to local timezone for display
        $date->setTimezone(new DateTimeZone(date_default_timezone_get()));
        return $date->format('c'); // ISO 8601 format
    } catch (Exception $e) {
        throw new Exception('Invalid datetime format: ' . $e->getMessage());
    }
}

// Function to create a notification
function createNotification($conn, $user_id, $message) {
    try {
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, date, time, users_read) VALUES (:user_id, :message, :date, :time, :users_read)");
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
        $result = $stmt->execute([
            'user_id' => $user_id,
            'message' => $message,
            'date' => $current_date,
            'time' => $current_time,
            'users_read' => $user_id
        ]);
        return $result;
    } catch (Exception $e) {
        error_log('Error creating notification: ' . $e->getMessage());
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_SESSION['user_login'])) {
            throw new Exception('User not logged in');
        }

        $user_id = $my_profile_id;
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['start_time']) || !isset($data['end_time'])) {
            throw new Exception('Start time and end time are required');
        }

        // Convert ISO datetime strings to MySQL datetime format
        $start_time = convertToMySQLDateTime($data['start_time']);
        $end_time = convertToMySQLDateTime($data['end_time']);
        $title = isset($data['title']) ? $data['title'] : 'Working Hours';

        // Insert working hours
        $stmt = $conn->prepare("INSERT INTO working_hours (user_id, title, start_time, end_time) VALUES (:user_id, :title, :start_time, :end_time)");
        $result = $stmt->execute([
            'user_id' => $user_id,
            'title' => $title,
            'start_time' => $start_time,
            'end_time' => $end_time
        ]);

        if (!$result) {
            throw new Exception('Failed to insert working hours: ' . implode(', ', $stmt->errorInfo()));
        }

        $inserted_id = $conn->lastInsertId();

        // Create notification for new working hours
        $notification_message = "New working hours added: {$title} from " . date('M d, Y h:i A', strtotime($start_time)) . " to " . date('M d, Y h:i A', strtotime($end_time));
        createNotification($conn, $user_id, $notification_message);

        // Verify the insertion by fetching the newly inserted record
        $verify_stmt = $conn->prepare("SELECT * FROM working_hours WHERE id = :id");
        $verify_stmt->execute(['id' => $inserted_id]);
        $inserted_record = $verify_stmt->fetch(PDO::FETCH_OBJ);

        echo json_encode([
            'success' => true,
            'message' => 'Working hours added successfully',
            'debug' => [
                'inserted_id' => $inserted_id,
                'inserted_record' => $inserted_record
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'debug' => [
                'error_info' => isset($stmt) ? $stmt->errorInfo() : null,
                'data' => isset($data) ? $data : null
            ]
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        if (!isset($_SESSION['user_login'])) {
            throw new Exception('User not logged in');
        }

        $user_id = $my_profile_id;

        // Get working hours
        $stmt = $conn->prepare("SELECT id, title, start_time, end_time FROM working_hours WHERE user_id = :user_id ORDER BY start_time ASC");
        $stmt->execute(['user_id' => $user_id]);
        $working_hours = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Convert MySQL datetime to ISO format
        foreach ($working_hours as &$hour) {
            $hour->start_time = convertToISODateTime($hour->start_time);
            $hour->end_time = convertToISODateTime($hour->end_time);
        }

        echo json_encode([
            'success' => true,
            'data' => $working_hours,
            'debug' => [
                'user_id' => $user_id,
                'count' => count($working_hours)
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'debug' => [
                'error_info' => isset($stmt) ? $stmt->errorInfo() : null
            ]
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        if (!isset($_SESSION['user_login'])) {
            throw new Exception('User not logged in');
        }

        $user_id = $my_profile_id;
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || !isset($data['start_time']) || !isset($data['end_time'])) {
            throw new Exception('ID, start time and end time are required');
        }

        // Convert ISO datetime strings to MySQL datetime format
        $start_time = convertToMySQLDateTime($data['start_time']);
        $end_time = convertToMySQLDateTime($data['end_time']);
        $title = isset($data['title']) ? $data['title'] : 'Working Hours';

        // Update working hours
        $stmt = $conn->prepare("UPDATE working_hours SET title = :title, start_time = :start_time, end_time = :end_time WHERE id = :id AND user_id = :user_id");
        $result = $stmt->execute([
            'id' => $data['id'],
            'user_id' => $user_id,
            'title' => $title,
            'start_time' => $start_time,
            'end_time' => $end_time
        ]);

        if (!$result) {
            throw new Exception('Failed to update working hours: ' . implode(', ', $stmt->errorInfo()));
        }

        if ($stmt->rowCount() === 0) {
            throw new Exception('Working hours not found or you do not have permission to update it');
        }

        // Create notification for updated working hours
        $notification_message = "Working hours updated: {$title} from " . date('M d, Y h:i A', strtotime($start_time)) . " to " . date('M d, Y h:i A', strtotime($end_time));
        createNotification($conn, $user_id, $notification_message);

        echo json_encode([
            'success' => true,
            'message' => 'Working hours updated successfully',
            'debug' => [
                'affected_rows' => $stmt->rowCount()
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'debug' => [
                'error_info' => isset($stmt) ? $stmt->errorInfo() : null,
                'data' => isset($data) ? $data : null
            ]
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        if (!isset($_SESSION['user_login'])) {
            throw new Exception('User not logged in');
        }

        $user_id = $my_profile_id;
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            throw new Exception('Working hours ID is required');
        }

        // Get working hours details before deletion for notification
        $get_stmt = $conn->prepare("SELECT title, start_time, end_time FROM working_hours WHERE id = :id AND user_id = :user_id");
        $get_stmt->execute([
            'id' => $data['id'],
            'user_id' => $user_id
        ]);
        $working_hours = $get_stmt->fetch(PDO::FETCH_OBJ);

        // Delete working hours
        $stmt = $conn->prepare("DELETE FROM working_hours WHERE id = :id AND user_id = :user_id");
        $result = $stmt->execute([
            'id' => $data['id'],
            'user_id' => $user_id
        ]);

        if (!$result) {
            throw new Exception('Failed to delete working hours: ' . implode(', ', $stmt->errorInfo()));
        }

        if ($stmt->rowCount() === 0) {
            throw new Exception('Working hours not found or you do not have permission to delete it');
        }

        // Create notification for deleted working hours
        if ($working_hours) {
            $notification_message = "Working hours deleted: {$working_hours->title} from " . date('M d, Y h:i A', strtotime($working_hours->start_time)) . " to " . date('M d, Y h:i A', strtotime($working_hours->end_time));
            createNotification($conn, $user_id, $notification_message);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Working hours deleted successfully',
            'debug' => [
                'affected_rows' => $stmt->rowCount()
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'debug' => [
                'error_info' => isset($stmt) ? $stmt->errorInfo() : null,
                'data' => isset($data) ? $data : null
            ]
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
} 