<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($my_profile_id)) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

try {
    // Get meeting drafts
    $meetings_stmt = $conn->prepare("
        SELECT 
            id,
            title,
            meeting_date,
            meeting_time,
            creator_name,
            description,
            broadcast_type,
            created_at
        FROM meetings 
        WHERE created_by = ? AND is_draft = TRUE 
        ORDER BY created_at DESC
    ");
    $meetings_stmt->execute([$my_profile_id]);
    $meeting_drafts = $meetings_stmt->fetchAll(PDO::FETCH_OBJ);

    // Get voting poll drafts
    $voting_stmt = $conn->prepare("
        SELECT 
            vp.id,
            vp.title,
            vp.description,
            vp.broadcast_type,
            vp.created_at,
            COUNT(vo.id) as option_count
        FROM voting_polls vp
        LEFT JOIN voting_options vo ON vp.id = vo.poll_id
        WHERE vp.created_by = ? AND vp.is_draft = TRUE 
        GROUP BY vp.id
        ORDER BY vp.created_at DESC
    ");
    $voting_stmt->execute([$my_profile_id]);
    $voting_drafts = $voting_stmt->fetchAll(PDO::FETCH_OBJ);

    // Format meeting drafts
    $formatted_meetings = [];
    foreach ($meeting_drafts as $meeting) {
        $formatted_meetings[] = [
            'id' => $meeting->id,
            'title' => $meeting->title,
            'date' => $meeting->meeting_date,
            'time' => $meeting->meeting_time,
            'creator' => $meeting->creator_name,
            'description' => $meeting->description,
            'broadcast_type' => $meeting->broadcast_type,
            'created_at' => $meeting->created_at,
            'type' => 'meeting'
        ];
    }

    // Format voting drafts
    $formatted_voting = [];
    foreach ($voting_drafts as $poll) {
        // Get voting options for this poll
        $options_stmt = $conn->prepare("
            SELECT 
                vo.id,
                vo.option_text,
                vo.option_order
            FROM voting_options vo
            WHERE vo.poll_id = ?
            ORDER BY vo.option_order
        ");
        $options_stmt->execute([$poll->id]);
        $options = $options_stmt->fetchAll(PDO::FETCH_OBJ);

        $formatted_options = [];
        foreach ($options as $option) {
            $formatted_options[] = [
                'id' => $option->id,
                'text' => $option->option_text,
                'order' => $option->option_order
            ];
        }

        $formatted_voting[] = [
            'id' => $poll->id,
            'title' => $poll->title,
            'description' => $poll->description,
            'broadcast_type' => $poll->broadcast_type,
            'created_at' => $poll->created_at,
            'option_count' => (int)$poll->option_count,
            'options' => $formatted_options,
            'type' => 'voting'
        ];
    }

    echo json_encode([
        'success' => true,
        'meeting_drafts' => $formatted_meetings,
        'voting_drafts' => $formatted_voting
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 