<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->prepare("
        SELECT 
            tc.id as checklist_id,
            tc.content,
            tc.start_date,
            tc.end_date,
            tc.start_time,
            tc.end_time,
            t.id as task_id,
            t.title as task_title,
            t.color as task_color
        FROM tasks_checklists tc
        JOIN tasks t ON tc.task_id = t.id
        WHERE tc.start_date IS NOT NULL
        AND tc.start_date != '0000-00-00'
    ");
    
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formattedEvents = array_map(function($event) {
        $startDateTime = $event['start_date'] . 'T' . ($event['start_time'] ?? '00:00:00');
        $endDateTime = $event['end_date'] ? $event['end_date'] . 'T' . ($event['end_time'] ?? '23:59:59') : null;

        return [
            'id' => 'checklist-' . $event['checklist_id'],
            'title' => $event['content'],
            'start' => $startDateTime,
            'end' => $endDateTime,
            'color' => $event['task_color'] ?: '#3788d8',
            'extendedProps' => [
                'checklistId' => $event['checklist_id'],
                'taskId' => $event['task_id'],
                'taskTitle' => $event['task_title']
            ]
        ];
    }, $events);

    echo json_encode($formattedEvents);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 