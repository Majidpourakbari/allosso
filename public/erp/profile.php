<?php include 'views/headin2.php' ?>

<?php
// Fetch current user data
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $my_profile_id]);
    $current_user = $stmt->fetch(PDO::FETCH_OBJ);
} catch(Exception $e) {
    echo "Error fetching user data: " . $e->getMessage();
}
?>

<div class="dashboard-container">
    <?php include 'views/header-sidebar.php' ?>
    
    <div class="profile-container">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                    <i class="fas fa-user me-2"></i>Profile Information
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="working-hours-tab" data-bs-toggle="tab" data-bs-target="#working-hours" type="button" role="tab" aria-controls="working-hours" aria-selected="false">
                    <i class="fas fa-clock me-2"></i>Working Hours
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="profileTabsContent">
            <!-- Profile Information Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="avatar-container mb-4">
                                    <img id="avatar-preview" src="uploads/profiles/<?php echo isset($current_user->avatar) ? $current_user->avatar : 'assets/img/default-avatar.png'; ?>" 
                                         class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                    <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#avatarModal">
                                        Change Avatar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Profile Information</h5>
                                <form id="profileForm">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo $current_user->name ?? ''; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" 
                                            value="<?php echo $current_user->email ?? ''; ?>"
                                            <?php echo !empty($current_user->email) ? 'readonly' : ''; ?>
                                            <?php echo !empty($current_user->email) ? 'title="Email cannot be changed once set"' : ''; ?>>
                                        <?php if(!empty($current_user->email)): ?>
                                            <small class="text-muted">Email cannot be changed once set</small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" name="phone_number" value="<?php echo $current_user->phone_number ?? ''; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Chat ID</label>
                                        <input type="text" class="form-control" name="chat_id" 
                                            value="<?php echo $current_user->chat_id ?? ''; ?>"
                                            <?php echo !empty($current_user->chat_id) ? 'readonly' : ''; ?>
                                            <?php echo !empty($current_user->chat_id) ? 'title="Chat ID cannot be changed once set"' : ''; ?>>
                                        <?php if(!empty($current_user->chat_id)): ?>
                                            <small class="text-muted">Chat ID cannot be changed once set</small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" name="password" placeholder="Enter new password">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Working Hours Tab -->
            <div class="tab-pane fade" id="working-hours" role="tabpanel" aria-labelledby="working-hours-tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Working Hours</h5>
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Avatar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="img-container">
                            <img id="cropper-image" src="" alt="Image to crop">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="preview-container">
                            <div class="img-preview preview-lg"></div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <input type="file" class="form-control" id="avatarInput" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="cropButton">Crop & Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Include required CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

<!-- Include required JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
.profile-container {
    padding: 20px;
}

/* Tab Styles */
.nav-tabs {
    border-bottom: 2px solid #e9ecef;
    margin-bottom: 2rem;
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    padding: 1rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border: none;
    color: #3b7cdd;
}

.nav-tabs .nav-link.active {
    border: none;
    color: #3b7cdd;
    border-bottom: 2px solid #3b7cdd;
    background: none;
}

.nav-tabs .nav-link i {
    margin-right: 0.5rem;
}

.tab-content {
    padding: 1rem 0;
}

.avatar-container {
    position: relative;
    display: inline-block;
}

.img-container {
    max-height: 400px;
    overflow: hidden;
}

.preview-container {
    margin-top: 20px;
}

.preview-lg {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
}

#cropper-image {
    max-width: 100%;
}

/* SweetAlert2 Custom Styles */
.my-swal {
    z-index: 9999 !important;
}

.swal2-popup {
    font-size: 1rem !important;
}

.swal2-title {
    font-size: 1.5rem !important;
}

.swal2-html-container {
    font-size: 1rem !important;
}

.swal2-confirm {
    font-size: 1rem !important;
}

#calendar {
    max-width: 100%;
    margin: 0 auto;
    padding: 20px;
}
.fc-event {
    cursor: pointer;
}

/* Toastr Custom Styles */
.toast {
    opacity: 1 !important;
}
.toast-success {
    background-color: #28a745 !important;
}
.toast-error {
    background-color: #dc3545 !important;
}
.toast-info {
    background-color: #17a2b8 !important;
}
.toast-warning {
    background-color: #ffc107 !important;
}
</style>

<script>
// Configure Toastr
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "3000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

$(document).ready(function() {
    let cropper;
    let calendar;
    
    // Initialize Cropper
    $('#avatarInput').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#cropper-image').attr('src', e.target.result);
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(document.getElementById('cropper-image'), {
                    aspectRatio: 1,
                    viewMode: 1,
                    preview: '.img-preview'
                });
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle profile form submission
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: 'update_profile.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    // Show success message with updated data
                    const updatedData = response.updated_data;
                    let message = '<div class="text-start">';
                    message += '<p><strong>Name:</strong> ' + updatedData.name + '</p>';
                    message += '<p><strong>Email:</strong> ' + updatedData.email + '</p>';
                    message += '<p><strong>Phone:</strong> ' + updatedData.phone_number + '</p>';
                    message += '<p><strong>Chat ID:</strong> ' + updatedData.chat_id + '</p>';
                    message += '</div>';
                    
                    Swal.fire({
                        title: 'Success!',
                        html: message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            container: 'my-swal'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error updating profile: ' + response.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            container: 'my-swal'
                        }
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Error updating profile',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        container: 'my-swal'
                    }
                });
            }
        });
    });

    // Handle avatar crop and save
    $('#cropButton').on('click', function() {
        if (!cropper) return;
        
        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300
        });
        
        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('avatar', blob, 'avatar.jpg');
            
            $.ajax({
                url: 'update_avatar.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.success) {
                        $('#avatar-preview').attr('src', response.avatar_url);
                        $('#avatarModal').modal('hide');
                        Swal.fire({
                            title: 'Success!',
                            text: 'Avatar updated successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                container: 'my-swal'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error updating avatar: ' + response.message,
                            icon: 'error',
                            confirmButtonText: 'OK',
                            customClass: {
                                container: 'my-swal'
                            }
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error updating avatar',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            container: 'my-swal'
                        }
                    });
                }
            });
        });
    });

    // Initialize calendar when working hours tab is shown
    $('#working-hours-tab').on('shown.bs.tab', function (e) {
        if (!calendar) {
            initializeCalendar();
        } else {
            calendar.render();
        }
    });

    function initializeCalendar() {
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,timeGridDay'
            },
            timeZone: 'local',
            selectable: true,
            editable: true,
            eventDurationEditable: true,
            selectMirror: true,
            dayMaxEvents: true,
            slotMinTime: '08:00:00',
            slotMaxTime: '20:00:00',
            slotDuration: '00:30:00',
            allDaySlot: false,
            select: function(info) {
                // Convert to local time
                const localStart = new Date(info.start);
                const localEnd = new Date(info.end);

                // Format for display
                const formatTime = (date) => {
                    return date.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });
                };

                // Format for input
                const formatForInput = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    return `${year}-${month}-${day}T${hours}:${minutes}`;
                };

                Swal.fire({
                    title: 'Add Working Hours',
                    html: `
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" id="eventTitle" class="form-control" placeholder="Enter event title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Start Time</label>
                            <input type="datetime-local" id="startTime" class="form-control" value="${formatForInput(localStart)}">
                            <small class="text-muted">Selected: ${formatTime(localStart)}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Time</label>
                            <input type="datetime-local" id="endTime" class="form-control" value="${formatForInput(localEnd)}">
                            <small class="text-muted">Selected: ${formatTime(localEnd)}</small>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Add',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        const title = document.getElementById('eventTitle').value || 'Working Hours';
                        const startTime = document.getElementById('startTime').value;
                        const endTime = document.getElementById('endTime').value;
                        if (!startTime || !endTime) {
                            Swal.showValidationMessage('Please fill in all fields');
                            return false;
                        }
                        return { title, startTime, endTime };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Convert local time to UTC for storage
                        const startUTC = new Date(result.value.startTime);
                        const endUTC = new Date(result.value.endTime);

                        $.ajax({
                            url: 'working_hours.php',
                            method: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                title: result.value.title,
                                start_time: startUTC.toISOString(),
                                end_time: endUTC.toISOString()
                            }),
                            success: function(response) {
                                if (response.success) {
                                    calendar.refetchEvents();
                                    toastr.success('Working hours added successfully');
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function() {
                                toastr.error('Error occurred while saving working hours');
                            }
                        });
                    }
                });
                calendar.unselect();
            },
            eventClick: function(info) {
                // Convert to local time
                const localStart = new Date(info.event.start);
                const localEnd = new Date(info.event.end);

                // Format for display
                const formatTime = (date) => {
                    return date.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });
                };

                // Format for input
                const formatForInput = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    return `${year}-${month}-${day}T${hours}:${minutes}`;
                };

                Swal.fire({
                    title: 'Edit Working Hours',
                    html: `
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" id="editTitle" class="form-control" value="${info.event.title}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Start Time</label>
                            <input type="datetime-local" id="editStartTime" class="form-control" value="${formatForInput(localStart)}">
                            <small class="text-muted">Current: ${formatTime(localStart)}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Time</label>
                            <input type="datetime-local" id="editEndTime" class="form-control" value="${formatForInput(localEnd)}">
                            <small class="text-muted">Current: ${formatTime(localEnd)}</small>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Delete',
                    showDenyButton: true,
                    denyButtonText: 'Cancel',
                    preConfirm: () => {
                        const title = document.getElementById('editTitle').value || 'Working Hours';
                        const startTime = document.getElementById('editStartTime').value;
                        const endTime = document.getElementById('editEndTime').value;
                        if (!startTime || !endTime) {
                            Swal.showValidationMessage('Please fill in all fields');
                            return false;
                        }
                        return { title, startTime, endTime };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Convert local time to UTC for storage
                        const startUTC = new Date(result.value.startTime);
                        const endUTC = new Date(result.value.endTime);

                        $.ajax({
                            url: 'working_hours.php',
                            method: 'PUT',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                id: info.event.id,
                                title: result.value.title,
                                start_time: startUTC.toISOString(),
                                end_time: endUTC.toISOString()
                            }),
                            success: function(response) {
                                if (response.success) {
                                    calendar.refetchEvents();
                                    toastr.success('Working hours updated successfully');
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function() {
                                toastr.error('Error occurred while updating working hours');
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // Delete event
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'No, cancel!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: 'working_hours.php',
                                    method: 'DELETE',
                                    contentType: 'application/json',
                                    data: JSON.stringify({
                                        id: info.event.id
                                    }),
                                    success: function(response) {
                                        if (response.success) {
                                            calendar.refetchEvents();
                                            toastr.success('Working hours deleted successfully');
                                        } else {
                                            toastr.error(response.message);
                                        }
                                    },
                                    error: function() {
                                        toastr.error('Error occurred while deleting working hours');
                                    }
                                });
                            }
                        });
                    }
                });
            },
            eventDrop: function(info) {
                // Convert to UTC for storage
                const startUTC = new Date(info.event.start);
                const endUTC = new Date(info.event.end);

                $.ajax({
                    url: 'working_hours.php',
                    method: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        id: info.event.id,
                        title: info.event.title,
                        start_time: startUTC.toISOString(),
                        end_time: endUTC.toISOString()
                    }),
                    success: function(response) {
                        if (!response.success) {
                            info.revert();
                            toastr.error(response.message);
                        } else {
                            toastr.success('Working hours updated successfully');
                        }
                    },
                    error: function() {
                        info.revert();
                        toastr.error('Error occurred while updating working hours');
                    }
                });
            },
            eventResize: function(info) {
                // Convert to UTC for storage
                const startUTC = new Date(info.event.start);
                const endUTC = new Date(info.event.end);

                $.ajax({
                    url: 'working_hours.php',
                    method: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        id: info.event.id,
                        title: info.event.title,
                        start_time: startUTC.toISOString(),
                        end_time: endUTC.toISOString()
                    }),
                    success: function(response) {
                        if (!response.success) {
                            info.revert();
                            toastr.error(response.message);
                        } else {
                            toastr.success('Working hours updated successfully');
                        }
                    },
                    error: function() {
                        info.revert();
                        toastr.error('Error occurred while updating working hours');
                    }
                });
            },
            events: function(info, successCallback, failureCallback) {
                $.ajax({
                    url: 'working_hours.php',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var events = response.data.map(function(item) {
                                return {
                                    id: item.id,
                                    title: item.title || 'Working Hours',
                                    start: item.start_time,
                                    end: item.end_time,
                                    backgroundColor: '#3788d8',
                                    borderColor: '#3788d8',
                                    allDay: false
                                };
                            });
                            successCallback(events);
                        } else {
                            failureCallback(response.message);
                        }
                    },
                    error: function() {
                        failureCallback('Error loading working hours');
                    }
                });
            },
            eventContent: function(arg) {
                return {
                    html: `
                        <div class="fc-content">
                            <div class="fc-title">${arg.event.title}</div>
                            <div class="fc-time">
                                ${arg.timeText}
                            </div>
                        </div>
                    `
                };
            }
        });
        calendar.render();
    }
});
</script>

<?php include 'views/footer-dashboard.php' ?> 