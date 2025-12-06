<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    $events = [];
    
    // Get all tasks with their dates and creator information
    $stmt = $conn->prepare("
        SELECT 
            t.id,
            t.title,
            t.date_start,
            t.date_finish,
            t.time_start,
            t.time_finish,
            t.status,
            t.priority,
            t.category,
            u.name as created_by,
            u.avatar as creator_avatar
        FROM tasks t
        JOIN users u ON t.user_creator = u.id
        WHERE (t.date_start IS NOT NULL OR t.date_finish IS NOT NULL)
        AND t.status != 4
        ORDER BY COALESCE(t.date_start, t.date_finish) ASC
    ");
    
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Format the task events for FullCalendar
    foreach ($tasks as $task) {
        // Determine start date and time
        $startDate = $task->date_start ?: $task->date_finish;
        $startTime = $task->time_start ?: '00:00:00';
        $start = $startDate . 'T' . $startTime;
        
        // Determine end date and time
        $endDate = $task->date_finish ?: $task->date_start;
        $endTime = $task->time_finish ?: '23:59:59';
        $end = $endDate . 'T' . $endTime;
        
        // Set color based on priority and status
        $color = '#007bff'; // default blue
        
        if ($task->status === '3') { // Done
            $color = '#28a745'; // green
        } elseif ($task->status === '2') { // Review
            $color = '#ffc107'; // yellow
        } elseif ($task->status === '4') { // Archived
            $color = '#6c757d'; // gray
        } elseif ($task->status === '1') { // In Progress
            $color = '#17a2b8'; // cyan
        } else { // To Do - use priority colors
            switch ($task->priority) {
                case 'Critical':
                    $color = '#dc3545'; // red
                    break;
                case 'High':
                    $color = '#fd7e14'; // orange
                    break;
                case 'Medium':
                    $color = '#6f42c1'; // purple
                    break;
                case 'Low':
                    $color = '#20c997'; // teal
                    break;
            }
        }
        
        $events[] = [
            'id' => 'task_' . $task->id,
            'title' => $task->title,
            'start' => $start,
            'end' => $end,
            'color' => $color,
            'extendedProps' => [
                'type' => 'task',
                'task_id' => $task->id,
                'task_title' => $task->title,
                'created_by' => $task->created_by,
                'status' => $task->status,
                'priority' => $task->priority,
                'category' => $task->category,
                'creator_avatar' => $task->creator_avatar
            ]
        ];
    }
    
    // Get all meetings with their dates and creator information
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
            u.name as created_by,
            u.avatar as creator_avatar
        FROM meetings m
        LEFT JOIN users u ON m.created_by = u.id
        WHERE m.meeting_date IS NOT NULL
        ORDER BY m.meeting_date ASC
    ");
    
    $stmt->execute();
    $meetings = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Format the meeting events for FullCalendar
    foreach ($meetings as $meeting) {
        // Create start datetime for the meeting
        $start = $meeting->meeting_date . 'T' . $meeting->meeting_time;
        
        // Create end datetime (assume 1 hour duration if not specified)
        $endTime = date('H:i:s', strtotime($meeting->meeting_time . ' +1 hour'));
        
        // Check if adding 1 hour would push the end time to the next day
        $startDateTime = new DateTime($meeting->meeting_date . ' ' . $meeting->meeting_time);
        $endDateTime = clone $startDateTime;
        $endDateTime->add(new DateInterval('PT1H'));
        
        // If the end time would be on a different day, set it to 23:59:59 of the same day
        if ($endDateTime->format('Y-m-d') !== $meeting->meeting_date) {
            $endTime = '23:59:59';
        }
        
        $end = $meeting->meeting_date . 'T' . $endTime;
        
        // Use red color for meetings
        $color = '#dc3545'; // red
        
        $events[] = [
            'id' => 'meeting_' . $meeting->id,
            'title' => '📅 ' . $meeting->title,
            'start' => $start,
            'end' => $end,
            'color' => $color,
            'extendedProps' => [
                'type' => 'meeting',
                'meeting_id' => $meeting->id,
                'meeting_title' => $meeting->title,
                'created_by' => $meeting->created_by ?: $meeting->creator_name,
                'creator_name' => $meeting->creator_name,
                'description' => $meeting->description,
                'broadcast_type' => $meeting->broadcast_type,
                'creator_avatar' => $meeting->creator_avatar
            ]
        ];
    }
    
    echo json_encode($events);
    
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 