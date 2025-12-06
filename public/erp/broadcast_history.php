<?php include 'views/headin2.php' ?>
<?php require_once 'controlls/db/functions.php' ?>

<div class="dashboard-container">
    <?php include 'views/header-sidebar.php' ?>
    
    <!-- Broadcast History -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Broadcast History</h4>
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="historyType" id="meetingsHistory" value="meetings" checked>
                <label class="btn btn-outline-primary" for="meetingsHistory">
                    <i class="fas fa-calendar-alt me-2"></i>Meetings
                </label>
                
                <input type="radio" class="btn-check" name="historyType" id="votingHistory" value="voting">
                <label class="btn btn-outline-primary" for="votingHistory">
                    <i class="fas fa-poll me-2"></i>Voting Polls
                </label>
            </div>
        </div>
        <div class="card-body">
            <!-- Meetings History -->
            <div id="meetingsHistoryContent">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date & Time</th>
                                <th>Creator</th>
                                <th>Broadcast Type</th>
                                <th>Responses</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("
                                SELECT m.*, 
                                       COUNT(mr.id) as total_responses,
                                       SUM(CASE WHEN mr.response = 'accept' THEN 1 ELSE 0 END) as accepted,
                                       SUM(CASE WHEN mr.response = 'decline' THEN 1 ELSE 0 END) as declined
                                FROM meetings m 
                                LEFT JOIN meeting_responses mr ON m.id = mr.meeting_id 
                                GROUP BY m.id 
                                ORDER BY m.created_at DESC
                            ");
                            $stmt->execute();
                            while ($meeting = $stmt->fetch(PDO::FETCH_OBJ)) {
                                echo "<tr>";
                                echo "<td><strong>{$meeting->title}</strong>";
                                if (!empty($meeting->description)) {
                                    echo "<br><small class='text-muted'>" . substr($meeting->description, 0, 50) . (strlen($meeting->description) > 50 ? '...' : '') . "</small>";
                                }
                                echo "</td>";
                                echo "<td>{$meeting->meeting_date}<br><small class='text-muted'>{$meeting->meeting_time}</small></td>";
                                echo "<td>{$meeting->creator_name}</td>";
                                echo "<td><span class='badge bg-" . ($meeting->broadcast_type === 'all' ? 'success' : 'warning') . "'>{$meeting->broadcast_type}</span></td>";
                                echo "<td>";
                                echo "<small>Total: {$meeting->total_responses}</small><br>";
                                echo "<small class='text-success'>✅ {$meeting->accepted}</small><br>";
                                echo "<small class='text-danger'>❌ {$meeting->declined}</small>";
                                echo "</td>";
                                echo "<td><small>" . date('Y-m-d H:i', strtotime($meeting->created_at)) . "</small></td>";
                                echo "<td>";
                                echo "<button class='btn btn-sm btn-outline-primary' onclick='viewMeetingDetails({$meeting->id})'>";
                                echo "<i class='fas fa-eye'></i> View";
                                echo "</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Voting History -->
            <div id="votingHistoryContent" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Options</th>
                                <th>Broadcast Type</th>
                                <th>Total Votes</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("
                                SELECT vp.*, 
                                       COUNT(DISTINCT vo.id) as total_options,
                                       COUNT(vr.id) as total_votes
                                FROM voting_polls vp 
                                LEFT JOIN voting_options vo ON vp.id = vo.poll_id 
                                LEFT JOIN voting_responses vr ON vp.id = vr.poll_id 
                                GROUP BY vp.id 
                                ORDER BY vp.created_at DESC
                            ");
                            $stmt->execute();
                            while ($poll = $stmt->fetch(PDO::FETCH_OBJ)) {
                                echo "<tr>";
                                echo "<td><strong>{$poll->title}</strong></td>";
                                echo "<td>" . (!empty($poll->description) ? substr($poll->description, 0, 50) . (strlen($poll->description) > 50 ? '...' : '') : '<em>No description</em>') . "</td>";
                                echo "<td>{$poll->total_options} options</td>";
                                echo "<td><span class='badge bg-" . ($poll->broadcast_type === 'all' ? 'success' : 'warning') . "'>{$poll->broadcast_type}</span></td>";
                                echo "<td>{$poll->total_votes} votes</td>";
                                echo "<td><small>" . date('Y-m-d H:i', strtotime($poll->created_at)) . "</small></td>";
                                echo "<td>";
                                echo "<button class='btn btn-sm btn-outline-primary' onclick='viewVotingDetails({$poll->id})'>";
                                echo "<i class='fas fa-eye'></i> View";
                                echo "</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Meeting Details Modal -->
<div class="modal fade" id="meetingDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Meeting Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="meetingDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Voting Details Modal -->
<div class="modal fade" id="votingDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Voting Poll Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="votingDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // History type switching
    const meetingsHistory = document.getElementById('meetingsHistory');
    const votingHistory = document.getElementById('votingHistory');
    const meetingsContent = document.getElementById('meetingsHistoryContent');
    const votingContent = document.getElementById('votingHistoryContent');

    function switchHistory() {
        if (meetingsHistory.checked) {
            meetingsContent.style.display = 'block';
            votingContent.style.display = 'none';
        } else {
            meetingsContent.style.display = 'none';
            votingContent.style.display = 'block';
        }
    }

    meetingsHistory.addEventListener('change', switchHistory);
    votingHistory.addEventListener('change', switchHistory);
});

// View meeting details
function viewMeetingDetails(meetingId) {
    fetch(`get_meeting_details.php?id=${meetingId}&format=html`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('meetingDetailsContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('meetingDetailsModal')).show();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Failed to load meeting details', 'error');
        });
}

// View voting details
function viewVotingDetails(pollId) {
    fetch(`get_voting_details.php?id=${pollId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('votingDetailsContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('votingDetailsModal')).show();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Failed to load voting details', 'error');
        });
}
</script>

<?php include 'views/footer-dashboard.php' ?> 