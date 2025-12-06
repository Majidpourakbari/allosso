<?php include 'views/headin2.php' ?>
<?php require_once 'controlls/db/functions.php' ?>

<div class="dashboard-container">
    <?php include 'views/header-sidebar.php' ?>
    
    <!-- Broadcast History Link -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Broadcast Messages</h2>
        <div>
            <a href="drafts" class="btn btn-outline-secondary me-2">
                <i class="fas fa-save me-2"></i>View Drafts
            </a>
            <a href="broadcast_history" class="btn btn-outline-secondary">
                <i class="fas fa-history me-2"></i>View Broadcast History
            </a>
        </div>
    </div>
    
    <!-- Message Type Selection -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">Broadcast Message Type</h4>
        </div>
        <div class="card-body">
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="messageType" id="meetingType" value="meeting" checked>
                <label class="btn btn-outline-primary" for="meetingType">
                    <i class="fas fa-calendar-alt me-2"></i>Meeting Info
                </label>
                
                <input type="radio" class="btn-check" name="messageType" id="votingType" value="voting">
                <label class="btn btn-outline-primary" for="votingType">
                    <i class="fas fa-poll me-2"></i>Voting
                </label>
            </div>
        </div>
    </div>

    <!-- Meeting Info Form -->
    <div class="card" id="meetingForm">
        <div class="card-header">
            <h4 class="card-title">Send Meeting Information</h4>
        </div>
        <div class="card-body">
            <form id="meetingBroadcastForm" class="needs-validation" novalidate>
                <input type="hidden" name="messageType" value="meeting">
                
                <div class="mb-3">
                    <label for="meetingBroadcastType" class="form-label">Broadcast Type</label>
                    <select class="form-select" id="meetingBroadcastType" name="broadcastType" required>
                        <option value="">Select broadcast type</option>
                        <option value="all">All Users</option>
                        <option value="selected">Selected Users</option>
                    </select>
                </div>

                <div class="mb-3" id="meetingUserSelection" style="display: none;">
                    <label for="meetingUsers" class="form-label">Select Users</label>
                    <select class="form-select" id="meetingUsers" name="users[]" multiple>
                        <?php
                        $stmt = $conn->prepare("SELECT id, name, chat_id FROM users");
                        $stmt->execute();
                        while ($user = $stmt->fetch(PDO::FETCH_OBJ)) {
                            echo "<option value='{$user->chat_id}'>{$user->name}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="meetingTitle" class="form-label">Meeting Title</label>
                            <input type="text" class="form-control" id="meetingTitle" name="meetingTitle" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="meetingCreator" class="form-label">Creator Name</label>
                            <input type="text" class="form-control" id="meetingCreator" name="meetingCreator" value="<?php echo $my_profile_name ?? ''; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="meetingDate" class="form-label">Meeting Date</label>
                            <input type="date" class="form-control" id="meetingDate" name="meetingDate" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="meetingTime" class="form-label">Meeting Time</label>
                            <input type="time" class="form-control" id="meetingTime" name="meetingTime" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="meetingDescription" class="form-label">Additional Description (Optional)</label>
                    <textarea class="form-control" id="meetingDescription" name="meetingDescription" rows="3"></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="saveMeetingDraft()">
                        <i class="fas fa-save me-2"></i>Save Draft
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Meeting Info
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Voting Form -->
    <div class="card" id="votingForm" style="display: none;">
        <div class="card-header">
            <h4 class="card-title">Send Voting Poll</h4>
        </div>
        <div class="card-body">
            <form id="votingBroadcastForm" class="needs-validation" novalidate>
                <input type="hidden" name="messageType" value="voting">
                
                <div class="mb-3">
                    <label for="votingBroadcastType" class="form-label">Broadcast Type</label>
                    <select class="form-select" id="votingBroadcastType" name="broadcastType" required>
                        <option value="">Select broadcast type</option>
                        <option value="all">All Users</option>
                        <option value="selected">Selected Users</option>
                    </select>
                </div>

                <div class="mb-3" id="votingUserSelection" style="display: none;">
                    <label for="votingUsers" class="form-label">Select Users</label>
                    <select class="form-select" id="votingUsers" name="users[]" multiple>
                        <?php
                        $stmt = $conn->prepare("SELECT id, name, chat_id FROM users WHERE chat_id IS NOT NULL");
                        $stmt->execute();
                        while ($user = $stmt->fetch(PDO::FETCH_OBJ)) {
                            echo "<option value='{$user->chat_id}'>{$user->name}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="votingTitle" class="form-label">Voting Title</label>
                    <input type="text" class="form-control" id="votingTitle" name="votingTitle" required>
                </div>

                <div class="mb-3">
                    <label for="votingDescription" class="form-label">Voting Description (Optional)</label>
                    <textarea class="form-control" id="votingDescription" name="votingDescription" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Voting Options</label>
                    <div id="votingOptions">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="votingOptions[]" placeholder="Option 1" required>
                            <button type="button" class="btn btn-outline-danger remove-option" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="votingOptions[]" placeholder="Option 2" required>
                            <button type="button" class="btn btn-outline-danger remove-option" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addVotingOption">
                        <i class="fas fa-plus me-1"></i>Add Option
                    </button>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="saveVotingDraft()">
                        <i class="fas fa-save me-2"></i>Save Draft
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-poll me-2"></i>Send Voting Poll
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Drafts Section -->
    <div class="card mt-4" id="draftsSection" style="display: none;">
        <div class="card-header">
            <h4 class="card-title">Saved Drafts</h4>
        </div>
        <div class="card-body">
            <div class="btn-group mb-3" role="group">
                <input type="radio" class="btn-check" name="draftType" id="meetingDrafts" value="meetings" checked>
                <label class="btn btn-outline-primary" for="meetingDrafts">
                    <i class="fas fa-calendar-alt me-2"></i>Meeting Drafts
                </label>
                
                <input type="radio" class="btn-check" name="draftType" id="votingDrafts" value="voting">
                <label class="btn btn-outline-primary" for="votingDrafts">
                    <i class="fas fa-poll me-2"></i>Voting Drafts
                </label>
            </div>

            <div id="meetingDraftsContent">
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
                            <!-- Meeting drafts will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="votingDraftsContent" style="display: none;">
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
                            <!-- Voting drafts will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Message type switching
    const meetingType = document.getElementById('meetingType');
    const votingType = document.getElementById('votingType');
    const meetingForm = document.getElementById('meetingForm');
    const votingForm = document.getElementById('votingForm');

    function switchForm() {
        if (meetingType.checked) {
            meetingForm.style.display = 'block';
            votingForm.style.display = 'none';
        } else {
            meetingForm.style.display = 'none';
            votingForm.style.display = 'block';
        }
    }

    meetingType.addEventListener('change', switchForm);
    votingType.addEventListener('change', switchForm);

    // Meeting form handling
    const meetingBroadcastType = document.getElementById('meetingBroadcastType');
    const meetingUserSelection = document.getElementById('meetingUserSelection');
    const meetingUsers = document.getElementById('meetingUsers');
    const meetingBroadcastForm = document.getElementById('meetingBroadcastForm');

    meetingBroadcastType.addEventListener('change', function() {
        meetingUserSelection.style.display = this.value === 'selected' ? 'block' : 'none';
        if (this.value === 'selected') {
            meetingUsers.setAttribute('required', 'required');
        } else {
            meetingUsers.removeAttribute('required');
        }
    });

    // Voting form handling
    const votingBroadcastType = document.getElementById('votingBroadcastType');
    const votingUserSelection = document.getElementById('votingUserSelection');
    const votingUsers = document.getElementById('votingUsers');
    const votingBroadcastForm = document.getElementById('votingBroadcastForm');

    votingBroadcastType.addEventListener('change', function() {
        votingUserSelection.style.display = this.value === 'selected' ? 'block' : 'none';
        if (this.value === 'selected') {
            votingUsers.setAttribute('required', 'required');
        } else {
            votingUsers.removeAttribute('required');
        }
    });

    // Voting options management
    const addVotingOption = document.getElementById('addVotingOption');
    const votingOptions = document.getElementById('votingOptions');

    addVotingOption.addEventListener('click', function() {
        const optionCount = votingOptions.children.length + 1;
        const optionDiv = document.createElement('div');
        optionDiv.className = 'input-group mb-2';
        optionDiv.innerHTML = `
            <input type="text" class="form-control" name="votingOptions[]" placeholder="Option ${optionCount}" required>
            <button type="button" class="btn btn-outline-danger remove-option">
                <i class="fas fa-trash"></i>
            </button>
        `;
        votingOptions.appendChild(optionDiv);
        
        // Show remove buttons if more than 2 options
        if (votingOptions.children.length > 2) {
            document.querySelectorAll('.remove-option').forEach(btn => btn.style.display = 'block');
        }
    });

    votingOptions.addEventListener('click', function(e) {
        if (e.target.closest('.remove-option')) {
            e.target.closest('.input-group').remove();
            
            // Hide remove buttons if only 2 options left
            if (votingOptions.children.length <= 2) {
                document.querySelectorAll('.remove-option').forEach(btn => btn.style.display = 'none');
            }
        }
    });

    // Draft type switching
    const meetingDrafts = document.getElementById('meetingDrafts');
    const votingDrafts = document.getElementById('votingDrafts');
    const meetingDraftsContent = document.getElementById('meetingDraftsContent');
    const votingDraftsContent = document.getElementById('votingDraftsContent');

    function switchDraftType() {
        if (meetingDrafts.checked) {
            meetingDraftsContent.style.display = 'block';
            votingDraftsContent.style.display = 'none';
        } else {
            meetingDraftsContent.style.display = 'none';
            votingDraftsContent.style.display = 'block';
        }
    }

    meetingDrafts.addEventListener('change', switchDraftType);
    votingDrafts.addEventListener('change', switchDraftType);

    // Meeting form submission
    meetingBroadcastForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';

        fetch('send_broadcast.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Meeting information sent successfully',
                    icon: 'success'
                });
                meetingBroadcastForm.reset();
                meetingUserSelection.style.display = 'none';
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to send meeting information',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while sending the meeting information',
                icon: 'error'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Voting form submission
    votingBroadcastForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';

        fetch('send_broadcast.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Voting poll sent successfully',
                    icon: 'success'
                });
                votingBroadcastForm.reset();
                votingUserSelection.style.display = 'none';
                
                // Reset voting options to 2
                votingOptions.innerHTML = `
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="votingOptions[]" placeholder="Option 1" required>
                        <button type="button" class="btn btn-outline-danger remove-option" style="display: none;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="votingOptions[]" placeholder="Option 2" required>
                        <button type="button" class="btn btn-outline-danger remove-option" style="display: none;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to send voting poll',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while sending the voting poll',
                icon: 'error'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});

// Save meeting draft
function saveMeetingDraft() {
    const form = document.getElementById('meetingBroadcastForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const title = formData.get('meetingTitle');
    const date = formData.get('meetingDate');
    const time = formData.get('meetingTime');
    const creator = formData.get('meetingCreator');
    const broadcastType = formData.get('broadcastType');
    
    if (!title || !date || !time || !creator || !broadcastType) {
        Swal.fire({
            title: 'Error!',
            text: 'Please fill in all required fields',
            icon: 'error'
        });
        return;
    }

    fetch('save_draft.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: 'Meeting draft saved successfully',
                icon: 'success'
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to save meeting draft',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while saving the draft',
            icon: 'error'
        });
    });
}

// Save voting draft
function saveVotingDraft() {
    const form = document.getElementById('votingBroadcastForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const title = formData.get('votingTitle');
    const options = formData.getAll('votingOptions[]').filter(option => option.trim() !== '');
    const broadcastType = formData.get('broadcastType');
    
    if (!title || options.length < 2 || !broadcastType) {
        Swal.fire({
            title: 'Error!',
            text: 'Please fill in title, at least 2 options, and broadcast type',
            icon: 'error'
        });
        return;
    }

    fetch('save_draft.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: 'Voting poll draft saved successfully',
                icon: 'success'
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to save voting poll draft',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while saving the draft',
            icon: 'error'
        });
    });
}

// Load drafts
function loadDrafts() {
    const draftsSection = document.getElementById('draftsSection');
    draftsSection.style.display = 'block';
    
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
