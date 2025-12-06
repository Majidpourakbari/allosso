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
    $pollId = $_GET['id'] ?? 0;
    $format = $_GET['format'] ?? 'html'; // 'html' or 'json'
    
    if (empty($pollId)) {
        throw new Exception('Poll ID is required');
    }

    // Get voting poll details
    $stmt = $conn->prepare("SELECT * FROM voting_polls WHERE id = :id");
    $stmt->execute(['id' => $pollId]);
    $poll = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$poll) {
        throw new Exception('Voting poll not found');
    }

    // Get voting options with vote counts
    $stmt = $conn->prepare("
        SELECT vo.*, COUNT(vr.id) as vote_count
        FROM voting_options vo 
        LEFT JOIN voting_responses vr ON vo.id = vr.option_id 
        WHERE vo.poll_id = :poll_id 
        GROUP BY vo.id 
        ORDER BY vo.option_order
    ");
    $stmt->execute(['poll_id' => $pollId]);
    $options = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Get total votes
    $stmt = $conn->prepare("SELECT COUNT(*) as total_votes FROM voting_responses WHERE poll_id = :poll_id");
    $stmt->execute(['poll_id' => $pollId]);
    $totalVotes = $stmt->fetch(PDO::FETCH_OBJ)->total_votes;

    // If format is JSON, return data for voting modal
    if ($format === 'json') {
        $formattedOptions = [];
        foreach ($options as $option) {
            $formattedOptions[] = [
                'id' => $option->id,
                'text' => $option->option_text,
                'option_text' => $option->option_text,
                'order' => $option->option_order,
                'vote_count' => (int)$option->vote_count
            ];
        }
        
        echo json_encode([
            'success' => true,
            'poll' => [
                'id' => $poll->id,
                'title' => $poll->title,
                'description' => $poll->description,
                'broadcast_type' => $poll->broadcast_type,
                'created_at' => $poll->created_at
            ],
            'options' => $formattedOptions,
            'total_votes' => (int)$totalVotes
        ]);
        exit;
    }

    // Get individual votes for HTML format
    $stmt = $conn->prepare("
        SELECT vr.*, vo.option_text 
        FROM voting_responses vr 
        JOIN voting_options vo ON vr.option_id = vo.id 
        WHERE vr.poll_id = :poll_id 
        ORDER BY vr.created_at DESC
    ");
    $stmt->execute(['poll_id' => $pollId]);
    $votes = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Generate HTML
    $html = "
    <div class='row'>
        <div class='col-md-6'>
            <h6>Poll Information</h6>
            <table class='table table-sm'>
                <tr><td><strong>Title:</strong></td><td>{$poll->title}</td></tr>
                <tr><td><strong>Broadcast Type:</strong></td><td><span class='badge bg-" . ($poll->broadcast_type === 'all' ? 'success' : 'warning') . "'>{$poll->broadcast_type}</span></td></tr>
                <tr><td><strong>Total Votes:</strong></td><td>{$totalVotes}</td></tr>
                <tr><td><strong>Created:</strong></td><td>" . date('Y-m-d H:i', strtotime($poll->created_at)) . "</td></tr>
            </table>
        </div>
        <div class='col-md-6'>
            <h6>Vote Results</h6>
            <div class='card'>
                <div class='card-body'>";
    
    foreach ($options as $option) {
        $percentage = $totalVotes > 0 ? round(($option->vote_count / $totalVotes) * 100, 1) : 0;
        $html .= "
                    <div class='mb-2'>
                        <div class='d-flex justify-content-between'>
                            <span>{$option->option_text}</span>
                            <span>{$option->vote_count} votes ({$percentage}%)</span>
                        </div>
                        <div class='progress' style='height: 8px;'>
                            <div class='progress-bar' role='progressbar' style='width: {$percentage}%'></div>
                        </div>
                    </div>";
    }
    
    $html .= "
                </div>
            </div>
        </div>
    </div>";

    if (!empty($poll->description)) {
        $html .= "
        <div class='row mt-3'>
            <div class='col-12'>
                <h6>Description</h6>
                <div class='alert alert-light'>{$poll->description}</div>
            </div>
        </div>";
    }

    if (!empty($votes)) {
        $html .= "
        <div class='row mt-3'>
            <div class='col-12'>
                <h6>Individual Votes</h6>
                <div class='table-responsive'>
                    <table class='table table-sm'>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Vote</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>";
        
        foreach ($votes as $vote) {
            $html .= "
                            <tr>
                                <td>{$vote->user_name}</td>
                                <td><span class='text-primary'>🗳️ {$vote->option_text}</span></td>
                                <td><small>" . date('Y-m-d H:i', strtotime($vote->created_at)) . "</small></td>
                            </tr>";
        }
        
        $html .= "
                        </tbody>
                    </table>
                </div>
            </div>
        </div>";
    } else {
        $html .= "
        <div class='row mt-3'>
            <div class='col-12'>
                <div class='alert alert-info'>No votes yet.</div>
            </div>
        </div>";
    }

    echo json_encode([
        'success' => true,
        'html' => $html
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 