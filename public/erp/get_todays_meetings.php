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
    // Get today's date in Sweden timezone
    $today = date('Y-m-d');
    
    // Get meetings scheduled for today
    $stmt = $conn->prepare("
        SELECT 
            m.id,
            m.title,
            m.meeting_date,
            m.meeting_time,
            m.creator_name,
            m.description,
            m.broadcast_type,
            m.created_at,
            COUNT(mr.id) as response_count,
            SUM(CASE WHEN mr.response = 'accept' THEN 1 ELSE 0 END) as accept_count,
            SUM(CASE WHEN mr.response = 'decline' THEN 1 ELSE 0 END) as decline_count
        FROM meetings m
        LEFT JOIN meeting_responses mr ON m.id = mr.meeting_id
        WHERE m.meeting_date = :today
        GROUP BY m.id
        ORDER BY m.meeting_time ASC
    ");
    
    $stmt->execute(['today' => $today]);
    $meetings = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Format the data and add login button logic
    $formatted_meetings = [];
    $current_time = time(); // Now in Sweden timezone
    
    foreach ($meetings as $meeting) {
        // Create meeting datetime in Sweden timezone
        $meeting_datetime = strtotime($meeting->meeting_date . ' ' . $meeting->meeting_time);
        $three_minutes_before = $meeting_datetime - (3 * 60); // 3 minutes before
        $meeting_end = $meeting_datetime + (60 * 60); // 1 hour after start
        
        // Determine button state based on Sweden time
        $button_state = 'disabled';
        $button_text = 'Meeting Not Started';
        $button_class = 'btn-secondary';
        
        if ($current_time >= $three_minutes_before && $current_time < $meeting_datetime) {
            $button_state = 'enabled';
            $button_text = 'Join Meeting';
            $button_class = 'btn-success';
        } elseif ($current_time >= $meeting_datetime && $current_time < $meeting_end) {
            $button_state = 'enabled';
            $button_text = 'Join Meeting';
            $button_class = 'btn-primary';
        } elseif ($current_time >= $meeting_end) {
            $button_state = 'disabled';
            $button_text = 'Meeting Ended';
            $button_class = 'btn-secondary';
        }
        
        // Calculate time remaining
        $time_remaining = '';
        if ($current_time < $meeting_datetime) {
            $remaining_seconds = $meeting_datetime - $current_time;
            $hours = floor($remaining_seconds / 3600);
            $minutes = floor(($remaining_seconds % 3600) / 60);
            
            if ($hours > 0) {
                $time_remaining = "in {$hours}h {$minutes}m";
            } else {
                $time_remaining = "in {$minutes}m";
            }
        }
        
        $formatted_meetings[] = [
            'id' => $meeting->id,
            'title' => $meeting->title,
            'date' => $meeting->meeting_date,
            'time' => $meeting->meeting_time,
            'creator' => $meeting->creator_name,
            'description' => $meeting->description,
            'broadcast_type' => $meeting->broadcast_type,
            'created_at' => $meeting->created_at,
            'response_count' => (int)$meeting->response_count,
            'accept_count' => (int)$meeting->accept_count,
            'decline_count' => (int)$meeting->decline_count,
            'button_state' => $button_state,
            'button_text' => $button_text,
            'button_class' => $button_class,
            'time_remaining' => $time_remaining,
            'meeting_datetime' => $meeting_datetime,
            'three_minutes_before' => $three_minutes_before,
            'meeting_end' => $meeting_end
        ];
    }
    
    echo json_encode([
        'success' => true,
        'meetings' => $formatted_meetings,
        'current_time' => $current_time,
        'timezone' => 'Europe/Stockholm'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 