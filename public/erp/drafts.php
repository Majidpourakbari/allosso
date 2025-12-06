<?php include 'views/headin2.php' ?>
<?php require_once 'controlls/db/functions.php' ?>

<div class="dashboard-container">
    <?php include 'views/header-sidebar.php' ?>
    
    <!-- Drafts Management -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Draft Management</h2>
        <a href="broadcast" class="btn btn-outline-primary">
            <i class="fas fa-plus me-2"></i>Create New Broadcast
        </a>
    </div>
    
    <!-- Draft Type Selection -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">Draft Types</h4>
        </div>
        <div class="card-body">
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="draftType" id="meetingDrafts" value="meetings" checked>
                <label class="btn btn-outline-primary" for="meetingDrafts">
                    <i class="fas fa-calendar-alt me-2"></i>Meeting Drafts
                </label>
                
                <input type="radio" class="btn-check" name="draftType" id="votingDrafts" value="voting">
                <label class="btn btn-outline-primary" for="votingDrafts">
                    <i class="fas fa-poll me-2"></i>Voting Drafts
                </label>
            </div>
        </div>
    </div>

    <!-- Meeting Drafts -->
    <div class="card" id="meetingDraftsSection">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-calendar-alt me-2"></i>Meeting Drafts
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date & Time</th>
                            <th>Creator</th>
                            <th>Broadcast Type</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="meetingDraftsTable">
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Voting Drafts -->
    <div class="card" id="votingDraftsSection" style="display: none;">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-poll me-2"></i>Voting Poll Drafts
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Options</th>
                            <th>Broadcast Type</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="votingDraftsTable">
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load drafts on page load
    loadDrafts();
    
    // Draft type switching
    const meetingDrafts = document.getElementById('meetingDrafts');
    const votingDrafts = document.getElementById('votingDrafts');
    const meetingDraftsSection = document.getElementById('meetingDraftsSection');
    const votingDraftsSection = document.getElementById('votingDraftsSection');

    function switchDraftType() {
        if (meetingDrafts.checked) {
            meetingDraftsSection.style.display = 'block';
            votingDraftsSection.style.display = 'none';
        } else {
            meetingDraftsSection.style.display = 'none';
            votingDraftsSection.style.display = 'block';
        }
    }

    meetingDrafts.addEventListener('change', switchDraftType);
    votingDrafts.addEventListener('change', switchDraftType);
});

// Load drafts
function loadDrafts() {
    fetch('get_drafts.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMeetingDrafts(data.meeting_drafts);
                displayVotingDrafts(data.voting_drafts);
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to load drafts',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while loading drafts',
                icon: 'error'
            });
        });
}

// Display meeting drafts
function displayMeetingDrafts(drafts) {
    const table = document.getElementById('meetingDraftsTable');
    
    if (drafts.length === 0) {
        table.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No meeting drafts found</td></tr>';
        return;
    }
    
    table.innerHTML = drafts.map(draft => `
        <tr>
            <td><strong>${draft.title}</strong></td>
            <td>${draft.date} at ${draft.time}</td>
            <td>${draft.creator}</td>
            <td><span class="badge bg-${draft.broadcast_type === 'all' ? 'success' : 'warning'}">${draft.broadcast_type}</span></td>
            <td><small>${new Date(draft.created_at).toLocaleString()}</small></td>
            <td>
                <button class="btn btn-sm btn-success" onclick="sendDraft(${draft.id}, 'meeting')">
                    <i class="fas fa-paper-plane"></i> Send
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteDraft(${draft.id}, 'meeting')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </td>
        </tr>
    `).join('');
}

// Display voting drafts
function displayVotingDrafts(drafts) {
    const table = document.getElementById('votingDraftsTable');
    
    if (drafts.length === 0) {
        table.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No voting poll drafts found</td></tr>';
        return;
    }
    
    table.innerHTML = drafts.map(draft => `
        <tr>
            <td><strong>${draft.title}</strong></td>
            <td>${draft.description || '<em>No description</em>'}</td>
            <td>${draft.option_count} options</td>
            <td><span class="badge bg-${draft.broadcast_type === 'all' ? 'success' : 'warning'}">${draft.broadcast_type}</span></td>
            <td><small>${new Date(draft.created_at).toLocaleString()}</small></td>
            <td>
                <button class="btn btn-sm btn-success" onclick="sendDraft(${draft.id}, 'voting')">
                    <i class="fas fa-paper-plane"></i> Send
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteDraft(${draft.id}, 'voting')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </td>
        </tr>
    `).join('');
}

// Send draft
function sendDraft(draftId, messageType) {
    Swal.fire({
        title: 'Send Draft?',
        text: 'Are you sure you want to send this draft?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Send',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('draftId', draftId);
            formData.append('messageType', messageType);
            
            fetch('send_draft.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Draft sent successfully',
                        icon: 'success'
                    });
                    loadDrafts(); // Refresh drafts list
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to send draft',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while sending the draft',
                    icon: 'error'
                });
            });
        }
    });
}

// Delete draft
function deleteDraft(draftId, messageType) {
    Swal.fire({
        title: 'Delete Draft?',
        text: 'Are you sure you want to delete this draft? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            // TODO: Implement delete draft functionality
            Swal.fire({
                title: 'Not Implemented',
                text: 'Delete functionality will be implemented in the next update',
                icon: 'info'
            });
        }
    });
}
</script>

<?php include 'views/footer-dashboard.php' ?> 