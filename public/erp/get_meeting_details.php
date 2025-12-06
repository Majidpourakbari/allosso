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
    $meetingId = $_GET['id'] ?? 0;
    $format = $_GET['format'] ?? 'json'; // 'json' for structured data, 'html' for pre-formatted HTML
    
    if (empty($meetingId)) {
        throw new Exception('Meeting ID is required');
    }

    // Get meeting details
    $stmt = $conn->prepare("SELECT * FROM meetings WHERE id = :id");
    $stmt->execute(['id' => $meetingId]);
    $meeting = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$meeting) {
        throw new Exception('Meeting not found');
    }

    // Get meeting responses
    $stmt = $conn->prepare("SELECT * FROM meeting_responses WHERE meeting_id = :meeting_id ORDER BY created_at DESC");
    $stmt->execute(['meeting_id' => $meetingId]);
    $responses = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Count responses
    $accepted = array_filter($responses, function($r) { return $r->response === 'accept'; });
    $declined = array_filter($responses, function($r) { return $r->response === 'decline'; });

    if ($format === 'html') {
        // Generate HTML for broadcast_history.php
        $html = "
        <div class='row'>
            <div class='col-md-6'>
                <h6>Meeting Information</h6>
                <table class='table table-sm'>
                    <tr><td><strong>Title:</strong></td><td>{$meeting->title}</td></tr>
                    <tr><td><strong>Date:</strong></td><td>{$meeting->meeting_date}</td></tr>
                    <tr><td><strong>Time:</strong></td><td>{$meeting->meeting_time}</td></tr>
                    <tr><td><strong>Creator:</strong></td><td>{$meeting->creator_name}</td></tr>
                    <tr><td><strong>Broadcast Type:</strong></td><td><span class='badge bg-" . ($meeting->broadcast_type === 'all' ? 'success' : 'warning') . "'>{$meeting->broadcast_type}</span></td></tr>
                    <tr><td><strong>Created:</strong></td><td>" . date('Y-m-d H:i', strtotime($meeting->created_at)) . "</td></tr>
                </table>
            </div>
            <div class='col-md-6'>
                <h6>Response Summary</h6>
                <div class='card'>
                    <div class='card-body'>
                        <div class='row text-center'>
                            <div class='col-4'>
                                <div class='border rounded p-2'>
                                    <h4 class='text-primary mb-0'>" . count($responses) . "</h4>
                                    <small class='text-muted'>Total Responses</small>
                                </div>
                            </div>
                            <div class='col-4'>
                                <div class='border rounded p-2'>
                                    <h4 class='text-success mb-0'>" . count($accepted) . "</h4>
                                    <small class='text-muted'>✅ Accepted</small>
                                </div>
                            </div>
                            <div class='col-4'>
                                <div class='border rounded p-2'>
                                    <h4 class='text-danger mb-0'>" . count($declined) . "</h4>
                                    <small class='text-muted'>❌ Declined</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>";

        if (!empty($meeting->description)) {
            $html .= "
            <div class='row mt-3'>
                <div class='col-12'>
                    <h6>Description</h6>
                    <div class='alert alert-light'>{$meeting->description}</div>
                </div>
            </div>";
        }

        if (!empty($responses)) {
            $html .= "
            <div class='row mt-3'>
                <div class='col-12'>
                    <h6>Individual Responses</h6>
                    <div class='table-responsive'>
                        <table class='table table-sm'>
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Response</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>";
            
            foreach ($responses as $response) {
                $responseIcon = $response->response === 'accept' ? '✅' : '❌';
                $responseClass = $response->response === 'accept' ? 'text-success' : 'text-danger';
                $responseText = ucfirst($response->response);
                
                $html .= "
                                <tr>
                                    <td>{$response->user_name}</td>
                                    <td><span class='{$responseClass}'>{$responseIcon} {$responseText}</span></td>
                                    <td><small>" . date('Y-m-d H:i', strtotime($response->created_at)) . "</small></td>
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
                    <div class='alert alert-info'>No responses yet.</div>
                </div>
            </div>";
        }

        echo json_encode([
            'success' => true,
            'html' => $html
        ]);
    } else {
        // Return structured data for tasks.php and dashboard.php
        $formattedResponses = [];
        foreach ($responses as $response) {
            $formattedResponses[] = [
                'user_name' => $response->user_name,
                'response' => $response->response,
                'created_at' => date('Y-m-d H:i', strtotime($response->created_at))
            ];
        }

        echo json_encode([
            'success' => true,
            'meeting' => [
                'title' => $meeting->title,
                'meeting_date' => $meeting->meeting_date,
                'meeting_time' => $meeting->meeting_time,
                'creator_name' => $meeting->creator_name,
                'broadcast_type' => $meeting->broadcast_type,
                'description' => $meeting->description,
                'created_at' => date('Y-m-d H:i', strtotime($meeting->created_at))
            ],
            'total_responses' => count($responses),
            'accepted' => count($accepted),
            'declined' => count($declined),
            'responses' => $formattedResponses
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 