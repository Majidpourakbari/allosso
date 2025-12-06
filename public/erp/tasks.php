<?php include 'views/headin2.php' ?>

<!-- Add SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<!-- Add SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<div class="dashboard-container">
    <?php include 'views/header-sidebar.php' ?>
    
    <div class="content-wrapper">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Tasks</h2>
            </div>
        </div>

        <div class="content">
            <div class="card">
                <div class="card-body">
                    <!-- Filters Section -->
                    <div class="filters-section mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-2 mb-3">
                                <label for="task-status" class="form-label">Status</label>
                                <select class="form-select filter-select" id="task-status">
                                    <option value="">All Status</option>
                                    <option value="0">To Do</option>
                                    <option value="1">In Progress</option>
                                    <option value="2">Review</option>
                                    <option value="3">Done</option>
                                    <option value="4">Archived</option>
                                    <option value="5">To Debug</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="task-priority" class="form-label">Priority</label>
                                <select class="form-select filter-select" id="task-priority">
                                    <option value="">All Priority</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                    <option value="Hotfix">Hotfix</option>
                                    <option value="Urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="task-category" class="form-label">Category</label>
                                <select class="form-select filter-select" id="task-category">
                                    <option value="">All Categories</option>
                                    <option value="IT">IT</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Product management">Product management</option>
                                    <option value="Product Development">Product Development</option>
                                    <option value="Strategy planning">Strategy planning</option>
                                    <option value="Business planning">Business planning</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="task-allo-section" class="form-label">Allo Section</label>
                                <select class="form-select filter-select" id="task-allo-section">
                                    <option value="">All Sections</option>
                                    <option value="allolancer">allolancer</option>
                                    <option value="allohub erp">allohub erp</option>
                                    <option value="alloAi">alloAi</option>
                                    <option value="private">private</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="task-label" class="form-label">Label</label>
                                <select class="form-select filter-select" id="task-label">
                                    <option value="">All Labels</option>
                                    <option value="feature">Feature</option>
                                    <option value="debug">Debug</option>
                                    <option value="QA">QA</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3 d-flex align-items-end gap-2">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newTaskModal" title="New Task">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#calendarOffcanvas" aria-controls="calendarOffcanvas" title="Calendar">
                                    <i class="fas fa-calendar"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Category</th>
                                    <th>Allo Section</th>
                                    <th>Label</th>
                                    <th>Progress (%)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $stmt = $conn->prepare("SELECT t.*, 
                                                          u.name as creator_name, 
                                                          u.avatar as creator_avatar,
                                                          GROUP_CONCAT(DISTINCT CONCAT(ur.id, ':', ur.name, ':', ur.avatar) SEPARATOR '||') as receiver_info,
                                                          DATE_FORMAT(t.date_create, '%Y-%m-%d %H:%i') as formatted_date,
                                                          (SELECT COUNT(*) FROM notifications n 
                                                           WHERE n.receiver_ids = :my_profile_id 
                                                           AND n.message LIKE CONCAT('%added to task #', t.id, '%')
                                                           AND n.users_read NOT LIKE CONCAT('%,', :my_profile_id, ',%')
                                                           AND n.users_read NOT LIKE CONCAT('%,', :my_profile_id)
                                                           AND n.users_read NOT LIKE CONCAT(:my_profile_id, ',%')
                                                           AND n.users_read != :my_profile_id) as has_new_access,
                                                          COALESCE(
                                                              CASE 
                                                                  WHEN (SELECT COUNT(*) FROM tasks_checklists tc WHERE tc.task_id = t.id) = 0 THEN 0
                                                                  ELSE ROUND(
                                                                      (SELECT COUNT(*) FROM tasks_checklists tc WHERE tc.task_id = t.id AND tc.status = 1) * 100.0 / 
                                                                      (SELECT COUNT(*) FROM tasks_checklists tc WHERE tc.task_id = t.id), 0
                                                                  )
                                                              END, 0
                                                          ) as calculated_progress
                                                          FROM tasks t 
                                                          LEFT JOIN users u ON t.user_creator = u.id 
                                                          LEFT JOIN task_users tu ON t.id = tu.task_id
                                                          LEFT JOIN users ur ON tu.user_id = ur.id 
                                                          WHERE (t.user_creator = :my_profile_id 
                                                          OR tu.user_id = :my_profile_id)
                                                          AND t.status != 4
                                                          GROUP BY t.id
                                                          ORDER BY t.id DESC");
                                    $stmt->bindParam(':my_profile_id', $my_profile_id);
                                    $stmt->execute();
                                    $read_db = $stmt->fetchAll(PDO::FETCH_OBJ);
                                } catch(Exception $e) {
                                    echo '<tr><td colspan="8" class="text-center text-danger">Error: ' . $e->getMessage() . '</td></tr>';
                                }

                                if(empty($read_db)) {
                                    echo '<tr><td colspan="8" class="text-center">No tasks found</td></tr>';
                                } else {
                                    foreach($read_db as $task) : 
                                                                // Map status values to text
                        $statusText = match($task->status) {
                            '0' => 'To Do',
                            '1' => 'In Progress',
                            '2' => 'Review',
                            '3' => 'Done',
                            '4' => 'Archived',
                            '5' => 'To Debug', // Keep for backward compatibility
                            default => 'Unknown'
                        };
                                        
                                                                // Map status to badge color
                        $statusClass = match($task->status) {
                            '0' => 'secondary',
                            '1' => 'primary',
                            '2' => 'warning',
                            '3' => 'success',
                            '4' => 'dark',
                            '5' => 'danger', // Keep for backward compatibility
                            default => 'secondary'
                        };
                                        
                                        // Map priority to badge color
                                        $priorityClass = match($task->priority) {
                                            'Low' => 'info',
                                            'Medium' => 'primary',
                                            'High' => 'warning',
                                            'Critical' => 'danger',
                                            'Hotfix' => 'danger',
                                            'Urgent' => 'danger',
                                            default => 'secondary'
                                        };

                                        // Check if user has new access to this task
                                        $hasNewAccess = $task->has_new_access > 0;
                                        $rowClass = $hasNewAccess ? 'table-warning' : '';
                                    ?>
                                        <tr class="task-row <?php echo $rowClass; ?>" data-task-id="<?php echo htmlspecialchars($task->id); ?>" style="cursor: pointer;">
                                            <td><?php echo htmlspecialchars($task->id); ?></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($hasNewAccess): ?>
                                                            <span class="badge bg-warning me-2" title="New access granted">
                                                                <i class="fas fa-user-plus"></i>
                                                            </span>
                                                        <?php endif; ?>
                                                        <img src="uploads/profiles/<?php echo htmlspecialchars($task->creator_avatar ?? 'no-profile.jpg'); ?>" 
                                                             alt="<?php echo htmlspecialchars($task->creator_name ?? 'Unknown'); ?>" 
                                                             class="rounded-circle me-2" 
                                                             style="width: 32px; height: 32px; object-fit: cover;">
                                                        <?php 
                                                        $title = htmlspecialchars($task->title);
                                                        echo strlen($title) > 100 ? substr($title, 0, 30) . '...' : $title;
                                                        ?>
                                                    </div>
                                                    <?php if ($task->receiver_info): ?>
                                                    <div class="mt-1">
                                                        <?php 
                                                        $receivers = explode('||', $task->receiver_info);
                                                        foreach($receivers as $receiver): 
                                                            list($id, $name, $avatar) = explode(':', $receiver);
                                                        ?>
                                                            <div class="profile-tooltip">
                                                                <img src="uploads/profiles/<?php echo htmlspecialchars($avatar ?? 'no-profile.jpg'); ?>" 
                                                                     alt="<?php echo htmlspecialchars($name); ?>" 
                                                                     class="rounded-circle" 
                                                                     style="width: 24px; height: 24px; object-fit: cover;">
                                                                <span class="tooltip-text"><?php echo htmlspecialchars($name); ?></span>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo $statusClass; ?>">
                                                    <?php echo htmlspecialchars($statusText); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo $priorityClass; ?>">
                                                    <?php echo htmlspecialchars($task->priority); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($task->category); ?></td>
                                            <td><?php echo htmlspecialchars($task->allo_section); ?></td>
                                            <td>
                                                <?php if ($task->label): ?>
                                                    <span class="badge badge-info">
                                                        <?php echo htmlspecialchars($task->label); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 60px; height: 8px;">
                                                        <div class="progress-bar bg-primary" role="progressbar" 
                                                             style="width: <?php echo ($task->calculated_progress ?? 0); ?>%" 
                                                             aria-valuenow="<?php echo ($task->calculated_progress ?? 0); ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted"><?php echo ($task->calculated_progress ?? 0); ?>%</small>
                                                </div>
                                            </td>
                                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item edit-task" href="#" data-task-id="<?php echo htmlspecialchars($task->id); ?>">
                                                <i class="fas fa-edit text-primary me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item print-task" href="#" data-task-id="<?php echo htmlspecialchars($task->id); ?>" title="Print Task">
                                                <i class="fas fa-print text-info me-2"></i>Print
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <?php if ($task->status === '4'): ?>
                                            <li>
                                                <a class="dropdown-item unarchive-task" href="#" data-task-id="<?php echo htmlspecialchars($task->id); ?>" title="Unarchive Task">
                                                    <i class="fas fa-undo text-success me-2"></i>Unarchive
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li>
                                                <a class="dropdown-item archive-task" href="#" data-task-id="<?php echo htmlspecialchars($task->id); ?>" title="Archive Task">
                                                    <i class="fas fa-archive text-dark me-2"></i>Archive
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <li>
                                            <a class="dropdown-item delete-task" href="#" data-task-id="<?php echo htmlspecialchars($task->id); ?>">
                                                <i class="fas fa-trash text-danger me-2"></i>Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                                        </tr>
                                    <?php endforeach;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Details Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="taskDetailsOffcanvas" aria-labelledby="taskDetailsOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="taskDetailsOffcanvasLabel">Task Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" onclick="closeTaskDetailsOffcanvas()"></button>
    </div>
    <div class="offcanvas-body">
        <div id="taskPercentage">
            <!-- Task percentage will be displayed here -->
        </div>
        <div id="taskDetailsContent">
            <!-- Task details will be loaded here -->
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm">
                    <input type="hidden" id="editTaskId" name="task_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="editTitle" name="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editCategory" class="form-label">Category</label>
                            <select class="form-select" id="editCategory" name="category">
                                <option value="IT">IT</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Product management">Product management</option>
                                <option value="Product Development">Product Development</option>
                                <option value="Strategy planning">Strategy planning</option>
                                <option value="Business planning">Business planning</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editAlloSection" class="form-label">Allo Section</label>
                            <select class="form-select" id="editAlloSection" name="allo_section">
                                <option value="">Select Allo Section</option>
                                <option value="allolancer">allolancer</option>
                                <option value="allohub erp">allohub erp</option>
                                <option value="alloAi">alloAi</option>
                                <option value="private">private</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editPriority" class="form-label">Priority</label>
                            <select class="form-select" id="editPriority" name="priority">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                                <option value="Hotfix">Hotfix</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editLabel" class="form-label">Label</label>
                            <select class="form-select" id="editLabel" name="label">
                                <option value="">Select Label (Optional)</option>
                                <option value="feature">Feature</option>
                                <option value="debug">Debug</option>
                                <option value="QA">QA</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status">
                                <option value="0">To Do</option>
                                <option value="1">In Progress</option>
                                <option value="2">Review</option>
                                <option value="3">Done</option>
                                <option value="4">Archived</option>
                                <option value="5">To Debug</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editObjective" class="form-label">Objective</label>
                            <input type="text" class="form-control" id="editObjective" name="objective">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editProgress" class="form-label">Progress (%)</label>
                            <input type="number" class="form-control" id="editProgress" name="progress" min="0" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editDateStart" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="editDateStart" name="date_start">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editTimeStart" class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="editTimeStart" name="time_start">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editDateFinish" class="form-label">Finish Date</label>
                            <input type="date" class="form-control" id="editDateFinish" name="date_finish">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editTimeFinish" class="form-label">Finish Time</label>
                            <input type="time" class="form-control" id="editTimeFinish" name="time_finish">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editBudget" class="form-label">Budget</label>
                            <input type="text" class="form-control" id="editBudget" name="budget">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editRisks" class="form-label">Risks</label>
                            <textarea class="form-control" id="editRisks" name="risks" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editRequiredTools" class="form-label">Required Tools</label>
                            <textarea class="form-control" id="editRequiredTools" name="required_tools" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveTaskChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add User to Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="userSearchInput" class="form-label">Search Users</label>
                    <input type="text" class="form-control" id="userSearchInput" placeholder="Type to search users...">
                </div>
                <div id="userSearchResults" class="list-group">
                    <!-- User search results will be displayed here -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Action Dropdown Styling */
.dropdown-toggle::after {
    display: none; /* Remove default Bootstrap arrow */
}

.dropdown-menu {
    min-width: 160px;
    border: 1px solid rgba(0, 0, 0, 0.15);
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.dropdown-item {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    transition: all 0.15s ease-in-out;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(2px);
}

.dropdown-item i {
    width: 16px;
    text-align: center;
}

.dropdown-divider {
    margin: 0.5rem 0;
}

/* Action button styling */
.btn-outline-secondary.dropdown-toggle {
    border-color: #dee2e6;
    color: #6c757d;
    transition: all 0.15s ease-in-out;
}

.btn-outline-secondary.dropdown-toggle:hover {
    background-color: #f8f9fa;
    border-color: #adb5bd;
    color: #495057;
}

.table {
    width: 100%;
    margin-bottom: 1rem;
    background-color: transparent;
    font-size: 0.85rem;
}

.table th,
.table td {
    padding: 0.5rem;
    vertical-align: middle;
    border-top: 1px solid #dee2e6;
}

.table thead th {
    vertical-align: bottom;
    border-bottom: 2px solid #dee2e6;
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody tr:hover {
    background-color: rgba(0,0,0,.075);
}

.badge {
    padding: 0.35em 0.65em;
    font-size: 0.7rem;
    font-weight: 600;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}

.badge-secondary { background-color: #6c757d; color: #fff; }
.badge-primary { background-color: #007bff; color: #fff; }
.badge-success { background-color: #28a745; color: #fff; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-danger { background-color: #dc3545; color: #fff; }
.badge-info { background-color: #17a2b8; color: #fff; }
.badge-dark { background-color: #343a40; color: #fff; }

.btn-group {
    display: flex;
    gap: 0.25rem;
}

.btn-sm {
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
    line-height: 1.4;
    border-radius: 0.2rem;
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-info {
    color: #fff;
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-info:hover {
    color: #fff;
    background-color: #138496;
    border-color: #117a8b;
}

.filters-section {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.25rem;
    margin-bottom: 1rem;
}

.form-label {
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-select {
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

@media (max-width: 768px) {
    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
    }
    
    .table th,
    .table td {
        white-space: nowrap;
    }
    
    .filters-section .col-md-3 {
        margin-bottom: 1rem;
    }
}

/* Profile Tooltip Styles */
.profile-tooltip {
    position: relative;
    display: inline-block;
    margin-right: 4px;
}

.profile-tooltip .tooltip-text {
    visibility: hidden;
    background-color: #333;
    color: #fff;
    text-align: center;
    padding: 5px 10px;
    border-radius: 4px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    font-size: 12px;
    opacity: 0;
    transition: opacity 0.3s;
}

.profile-tooltip .tooltip-text::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #333 transparent transparent transparent;
}

.profile-tooltip:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}

.offcanvas {
    width: 90% !important;
}

#taskDetailsOffcanvas {
    width: 50% !important;
}

@media (max-width: 768px) {
    .offcanvas {
        width: 100% !important;
    }
}

/* Add new styles for the task info line */
.task-info-line {
    border: 1px solid #dee2e6;
    background-color: #f8f9fa;
}

.task-info-line .text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.task-info-line small {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.task-info-line .profile-tooltip {
    margin-right: 2px;
}

.task-info-line .profile-tooltip img {
    border: 2px solid #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .task-info-line .col-md-3 {
        margin-bottom: 1rem;
    }
}

/* Add new styles for tabs and content */
.nav-tabs {
    border-bottom: 1px solid #dee2e6;
}

.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

.nav-tabs .nav-link.active {
    color: #007bff;
    background: none;
    border-bottom: 2px solid #007bff;
}

.nav-tabs .nav-link i {
    margin-right: 0.5rem;
}

.tab-content {
    padding: 1rem 0;
}

.file-item, .user-item {
    transition: background-color 0.2s;
}

.file-item:hover, .user-item:hover {
    background-color: #f8f9fa;
}

.progress {
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    transition: width 0.3s ease;
}

/* Task Details Styles */
.task-details {
    padding: 1rem;
}

.task-details h4 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.task-meta {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.task-meta .badge {
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    font-weight: 500;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Progress Bar Styles */
.task-progress {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
}

.task-progress .progress {
    height: 8px;
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.task-progress .progress-bar {
    background-color: #007bff;
    transition: width 0.3s ease;
}

.task-progress small {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Info Line Styles */
.task-info-line {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.task-info-line small {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    margin-bottom: 0.25rem;
    display: block;
}

.task-info-line .text-truncate {
    color: #2c3e50;
    font-size: 0.9rem;
}

/* Tab Styles */
.nav-tabs {
    border-bottom: 2px solid #e9ecef;
    margin-bottom: 1.5rem;
}

.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    padding: 0.75rem 1.25rem;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.nav-tabs .nav-link:hover {
    color: #007bff;
    background: none;
    border: none;
}

.nav-tabs .nav-link.active {
    color: #007bff;
    background: none;
    border: none;
    border-bottom: 2px solid #007bff;
}

.nav-tabs .nav-link i {
    margin-right: 0.5rem;
    font-size: 0.9rem;
}

/* Tab Content Styles */
.tab-content {
    padding: 1rem 0;
}

/* Checklist Styles */
.checklist-items {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.checklist-item {
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    transition: all 0.2s ease;
    position: relative;
}

.checklist-item:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.checklist-item .form-check-input {
    width: 1.2rem;
    height: 1.2rem;
    margin-top: 0.1rem;
}

.checklist-item .form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

/* Hierarchical checklist styles */
.checklist-item[style*="margin-left"] {
    border-left: 3px solid #007bff;
    margin-left: 20px !important;
}

.checklist-item[style*="margin-left: 20px"] {
    border-left-color: #28a745;
}

.checklist-item[style*="margin-left: 40px"] {
    border-left-color: #ffc107;
}

.checklist-item[style*="margin-left: 60px"] {
    border-left-color: #dc3545;
}

.checklist-item[style*="margin-left: 80px"] {
    border-left-color: #6c757d;
}

/* Parent checklist indicator */
.checklist-item.has-children::before {
    content: "📁";
    position: absolute;
    left: -15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    color: #6c757d;
}

/* Child checklist indicator */
.checklist-item.is-child::before {
    content: "📄";
    position: absolute;
    left: -15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    color: #6c757d;
}

/* Archived checklist items */
.checklist-item.archived-item {
    background-color: #f8f9fa;
    opacity: 0.8;
}

.checklist-item.archived-item .form-check-input {
    opacity: 0.5;
}

/* Archived tab styling */
#archived-tab .badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}

.archived-checklists {
    padding: 1rem 0;
}

/* Label badge styling */
.badge.badge-success {
    background-color: #28a745;
    color: white;
}

.badge.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.checklist-item .badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 500;
}



/* Files List Styles */
.files-list {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.file-item {
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.file-item:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.file-item i.fa-file {
    color: #6c757d;
    font-size: 1.2rem;
}

/* People List Styles */
.people-list {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.user-item {
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.user-item:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.user-item img {
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Button Styles */
.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    font-weight: 500;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-outline-primary {
    color: #007bff;
    border-color: #007bff;
}

.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

/* Scrollbar Styles */
.checklist-items::-webkit-scrollbar,
.files-list::-webkit-scrollbar,
.people-list::-webkit-scrollbar {
    width: 6px;
}

.checklist-items::-webkit-scrollbar-track,
.files-list::-webkit-scrollbar-track,
.people-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.checklist-items::-webkit-scrollbar-thumb,
.files-list::-webkit-scrollbar-thumb,
.people-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.checklist-items::-webkit-scrollbar-thumb:hover,
.files-list::-webkit-scrollbar-thumb:hover,
.people-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .task-details {
        padding: 0.5rem;
    }

    .task-details h4 {
        font-size: 1.25rem;
    }

    .nav-tabs .nav-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
    }

    .task-info-line .col-md-3 {
        margin-bottom: 1rem;
    }

    .btn-sm {
        padding: 0.3rem 0.6rem;
        font-size: 0.75rem;
    }
}

/* Accordion Styles */
.accordion-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.accordion-button {
    padding: 1rem;
    font-weight: 500;
    color: #2c3e50;
    background-color: #f8f9fa;
}

.accordion-button:not(.collapsed) {
    color: #007bff;
    background-color: #f8f9fa;
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: #e9ecef;
}

.accordion-button::after {
    background-size: 1rem;
}

.accordion-body {
    padding: 1.5rem;
    background-color: #fff;
}

.info-item {
    padding: 0.5rem;
    border-radius: 6px;
    background-color: #f8f9fa;
}

.info-item small {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.info-item p {
    color: #2c3e50;
    font-size: 0.9rem;
}

.info-item .profile-tooltip {
    margin-right: 4px;
}

.info-item .profile-tooltip img {
    border: 2px solid #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Checklist Details Modal Styles */
.checklist-details .form-label {
    color: #6c757d;
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.checklist-details p {
    color: #2c3e50;
    font-size: 0.95rem;
}

.checklist-details .badge {
    font-size: 0.8rem;
    padding: 0.35em 0.65em;
}

.checklist-details .attachment-link {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    text-decoration: none;
    color: #2c3e50;
    transition: all 0.2s ease;
}

.checklist-details .attachment-link:hover {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

.checklist-details .attachment-link i {
    margin-right: 0.5rem;
    color: #6c757d;
}

/* Enhanced Attachment Item Styles */
.attachment-item {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef !important;
    border-radius: 8px !important;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.attachment-item:hover {
    background-color: #fff;
    border-color: #007bff !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.attachment-item .fas {
    font-size: 1.1rem;
}

.attachment-item audio {
    border-radius: 6px;
    background-color: #fff;
}

.attachment-item audio::-webkit-media-controls-panel {
    background-color: #f8f9fa;
}

.attachment-item audio::-webkit-media-controls-play-button {
    background-color: #007bff;
    border-radius: 50%;
}

.attachment-item img {
    border: 1px solid #dee2e6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.attachment-item img:hover {
    transform: scale(1.02);
}

.attachment-item .btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8rem;
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.attachment-item .btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
}

.attachment-item .btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
}

/* Responsive adjustments for attachment items */
@media (max-width: 768px) {
    .attachment-item {
        padding: 1rem !important;
    }
    
    .attachment-item img {
        max-height: 200px !important;
    }
    
    .attachment-item .d-flex.justify-content-between {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .attachment-item .d-flex.justify-content-between .btn {
        align-self: flex-end;
    }
}

/* Notes Tab Styles */
.notes-list {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.note-item {
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.note-item:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.note-item p {
    white-space: pre-wrap;
    word-break: break-word;
}

/* Make tabs scrollable */
.nav-tabs {
    flex-wrap: nowrap;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

.nav-tabs::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

.nav-tabs .nav-item {
    flex: 0 0 auto;
    white-space: nowrap;
}

.nav-tabs .nav-link {
    padding: 0.75rem 1.25rem;
}

#calendar {
    height: calc(100vh - 120px);
}

.highlight {
    animation: highlight-pulse 2s ease-out;
}

@keyframes highlight-pulse {
    0% { background-color: #fff3cd; }
    100% { background-color: transparent; }
}

/* Calendar Offcanvas */
#calendarOffcanvas {
    width: 90% !important;
}

/* Task Details Offcanvas */
#taskDetailsOffcanvas {
    width: 50% !important;
}

/* Custom styles for meeting events */
.fc-event.meeting-event {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
    font-weight: bold !important;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3) !important;
}

.fc-event.meeting-event:hover {
    background-color: #c82333 !important;
    border-color: #c82333 !important;
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4) !important;
}

.fc-event.meeting-event .fc-event-title {
    color: white !important;
    font-weight: bold !important;
}

@media (max-width: 768px) {
    #calendarOffcanvas,
    #taskDetailsOffcanvas {
        width: 100% !important;
    }
}

/* Task Filter Section */
.task-filter-section {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    background-color: #f8f9fa;
}

.task-filter-section h6 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 15px;
}

.task-checkboxes-container {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background-color: white;
    padding: 10px;
}

.task-checkbox-item {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 4px;
    margin-bottom: 4px;
    transition: background-color 0.2s;
    cursor: pointer;
}

.task-checkbox-item:hover {
    background-color: #e9ecef;
}

.task-checkbox-item:last-child {
    margin-bottom: 0;
}

.task-checkbox-item input[type="checkbox"] {
    margin-right: 10px;
    cursor: pointer;
}

.task-checkbox-item label {
    margin: 0;
    cursor: pointer;
    font-size: 14px;
    color: #495057;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.task-checkbox-item .task-status {
    margin-left: 8px;
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 12px;
    font-weight: 500;
}

.task-checkbox-item .task-status.created { background-color: #6c757d; color: white; }
.task-checkbox-item .task-status.in-progress { background-color: #007bff; color: white; }
.task-checkbox-item .task-status.completed { background-color: #28a745; color: white; }
.task-checkbox-item .task-status.on-hold { background-color: #ffc107; color: #212529; }
.task-checkbox-item .task-status.to-debug { background-color: #dc3545; color: white; }
</style>

<script>
// Global function to close Task Details offcanvas
function closeTaskDetailsOffcanvas() {
    const taskDetailsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('taskDetailsOffcanvas'));
    if (taskDetailsOffcanvas) {
        taskDetailsOffcanvas.hide();
    }
}

// Global function to mark task notifications as read
function markTaskNotificationsAsRead(taskId) {
    // Check if we've already marked notifications for this task in this session
    const viewedTasks = JSON.parse(sessionStorage.getItem('viewedTasks') || '[]');
    
    if (viewedTasks.includes(taskId)) {
        // Already viewed this task, don't mark notifications again
        return;
    }
    
    // Mark notifications as read for this specific task
    fetch('mark_notifications_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            task_id: taskId,
            my_profile_id: <?php echo $my_profile_id; ?>
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add this task to the viewed tasks list
            viewedTasks.push(taskId);
            sessionStorage.setItem('viewedTasks', JSON.stringify(viewedTasks));
            
            // Update the task row styling to remove the "new access" indicator
            const taskRow = document.querySelector(`.task-row[data-task-id="${taskId}"]`);
            if (taskRow) {
                taskRow.classList.remove('table-warning');
                const newAccessBadge = taskRow.querySelector('.badge.bg-warning');
                if (newAccessBadge) {
                    newAccessBadge.remove();
                }
            }
            
            // Update notification counter in header if it exists
            if (typeof updateNotificationCounter === 'function') {
                updateNotificationCounter();
            }
            
            console.log('Task notifications marked as read for task:', taskId);
        } else {
            console.error('Failed to mark task notifications as read:', data.message);
        }
    })
    .catch(error => {
        console.error('Error marking task notifications as read:', error);
    });
}

// Global functions for task user management
function updatePeopleAccessTab(taskId) {
    fetch(`get_task_details.php?id=${taskId}`)
        .then(response => response.json())
        .then(data => {
            const peopleTab = document.getElementById('people');
            if (peopleTab) {
                const usersList = document.createElement('div');
                usersList.className = 'list-group';
                
                if (data.task_users && data.task_users.length > 0) {
                    data.task_users.forEach(user => {
                        const userItem = document.createElement('div');
                        userItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                        userItem.innerHTML = `
                            <div class="d-flex align-items-center">
                                <img src="uploads/profiles/${user.avatar || 'no-profile.jpg'}" 
                                     alt="${user.name}" 
                                     class="rounded-circle me-2" 
                                     style="width: 32px; height: 32px; object-fit: cover;">
                                <span>${user.name}</span>
                            </div>
                            <button class="btn btn-sm btn-danger" onclick="removeTaskUser(${taskId}, ${user.id})">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        usersList.appendChild(userItem);
                    });
                } else {
                    usersList.innerHTML = '<div class="list-group-item text-center">No users assigned to this task</div>';
                }

                // Add the "Add User" button
                const addUserButton = document.createElement('button');
                addUserButton.className = 'btn btn-primary mt-3';
                addUserButton.innerHTML = '<i class="fas fa-plus"></i> Add User';
                addUserButton.onclick = () => addTaskUser(taskId);

                // Clear and update the People Access tab
                peopleTab.innerHTML = '';
                peopleTab.appendChild(usersList);
                peopleTab.appendChild(addUserButton);
            }
        })
        .catch(error => {
            console.error('Error updating people access tab:', error);
        });
}

function addTaskUser(taskId) {
    const addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
    addUserModal.show();
    
    // Clear previous search results
    const resultsContainer = document.getElementById('userSearchResults');
    resultsContainer.innerHTML = '';
    
    // First get current task users and creator to check against
    fetch(`get_task_details.php?id=${taskId}`)
        .then(response => response.json())
        .then(taskData => {
            const assignedUserIds = taskData.task_users ? taskData.task_users.map(user => user.id) : [];
            const taskCreatorId = taskData.user_creator;
            
            // Load last 5 users by default
            fetch(`get_last_users.php?task_id=${taskId}`)
                .then(response => response.json())
                .then(users => {
                    users.forEach(user => {
                        // Skip if user is already assigned
                        if (assignedUserIds.includes(user.id)) {
                            return;
                        }
                        
                        const userItem = document.createElement('button');
                        userItem.className = 'list-group-item list-group-item-action d-flex align-items-center';
                        userItem.innerHTML = `
                            <img src="uploads/profiles/${user.avatar || 'no-profile.jpg'}" 
                                 alt="${user.name}" 
                                 class="rounded-circle me-2" 
                                 style="width: 32px; height: 32px; object-fit: cover;">
                            <span>${user.name}</span>
                        `;
                        userItem.onclick = () => addUserToTask(taskId, user.id, addUserModal);
                        resultsContainer.appendChild(userItem);
                    });
                })
                .catch(error => {
                    console.error('Error loading last users:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to load users.',
                        icon: 'error'
                    });
                });
        })
        .catch(error => {
            console.error('Error getting task details:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to get task details.',
                icon: 'error'
            });
        });
    
    // Add event listener for user search
    const searchInput = document.getElementById('userSearchInput');
    if (searchInput) {
        // Remove any existing event listeners
        searchInput.removeEventListener('input', handleSearch);
        
        // Add new event listener
        searchInput.addEventListener('input', handleSearch);
        
        function handleSearch(e) {
            const searchTerm = e.target.value.trim();
            
            if (searchTerm.length < 2) {
                return;
            }
            
            // Show loading state
            resultsContainer.innerHTML = '<div class="list-group-item text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            // First get current task users and creator to check against
            fetch(`get_task_details.php?id=${taskId}`)
                .then(response => response.json())
                .then(taskData => {
                    const assignedUserIds = taskData.task_users ? taskData.task_users.map(user => user.id) : [];
                    const taskCreatorId = taskData.user_creator;
                    
                    // Search for users
                    fetch(`search_users.php?term=${encodeURIComponent(searchTerm)}&task_id=${taskId}`)
                        .then(response => response.json())
                        .then(users => {
                            resultsContainer.innerHTML = '';
                            if (users.length === 0) {
                                resultsContainer.innerHTML = '<div class="list-group-item text-center">No users found</div>';
                                return;
                            }
                            users.forEach(user => {
                                // Skip if user is already assigned
                                if (assignedUserIds.includes(user.id)) {
                                    return;
                                }
                                
                                const userItem = document.createElement('button');
                                userItem.className = 'list-group-item list-group-item-action d-flex align-items-center';
                                userItem.innerHTML = `
                                    <img src="uploads/profiles/${user.avatar || 'no-profile.jpg'}" 
                                         alt="${user.name}" 
                                         class="rounded-circle me-2" 
                                         style="width: 32px; height: 32px; object-fit: cover;">
                                    <span>${user.name}</span>
                                `;
                                userItem.onclick = () => addUserToTask(taskId, user.id, addUserModal);
                                resultsContainer.appendChild(userItem);
                            });
                        })
                        .catch(error => {
                            console.error('Error searching users:', error);
                            resultsContainer.innerHTML = '<div class="list-group-item text-center text-danger">Error searching users</div>';
                        });
                })
                .catch(error => {
                    console.error('Error getting task details:', error);
                    resultsContainer.innerHTML = '<div class="list-group-item text-center text-danger">Error getting task details</div>';
                });
        }
    }
}

// Helper function to add user to task
function addUserToTask(taskId, userId, modal) {
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('user_id', userId);
    
    fetch('add_task_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            modal.hide();
            // Update the People Access tab directly
            updatePeopleAccessTab(taskId);
            Swal.fire({
                title: 'Success!',
                text: 'User added to task successfully.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            throw new Error(data.message || 'Failed to add user to task');
        }
    })
    .catch(error => {
        console.error('Error adding user to task:', error);
        Swal.fire({
            title: 'Error!',
            text: error.message || 'An error occurred while adding the user.',
            icon: 'error'
        });
    });
}

function removeTaskUser(taskId, userId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will remove the user from the task.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove user',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('task_id', taskId);
            formData.append('user_id', userId);
            
            fetch('remove_task_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update the People Access tab directly
                    updatePeopleAccessTab(taskId);
                    Swal.fire({
                        title: 'Success!',
                        text: 'User removed from task successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message || 'Failed to remove user from task');
                }
            })
            .catch(error => {
                console.error('Error removing user from task:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'An error occurred while removing the user.',
                    icon: 'error'
                });
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize audio blob variables
    window.recordedAudioBlob = null;
    window.editRecordedAudioBlob = null;

    // Check if Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not loaded');
        return;
    }

    // Initialize all modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        new bootstrap.Modal(modal);
    });

    const taskRows = document.querySelectorAll('.task-row');
    const taskDetailsOffcanvas = new bootstrap.Offcanvas(document.getElementById('taskDetailsOffcanvas'));
    const editTaskModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
    let currentTaskId = null;
    let filterTimeout = null;
    
    // Add manual event listener for the Task Details offcanvas close button
    const taskDetailsCloseBtn = document.querySelector('#taskDetailsOffcanvas .btn-close');
    if (taskDetailsCloseBtn) {
        taskDetailsCloseBtn.addEventListener('click', function() {
            taskDetailsOffcanvas.hide();
        });
    }
    
    // Add manual event listener for the Calendar offcanvas close button
    const calendarCloseBtn = document.querySelector('#calendarOffcanvas .btn-close');
    if (calendarCloseBtn) {
        calendarCloseBtn.addEventListener('click', function() {
            const calendarOffcanvas = new bootstrap.Offcanvas(document.getElementById('calendarOffcanvas'));
            calendarOffcanvas.hide();
        });
    }
    
    // Function to apply filters
    function applyFilters() {
        const status = document.getElementById('task-status').value;
        const priority = document.getElementById('task-priority').value;
        const category = document.getElementById('task-category').value;
        const allo_section = document.getElementById('task-allo-section').value;
        const label = document.getElementById('task-label').value;
        
        // Show loading state
        const tableBody = document.querySelector('.table tbody');
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
        
        // Make AJAX call to filter tasks
        fetch(`filter_tasks.php?status=${status}&priority=${priority}&category=${category}&allo_section=${allo_section}&label=${label}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Update table with filtered results
                tableBody.innerHTML = '';
                
                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center">No tasks found matching the selected filters</td></tr>';
                    return;
                }
                
                data.forEach(task => {
                    const statusText = {
                        '0': 'To Do',
                        '1': 'In Progress',
                        '2': 'Review',
                        '3': 'Done',
                        '4': 'Archived',
                        '5': 'To Debug'
                    }[task.status] || 'Unknown';
                    
                    const statusClass = {
                        '0': 'secondary',
                        '1': 'primary',
                        '2': 'warning',
                        '3': 'success',
                        '4': 'dark',
                        '5': 'danger'
                    }[task.status] || 'secondary';
                    
                    const priorityClass = {
                        'Low': 'info',
                        'Medium': 'primary',
                        'High': 'warning',
                        'Critical': 'danger',
                        'Hotfix': 'danger',
                        'Urgent': 'danger'
                    }[task.priority] || 'secondary';
                    
                    const row = document.createElement('tr');
                    row.className = 'task-row';
                    row.dataset.taskId = task.id;
                    row.style.cursor = 'pointer';
                    
                    row.innerHTML = `
                        <td>${task.id}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <img src="uploads/profiles/${task.creator_avatar || 'no-profile.jpg'}" 
                                         alt="${task.creator_name || 'Unknown'}" 
                                         class="rounded-circle me-2" 
                                         style="width: 32px; height: 32px; object-fit: cover;">
                                    ${task.title.length > 100 ? task.title.substring(0, 30) + '...' : task.title}
                                </div>
                                ${task.receiver_info ? `
                                <div class="mt-1">
                                    ${task.receiver_info.split('||').map((info, index) => {
                                        const [id, name, avatar] = info.split(':');
                                        return `
                                            <div class="profile-tooltip">
                                                <img src="uploads/profiles/${avatar || 'no-profile.jpg'}" 
                                                     alt="${name}" 
                                                     class="rounded-circle" 
                                                     style="width: 24px; height: 24px; object-fit: cover;">
                                                <span class="tooltip-text">${name}</span>
                                            </div>
                                        `;
                                    }).join('')}
                                </div>
                                ` : ''}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-${priorityClass}">
                                ${task.priority}
                            </span>
                        </td>
                        <td>${task.category}</td>
                        <td>${task.allo_section}</td>
                        <td>
                            ${task.label ? 
                                `<span class="badge badge-info">${task.label}</span>` : 
                                `<span class="text-muted">-</span>`
                            }
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress me-2" style="width: 60px; height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: ${task.calculated_progress || 0}%" 
                                         aria-valuenow="${task.calculated_progress || 0}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">${task.calculated_progress || 0}%</small>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item edit-task" href="#" data-task-id="${task.id}">
                                            <i class="fas fa-edit text-primary me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item print-task" href="#" data-task-id="${task.id}" title="Print Task">
                                            <i class="fas fa-print text-info me-2"></i>Print
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    ${task.status === '4' ? 
                                        `<li>
                                            <a class="dropdown-item unarchive-task" href="#" data-task-id="${task.id}" title="Unarchive Task">
                                                <i class="fas fa-undo text-success me-2"></i>Unarchive
                                            </a>
                                        </li>` : 
                                        `<li>
                                            <a class="dropdown-item archive-task" href="#" data-task-id="${task.id}" title="Archive Task">
                                                <i class="fas fa-archive text-dark me-2"></i>Archive
                                            </a>
                                        </li>`
                                    }
                                    <li>
                                        <a class="dropdown-item delete-task" href="#" data-task-id="${task.id}">
                                            <i class="fas fa-trash text-danger me-2"></i>Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    `;
                    
                    tableBody.appendChild(row);
                });
                
                // Reattach event listeners to new rows
                attachEventListeners();
            })
            .catch(error => {
                console.error('Error filtering tasks:', error);
                tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Error: ${error.message}</td></tr>`;
            });
    }
    
    // Add change event listeners to all filter selects
    document.querySelectorAll('.filter-select').forEach(select => {
        select.addEventListener('change', function() {
            // Clear any existing timeout
            if (filterTimeout) {
                clearTimeout(filterTimeout);
            }
            
            // Set a new timeout to apply filters after 300ms
            filterTimeout = setTimeout(applyFilters, 300);
        });
    });
    
    // Function to attach event listeners to task rows
    function attachEventListeners() {
        // Handle task row click for details
        document.querySelectorAll('.task-row').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('.edit-task') && !e.target.closest('.archive-task') && !e.target.closest('.unarchive-task') && !e.target.closest('.print-task') && !e.target.closest('.delete-task') && !e.target.closest('.dropdown')) {
                    const taskId = this.dataset.taskId;
                    currentTaskId = taskId;
                    loadTaskDetails(taskId);
                    taskDetailsOffcanvas.show();
                }
            });
        });
        
        // Handle edit dropdown clicks
        document.querySelectorAll('.edit-task').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const taskId = this.dataset.taskId;
                loadTaskForEdit(taskId);
                editTaskModal.show();
            });
        });
        
        // Handle archive dropdown clicks
        document.querySelectorAll('.archive-task').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const taskId = this.dataset.taskId;
                console.log('Archive item clicked for task:', taskId); // Debug log
                archiveTask(taskId);
            });
        });
        
        // Handle unarchive dropdown clicks
        document.querySelectorAll('.unarchive-task').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const taskId = this.dataset.taskId;
                console.log('Unarchive item clicked for task:', taskId); // Debug log
                unarchiveTask(taskId);
            });
        });
        
        // Handle print dropdown clicks
        document.querySelectorAll('.print-task').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const taskId = this.dataset.taskId;
                console.log('Print item clicked for task:', taskId); // Debug log
                printTask(taskId);
            });
        });
        
        // Handle delete dropdown clicks
        document.querySelectorAll('.delete-task').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const taskId = this.dataset.taskId;
                deleteTask(taskId);
            });
        });
    }
    
    // Attach event listeners on initial page load
    attachEventListeners();
    
    // Handle save changes button
    document.getElementById('saveTaskChanges').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('editTaskForm'));
        saveTaskChanges(formData);
    });
    
    function loadTaskForEdit(taskId) {
        fetch(`get_task_details.php?id=${taskId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('editTaskId').value = data.id;
                document.getElementById('editTitle').value = data.title;
                document.getElementById('editDescription').value = data.description || '';
                document.getElementById('editCategory').value = data.category;
                document.getElementById('editAlloSection').value = data.allo_section || '';
                document.getElementById('editLabel').value = data.label || '';
                document.getElementById('editPriority').value = data.priority;
                document.getElementById('editStatus').value = data.status;
                document.getElementById('editObjective').value = data.objective || '';
                document.getElementById('editProgress').value = data.progress || '0';
                document.getElementById('editDateStart').value = data.date_start || '';
                document.getElementById('editTimeStart').value = data.time_start || '';
                document.getElementById('editDateFinish').value = data.date_finish || '';
                document.getElementById('editTimeFinish').value = data.time_finish || '';
                document.getElementById('editRisks').value = data.risks || '';
                document.getElementById('editRequiredTools').value = data.required_tools || '';
                document.getElementById('editBudget').value = data.budget || '';
            })
            .catch(error => {
                console.error('Error loading task for edit:', error);
                alert('Error loading task details');
            });
    }
    
    function saveTaskChanges(formData) {
        fetch('update_task.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                editTaskModal.hide();
                Swal.fire({
                    title: 'Success!',
                    text: 'Task has been updated successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to update task.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error saving task changes:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while saving changes.',
                icon: 'error'
            });
        });
    }
    
    window.loadTaskDetails = function(taskId) {
        console.log('loadTaskDetails called with taskId:', taskId);
        currentTaskId = taskId; // Update current task ID
        
        // Store the currently active tab
        const activeTab = document.querySelector('.nav-tabs .nav-link.active');
        const activeTabId = activeTab ? activeTab.id : 'tasks-tab';
        
        // Show loading state
        const content = document.getElementById('taskDetailsContent');
        content.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading task details...</p></div>';
        
        fetch(`get_task_details.php?id=${taskId}`)
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Task details data received:', data);
                console.log('Data type:', typeof data);
                console.log('Data keys:', Object.keys(data));
                
                // Validate required data
                if (!data) {
                    throw new Error('No data received from server');
                }
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                if (!data.title) {
                    console.warn('Title is missing from data:', data);
                    data.title = 'Untitled Task';
                }
                
                if (!data.status) {
                    console.warn('Status is missing from data:', data);
                    data.status = '0';
                }
                
                if (!data.priority) {
                    console.warn('Priority is missing from data:', data);
                    data.priority = 'Medium';
                }
                
                // Use server-provided values or fallback to client-side calculation
                const statusText = data.statusText || {
                    '0': 'To Do',
                    '1': 'In Progress',
                    '2': 'Review',
                    '3': 'Done',
                    '4': 'Archived',
                    '5': 'To Debug'
                }[data.status] || 'Unknown';
                
                const statusClass = data.statusClass || {
                    '0': 'secondary',
                    '1': 'primary',
                    '2': 'warning',
                    '3': 'success',
                    '4': 'dark',
                    '5': 'danger'
                }[data.status] || 'secondary';
                
                const priorityClass = data.priorityClass || {
                    'Low': 'info',
                    'Medium': 'primary',
                    'High': 'warning',
                    'Critical': 'danger',
                    'Hotfix': 'danger',
                    'Urgent': 'danger'
                }[data.priority] || 'secondary';
                
                console.log('Using values:', {
                    statusText,
                    statusClass,
                    priorityClass,
                    title: data.title,
                    priority: data.priority
                });
                
                content.innerHTML = `
                    <div class="task-details">
                        <h4>${data.title || 'Untitled Task'}</h4>
                        <div class="task-meta">
                            <span class="badge badge-${statusClass}">${statusText}</span>
                            <span class="badge badge-${priorityClass}">${data.priority || 'Medium'}</span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="task-progress mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Task Progress</small>
                                <small class="text-muted">
                                    ${data.checklists && data.checklists.length > 0 
                                        ? Math.round((data.checklists.filter(item => item.status === '1').length / data.checklists.length) * 100) 
                                        : 0}%
                                </small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                     style="width: ${data.checklists && data.checklists.length > 0 
                                        ? Math.round((data.checklists.filter(item => item.status === '1').length / data.checklists.length) * 100) 
                                        : 0}%" 
                                     aria-valuenow="${data.checklists && data.checklists.length > 0 
                                        ? Math.round((data.checklists.filter(item => item.status === '1').length / data.checklists.length) * 100) 
                                        : 0}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">
                                    ${data.checklists ? data.checklists.filter(item => item.status === '1').length : 0} of ${data.checklists ? data.checklists.length : 0} tasks completed
                                </small>
                            </div>
                        </div>

                        <!-- Description Accordion -->
                        <div class="accordion mt-3" id="taskDescriptionAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="descriptionHeader">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#descriptionCollapse" aria-expanded="false" aria-controls="descriptionCollapse">
                                        <i class="fas fa-info-circle me-2"></i>Task Information
                                    </button>
                                </h2>
                                <div id="descriptionCollapse" class="accordion-collapse collapse" aria-labelledby="descriptionHeader" data-bs-parent="#taskDescriptionAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="info-item">
                                                    <small class="text-muted d-block mb-1">Description</small>
                                                    <p class="mb-0">${data.description || 'No description provided'}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="info-item">
                                                    <small class="text-muted d-block mb-1">Created By</small>
                                                    <div class="d-flex align-items-center">
                                                        <img src="uploads/profiles/${data.creator_avatar || 'no-profile.jpg'}" 
                                                             alt="${data.creator_name || 'Unknown User'}" 
                                                             class="rounded-circle me-2" 
                                                             style="width: 24px; height: 24px; object-fit: cover;">
                                                        <span>${data.creator_name || 'Unknown User'}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="info-item">
                                                    <small class="text-muted d-block mb-1">Created Date</small>
                                                    <p class="mb-0">${data.formatted_date || 'Unknown date'}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="info-item">
                                                    <small class="text-muted d-block mb-1">Assigned To</small>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        ${data.receiver_info ? data.receiver_info.split('||').map((info, index) => {
                                                            const [id, name, avatar] = info.split(':');
                                                            return `
                                                                <div class="profile-tooltip">
                                                                    <img src="uploads/profiles/${avatar || 'no-profile.jpg'}" 
                                                                         alt="${name || 'Unknown'}" 
                                                                         class="rounded-circle" 
                                                                         style="width: 24px; height: 24px; object-fit: cover;">
                                                                    <span class="tooltip-text">${name || 'Unknown'}</span>
                                                                </div>
                                                            `;
                                                        }).join('') : 'No assignees'}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <ul class="nav nav-tabs mt-4" id="taskTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link ${activeTabId === 'tasks-tab' ? 'active' : ''}" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab">
                                    <i class="fas fa-tasks"></i> Checklists (${data.checklists ? data.checklists.length : 0})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link ${activeTabId === 'archived-tab' ? 'active' : ''}" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archived" type="button" role="tab">
                                    <i class="fas fa-archive"></i> Archived
                                    <span class="badge bg-secondary ms-1" id="archivedCount">${data.archived_checklists ? data.archived_checklists.length : 0}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link ${activeTabId === 'files-tab' ? 'active' : ''}" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab">
                                    <i class="fas fa-file"></i> Files (${data.files ? data.files.length : 0})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link ${activeTabId === 'documents-tab' ? 'active' : ''}" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                                    <i class="fas fa-link"></i> Documents (${data.document_links ? data.document_links.length : 0})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link ${activeTabId === 'people-tab' ? 'active' : ''}" id="people-tab" data-bs-toggle="tab" data-bs-target="#people" type="button" role="tab">
                                    <i class="fas fa-users"></i> People Access (${data.task_users ? data.task_users.length : 0})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link ${activeTabId === 'notes-tab' ? 'active' : ''}" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">
                                    <i class="fas fa-sticky-note"></i> Notes (${data.notes ? data.notes.length : 0})
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="taskTabsContent">
                            <!-- Tasks Tab -->
                            <div class="tab-pane fade ${activeTabId === 'tasks-tab' ? 'show active' : ''}" id="tasks" role="tabpanel">
                                <div class="task-checklists">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Checklist</h6>
                                        <button class="btn btn-sm btn-primary" onclick="addChecklistItem(${data.id})">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button>
                                    </div>
                                    <div class="checklist-items" id="checklistItemsContainer">
                                        <!-- Active checklists will be rendered here by JavaScript -->
                                    </div>
                                </div>
                            </div>

                            <!-- Archived Tab -->
                            <div class="tab-pane fade ${activeTabId === 'archived-tab' ? 'show active' : ''}" id="archived" role="tabpanel">
                                <div class="archived-checklists">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-muted">
                                            <i class="fas fa-archive me-2"></i>Archived Checklist Items
                                        </h6>
                                    </div>
                                    <div class="archived-checklist-items" id="archivedChecklistItemsContainer">
                                        <!-- Archived checklists will be rendered here by JavaScript -->
                                    </div>
                                </div>
                            </div>

                            <!-- Files Tab -->
                            <div class="tab-pane fade ${activeTabId === 'files-tab' ? 'show active' : ''}" id="files" role="tabpanel">
                                <div class="files-section">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Task Files</h6>
                                        <button class="btn btn-sm btn-primary" onclick="addTaskFile(${data.id})">
                                            <i class="fas fa-plus"></i> Add File
                                        </button>
                                    </div>
                                    <div class="files-list">
                                        ${data.files && data.files.length > 0 ? data.files.map(file => `
                                            <div class="file-item d-flex align-items-center p-2 border rounded mb-2">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-file me-2"></i>
                                                            <span>${file.original_name}</span>
                                                            <small class="text-muted ms-2">(${formatFileSize(file.file_size)})</small>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <small class="text-muted me-2">
                                                                ${file.user_name} - ${file.formatted_date} ${file.formatted_time}
                                                            </small>
                                                            <div class="d-flex gap-2">
                                                                <a href="uploads/tasks/files/${file.file_name}" class="btn btn-sm btn-outline-primary" download="${file.original_name}">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteTaskFile(${file.id})">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('') : '<p class="text-muted">No files attached</p>'}
                                    </div>
                                </div>
                            </div>

                            <!-- Documents Tab -->
                            <div class="tab-pane fade ${activeTabId === 'documents-tab' ? 'show active' : ''}" id="documents" role="tabpanel">
                                <div class="document-links-section">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Document Links</h6>
                                        <button class="btn btn-sm btn-primary" onclick="addDocumentLink(${data.id})">
                                            <i class="fas fa-link"></i> Add Link
                                        </button>
                                    </div>
                                    <div class="document-links-list">
                                        ${data.document_links && data.document_links.length > 0 ? data.document_links.map(link => `
                                            <div class="document-link-item d-flex align-items-start p-2 border rounded mb-2">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-link me-2"></i>
                                                            <a href="${link.url}" target="_blank" class="text-primary">${link.title}</a>
                                                        </div>
                                                        <div class="d-flex gap-2">
                                                            <button class="btn btn-sm btn-outline-primary" 
                                                                    data-checklist='${JSON.stringify(link).replace(/'/g, "&#39;")}'
                                                                    onclick="editDocumentLink(this.dataset.checklist)">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-info" onclick="copyDocumentLink('${link.url}')">
                                                                <i class="fas fa-copy"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteDocumentLink(${link.id})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    ${link.description ? `<p class="mb-2 text-muted small">${link.description}</p>` : ''}
                                                    <div class="d-flex align-items-center">
                                                        <img src="uploads/profiles/${link.user_avatar || 'no-profile.jpg'}" 
                                                             alt="${link.user_name || 'Unknown User'}" 
                                                             class="rounded-circle me-2" 
                                                             style="width: 24px; height: 24px; object-fit: cover;">
                                                        <small class="text-muted">Added by ${link.user_name || 'Unknown User'} on ${link.formatted_date} ${link.formatted_time}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('') : '<p class="text-muted">No document links added</p>'}
                                    </div>
                                </div>
                            </div>

                            <!-- People Access Tab -->
                            <div class="tab-pane fade ${activeTabId === 'people-tab' ? 'show active' : ''}" id="people" role="tabpanel">
                                <div class="people-section">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Task Access</h6>
                                        <button class="btn btn-sm btn-primary" onclick="addTaskUser(${data.id})">
                                            <i class="fas fa-user-plus"></i> Add User
                                        </button>
                                    </div>
                                    <div class="people-list">
                                        ${data.task_users && data.task_users.length > 0 ? data.task_users.map(user => `
                                            <div class="user-item d-flex align-items-center p-2 border rounded mb-2">
                                                <img src="uploads/profiles/${user.avatar || 'no-profile.jpg'}" 
                                                     alt="${user.name || 'Unknown User'}" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>${user.name || 'Unknown User'}</span>
                                                        <small class="text-muted">${user.role || 'Member'}</small>
                                                    </div>
                                                </div>
                                                <div class="ms-2">
                                                    <button class="btn btn-sm btn-outline-danger" onclick="removeTaskUser(${data.id}, ${user.id})">
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        `).join('') : '<p class="text-muted">No users assigned</p>'}
                                    </div>
                                </div>
                            </div>

                            <!-- Notes Tab -->
                            <div class="tab-pane fade ${activeTabId === 'notes-tab' ? 'show active' : ''}" id="notes" role="tabpanel">
                                <div class="notes-section">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Task Notes</h6>
                                        <button class="btn btn-sm btn-primary" onclick="addTaskNote(${data.id})">
                                            <i class="fas fa-plus"></i> Add Note
                                        </button>
                                    </div>
                                    <div class="notes-list">
                                        ${data.notes && data.notes.length > 0 ? data.notes.map(note => `
                                            <div class="note-item d-flex align-items-start p-2 border rounded mb-2">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <img src="uploads/profiles/${note.user_avatar || 'no-profile.jpg'}" 
                                                                 alt="${note.user_name || 'Unknown User'}" 
                                                                 class="rounded-circle me-2" 
                                                                 style="width: 24px; height: 24px; object-fit: cover;">
                                                            <span class="fw-bold">${note.user_name || 'Unknown User'}</span>
                                                            <small class="text-muted ms-2">${note.formatted_date} ${note.formatted_time}</small>
                                                        </div>
                                                        <div class="d-flex gap-2">
                                                            <button class="btn btn-sm btn-outline-primary" onclick="editTaskNote(${JSON.stringify(note).replace(/"/g, '&quot;')})">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteTaskNote(${note.id})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <p class="mb-2" id="note-content-${note.id}">${note.note}</p>
                                                    ${note.file_name ? `
                                                        <div class="mt-2">
                                                            <a href="uploads/notes/${note.file_name}" class="btn btn-sm btn-outline-secondary" download>
                                                                <i class="fas fa-paperclip"></i> ${note.file_name}
                                                            </a>
                                                        </div>
                                                    ` : ''}
                                                </div>
                                            </div>
                                        `).join('') : '<p class="text-muted">No notes added</p>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Render hierarchical checklists (active items)
                if (data.checklists) {
                    console.log('Rendering active checklists:', data.checklists);
                    renderHierarchicalChecklists(data.checklists, 'checklistItemsContainer', data.id);
                } else {
                    console.log('No active checklists found in data');
                    const container = document.getElementById('checklistItemsContainer');
                    if (container) {
                        container.innerHTML = '<p class="text-muted">No active checklist items</p>';
                    }
                }

                // Render archived checklists
                if (data.archived_checklists && data.archived_checklists.length > 0) {
                    console.log('Rendering archived checklists:', data.archived_checklists);
                    renderArchivedChecklists(data.archived_checklists, 'archivedChecklistItemsContainer', data.id);
                    // Update archived count badge
                    const archivedCountBadge = document.getElementById('archivedCount');
                    if (archivedCountBadge) {
                        archivedCountBadge.textContent = data.archived_checklists.length;
                    }
                } else {
                    console.log('No archived checklists found in data');
                    const container = document.getElementById('archivedChecklistItemsContainer');
                    if (container) {
                        container.innerHTML = '<p class="text-muted">No archived items</p>';
                    }
                    // Update archived count badge
                    const archivedCountBadge = document.getElementById('archivedCount');
                    if (archivedCountBadge) {
                        archivedCountBadge.textContent = '0';
                    }
                }

                // Calculate and display task percentage relative to open tasks
                calculateTaskPercentage(data.id);
                
                // Mark notifications as read for this task if it's the first time opening
                markTaskNotificationsAsRead(taskId);

                // Always get or create the offcanvas instance and show it
                const offcanvasElem = document.getElementById('taskDetailsOffcanvas');
                let offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElem);
                if (!offcanvas) {
                    offcanvas = new bootstrap.Offcanvas(offcanvasElem);
                }
                offcanvas.show();
            })
            .catch(error => {
                console.error('Error loading task details:', error);
                console.error('Error stack:', error.stack);
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <h5>Error Loading Task Details</h5>
                        <p><strong>Error:</strong> ${error.message}</p>
                        <p><strong>Task ID:</strong> ${taskId}</p>
                        <p><strong>Time:</strong> ${new Date().toLocaleString()}</p>
                        <button class="btn btn-primary mt-2" onclick="loadTaskDetails(${taskId})">Retry</button>
                    </div>
                `;
            });
    }
    
    // Update the updateChecklistStatus function to refresh progress
    window.updateChecklistStatus = function(checklistId, status, checkbox) {
        const formData = new FormData();
        formData.append('checklist_id', checklistId);
        formData.append('status', status);
        
        // Update the text styling immediately
        const textElement = document.getElementById(`checklist-text-${checklistId}`);
        if (textElement) {
            if (status === '1') {
                textElement.classList.add('text-decoration-line-through', 'text-muted');
            } else {
                textElement.classList.remove('text-decoration-line-through', 'text-muted');
            }
        }
        
        fetch('update_checklist_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to update checklist status.',
                    icon: 'error'
                });
                // Revert the checkbox and text styling if there was an error
                checkbox.checked = !checkbox.checked;
                if (textElement) {
                    if (status === '1') {
                        textElement.classList.remove('text-decoration-line-through', 'text-muted');
                    } else {
                        textElement.classList.add('text-decoration-line-through', 'text-muted');
                    }
                }
            } else {
                // Reload the task details to show updated completion info
                loadTaskDetails(currentTaskId);
            }
        })
        .catch(error => {
            console.error('Error updating checklist status:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while updating the checklist status.',
                icon: 'error'
            });
            // Revert the checkbox and text styling if there was an error
            checkbox.checked = !checkbox.checked;
            if (textElement) {
                if (status === '1') {
                    textElement.classList.remove('text-decoration-line-through', 'text-muted');
                } else {
                    textElement.classList.add('text-decoration-line-through', 'text-muted');
                }
            }
        });
    };
    
    window.addChecklistItem = function(taskId) {
        document.getElementById('checklistTaskId').value = taskId;
        // Set default dates using local formatting
        const today = new Date();
        const formatLocalDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        const todayFormatted = formatLocalDate(today);
        document.getElementById('checklistStartDate').value = todayFormatted;
        document.getElementById('checklistEndDate').value = todayFormatted;
        
        // Populate parent dropdown
        populateParentDropdown(taskId, 'checklistParent');
        
        // Show modal
        const addChecklistModal = new bootstrap.Modal(document.getElementById('addChecklistItemModal'));
        addChecklistModal.show();
    };

    // Function to populate parent dropdown
    window.populateParentDropdown = function(taskId, dropdownId, excludeId = null) {
        const url = excludeId ? 
            `get_checklist_parents.php?task_id=${taskId}&exclude_id=${excludeId}` :
            `get_checklist_parents.php?task_id=${taskId}`;
            
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const dropdown = document.getElementById(dropdownId);
                // Clear existing options except the first one
                dropdown.innerHTML = '<option value="">No Parent (Main Item)</option>';
                
                if (data.success && data.checklists && data.checklists.length > 0) {
                    data.checklists.forEach(checklist => {
                        const option = document.createElement('option');
                        option.value = checklist.id;
                        option.textContent = checklist.content.length > 50 ? 
                            checklist.content.substring(0, 50) + '...' : 
                            checklist.content;
                        dropdown.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading checklists for parent dropdown:', error);
            });
    }

    // Function to flatten hierarchical checklists into a flat array
    function flattenChecklists(checklists, level = 0) {
        let flat = [];
        checklists.forEach(checklist => {
            // Add prefix to show hierarchy level
            const prefix = '─'.repeat(level);
            const checklistCopy = { ...checklist };
            checklistCopy.content = prefix + (prefix ? ' ' : '') + checklist.content;
            flat.push(checklistCopy);
            
            // Add children recursively
            if (checklist.children && checklist.children.length > 0) {
                flat = flat.concat(flattenChecklists(checklist.children, level + 1));
            }
        });
        return flat;
    }

    // Function to render hierarchical checklists
    function renderHierarchicalChecklists(checklists, containerId, taskId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        if (!checklists || checklists.length === 0) {
            container.innerHTML = '<p class="text-muted">No checklist items</p>';
            return;
        }
        
        container.innerHTML = '';
        checklists.forEach((checklist, index) => {
            renderChecklistItem(checklist, container, 0, taskId, index + 1);
        });
    }

    // Function to render archived checklists
    function renderArchivedChecklists(checklists, containerId, taskId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        if (!checklists || checklists.length === 0) {
            container.innerHTML = '<p class="text-muted">No archived items</p>';
            return;
        }
        
        container.innerHTML = '';
        checklists.forEach((checklist, index) => {
            renderArchivedChecklistItem(checklist, container, 0, taskId, index + 1);
        });
    }



    // Function to calculate task percentage relative to open tasks
    async function calculateTaskPercentage(taskId) {
        try {
            const response = await fetch('get_task_percentage.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `task_id=${taskId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Display the percentage in the task details header
                const percentageElement = document.getElementById('taskPercentage');
                if (percentageElement) {
                    percentageElement.innerHTML = `
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-chart-pie me-2"></i>
                            <strong>Task Weight:</strong> ${data.percentage}% of total open tasks
                            <br><small class="text-muted">Based on ${data.total_open_tasks} open tasks in the system</small>
                        </div>
                    `;
                }
            }
        } catch (error) {
            console.error('Error calculating task percentage:', error);
        }
    }

    // Function to render a single checklist item with its children
    function renderChecklistItem(checklist, container, level = 0, taskId = null, parentNumber = null) {
        const indentClass = level > 0 ? `ms-${level * 3}` : '';
        const indentStyle = level > 0 ? `margin-left: ${level * 20}px;` : '';
        
        // Generate hierarchical number
        let itemNumber = '';
        if (taskId && parentNumber) {
            if (level === 0) {
                // Root level items: taskId.parentNumber
                itemNumber = `${taskId}.${parentNumber}`;
            } else {
                // For child items, we need to find the correct child index
                // This will be calculated when we render the children
                itemNumber = `${taskId}.${parentNumber}`;
            }
        }
        
        // Determine if this is a parent or child
        const hasChildren = checklist.children && checklist.children.length > 0;
        const isChild = level > 0;
        const itemClasses = `checklist-item d-flex align-items-center mb-2 p-2 border rounded ${indentClass}`;
        const additionalClasses = hasChildren ? ' has-children' : (isChild ? ' is-child' : '');
        
        // Priority badge colors
        const priorityColors = {
            '1': 'badge-secondary', // Low
            '2': 'badge-info',      // Medium
            '3': 'badge-warning',   // High
            '4': 'badge-danger'     // Critical
        };
        
        const priorityText = {
            '1': 'Low',
            '2': 'Medium',
            '3': 'High',
            '4': 'Critical'
        };
        
        const priority = checklist.priority || '2';
        const priorityBadge = `<span class="badge ${priorityColors[priority]} me-2">${priorityText[priority]}</span>`;
        
        // Label badge
        const labelColors = {
            'feature': 'badge-success',
            'debug': 'badge-warning'
        };
        
        const labelText = {
            'feature': 'Feature',
            'debug': 'Debug'
        };
        
        const label = checklist.label || null;
        const labelBadge = label ? `<span class="badge ${labelColors[label]} me-2">${labelText[label]}</span>` : '';
        
        const checklistDiv = document.createElement('div');
        checklistDiv.className = itemClasses + additionalClasses;
        checklistDiv.style = indentStyle;
        checklistDiv.setAttribute('data-checklist-id', checklist.id);
        
        checklistDiv.innerHTML = `
            <div class="form-check me-2">
                <input class="form-check-input checklist-status" 
                       type="checkbox" 
                       id="checklist-${checklist.id}"
                       ${checklist.status === '1' ? 'checked' : ''}
                       onchange="updateChecklistStatus(${checklist.id}, this.checked ? '1' : '0', this)">
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        ${priorityBadge}
                        ${labelBadge}
                        <span id="checklist-text-${checklist.id}" class="${checklist.status === '1' ? 'text-decoration-line-through text-muted' : ''}">
                            ${itemNumber ? `<strong class="text-primary me-2">${itemNumber}</strong>` : ''}${checklist.content}
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header">Priority</h6></li>
                                <li><a class="dropdown-item ${priority === '1' ? 'active' : ''}" href="#" onclick="updateChecklistPriority(${checklist.id}, '1')">Low</a></li>
                                <li><a class="dropdown-item ${priority === '2' ? 'active' : ''}" href="#" onclick="updateChecklistPriority(${checklist.id}, '2')">Medium</a></li>
                                <li><a class="dropdown-item ${priority === '3' ? 'active' : ''}" href="#" onclick="updateChecklistPriority(${checklist.id}, '3')">High</a></li>
                                <li><a class="dropdown-item ${priority === '4' ? 'active' : ''}" href="#" onclick="updateChecklistPriority(${checklist.id}, '4')">Critical</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-warning" href="#" onclick="archiveChecklistItem(${checklist.id})">
                                    <i class="fas fa-archive"></i> Archive
                                </a></li>
                            </ul>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" 
                                data-checklist='${JSON.stringify(checklist).replace(/'/g, "&#39;")}'
                                onclick="editChecklistItem(this.dataset.checklist)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="showChecklistDetails(${JSON.stringify(checklist).replace(/"/g, '&quot;')})">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteChecklistItem(${checklist.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(checklistDiv);
        
        // Render children if they exist
        if (hasChildren) {
            checklist.children.forEach((child, childIndex) => {
                // For children, use the current item's number as the base
                const childNumber = `${itemNumber}.${childIndex + 1}`;
                renderChecklistItem(child, container, level + 1, taskId, childNumber);
            });
        }
    }

    // Function to render a single archived checklist item with its children
    function renderArchivedChecklistItem(checklist, container, level = 0, taskId = null, parentNumber = null) {
        const indentClass = level > 0 ? `ms-${level * 3}` : '';
        const indentStyle = level > 0 ? `margin-left: ${level * 20}px;` : '';
        
        // Generate hierarchical number
        let itemNumber = '';
        if (taskId && parentNumber) {
            if (level === 0) {
                // Root level items: taskId.parentNumber
                itemNumber = `${taskId}.${parentNumber}`;
            } else {
                // For child items, we need to find the correct child index
                // This will be calculated when we render the children
                itemNumber = `${taskId}.${parentNumber}`;
            }
        }
        
        // Determine if this is a parent or child
        const hasChildren = checklist.children && checklist.children.length > 0;
        const isChild = level > 0;
        const itemClasses = `checklist-item d-flex align-items-center mb-2 p-2 border rounded ${indentClass} archived-item`;
        const additionalClasses = hasChildren ? ' has-children' : (isChild ? ' is-child' : '');
        
        // Priority badge colors
        const priorityColors = {
            '1': 'badge-secondary', // Low
            '2': 'badge-info',      // Medium
            '3': 'badge-warning',   // High
            '4': 'badge-danger'     // Critical
        };
        
        const priorityText = {
            '1': 'Low',
            '2': 'Medium',
            '3': 'High',
            '4': 'Critical'
        };
        
        const priority = checklist.priority || '2';
        const priorityBadge = `<span class="badge ${priorityColors[priority]} me-2">${priorityText[priority]}</span>`;
        
        // Label badge
        const labelColors = {
            'feature': 'badge-success',
            'debug': 'badge-warning'
        };
        
        const labelText = {
            'feature': 'Feature',
            'debug': 'Debug'
        };
        
        const label = checklist.label || null;
        const labelBadge = label ? `<span class="badge ${labelColors[label]} me-2">${labelText[label]}</span>` : '';
        
        const checklistDiv = document.createElement('div');
        checklistDiv.className = itemClasses + additionalClasses;
        checklistDiv.style = indentStyle;
        checklistDiv.setAttribute('data-checklist-id', checklist.id);
        
        checklistDiv.innerHTML = `
            <div class="form-check me-2">
                <input class="form-check-input checklist-status" 
                       type="checkbox" 
                       id="archived-checklist-${checklist.id}"
                       ${checklist.status === '1' ? 'checked' : ''}
                       disabled>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="badge badge-secondary me-2">
                            <i class="fas fa-archive"></i> Archived
                        </span>
                        ${priorityBadge}
                        <span id="archived-checklist-text-${checklist.id}" class="text-muted">
                            ${itemNumber ? `<strong class="text-primary me-2">${itemNumber}</strong>` : ''}${checklist.content}
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-success" onclick="unarchiveChecklistItem(${checklist.id})">
                            <i class="fas fa-undo"></i> Restore
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="showChecklistDetails(${JSON.stringify(checklist).replace(/"/g, '&quot;')})">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteChecklistItem(${checklist.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(checklistDiv);
        
        // Render children if they exist
        if (hasChildren) {
            checklist.children.forEach((child, childIndex) => {
                // For children, use the current item's number as the base
                const childNumber = `${itemNumber}.${childIndex + 1}`;
                renderArchivedChecklistItem(child, container, level + 1, taskId, childNumber);
            });
        }
    }
    
    // Modify the existing saveChecklistItem event listener to handle recorded audio
    const saveChecklistItemBtn = document.getElementById('saveChecklistItem');
    if (saveChecklistItemBtn) {
        // Remove any existing event listeners
        const newSaveChecklistItemBtn = saveChecklistItemBtn.cloneNode(true);
        saveChecklistItemBtn.parentNode.replaceChild(newSaveChecklistItemBtn, saveChecklistItemBtn);
        
        newSaveChecklistItemBtn.addEventListener('click', function() {
            // Disable the button to prevent double submission
            this.disabled = true;
            
            const form = document.getElementById('addChecklistItemForm');
            const formData = new FormData(form);
            
            // Add the recorded audio blob if it exists
                            // Add the recorded audio blob if it exists and is valid
        if (window.recordedAudioBlob && window.recordedAudioBlob instanceof Blob && window.recordedAudioBlob.size > 0) {
            try {
                formData.append('audio', window.recordedAudioBlob, 'recorded_audio.wav');
                console.log('Audio blob appended successfully:', window.recordedAudioBlob.size, 'bytes');
            } catch (error) {
                console.error('Error appending audio blob:', error);
                window.recordedAudioBlob = null; // Clear invalid blob
            }
        } else if (window.recordedAudioBlob) {
            console.warn('Invalid audio blob detected, clearing:', typeof window.recordedAudioBlob);
            window.recordedAudioBlob = null;
        }
            
            fetch('add_checklist_item.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addChecklistItemModal')).hide();
                    form.reset();
                    document.getElementById('recordedAudio').classList.add('d-none');
                    window.recordedAudioBlob = null;
                    
                    Swal.fire({
                        title: 'Success!',
                        text: 'Checklist item has been added successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadTaskDetails(formData.get('task_id'));
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to add checklist item.',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error adding checklist item:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while adding the checklist item.',
                    icon: 'error'
                });
            })
            .finally(() => {
                // Re-enable the button after the request is complete
                this.disabled = false;
            });
        });
    }
    
    // Add event listener for saving task note
    document.getElementById('saveTaskNote').addEventListener('click', function() {
        const form = document.getElementById('addTaskNoteForm');
        const formData = new FormData(form);
        
        // Add current date and time using local formatting
        const now = new Date();
        const formatLocalDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        formData.append('date', formatLocalDate(now));
        formData.append('time', now.toTimeString().split(' ')[0]);
        
        fetch('add_task_note.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addTaskNoteModal')).hide();
                form.reset();
                Swal.fire({
                    title: 'Success!',
                    text: 'Note has been added successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                loadTaskDetails(formData.get('task_id'));
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to add note.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error adding note:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while adding the note.',
                icon: 'error'
            });
        });
    });

    // Add event listener for updating task note
    document.getElementById('updateTaskNote').addEventListener('click', function() {
        const form = document.getElementById('editTaskNoteForm');
        const formData = new FormData(form);
        
        fetch('update_task_note.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editTaskNoteModal')).hide();
                form.reset();
                Swal.fire({
                    title: 'Success!',
                    text: 'Note has been updated successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                loadTaskDetails(currentTaskId);
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to update note.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error updating note:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while updating the note.',
                icon: 'error'
            });
        });
    });

    // Add document link functions
    window.addDocumentLink = function(taskId) {
        document.getElementById('documentTaskId').value = taskId;
        const addDocumentModal = new bootstrap.Modal(document.getElementById('addDocumentLinkModal'));
        addDocumentModal.show();
    };

    window.editDocumentLink = function(docData) {
        console.log('editDocumentLink called with:', docData);
        
        // Ensure docData is properly parsed if it's a string
        if (typeof docData === 'string') {
            try {
                docData = JSON.parse(docData);
                console.log('Parsed document:', docData);
            } catch (e) {
                console.error('Error parsing document data:', e);
                return;
            }
        }
        
        // Set the form values
        document.getElementById('editDocumentId').value = docData.id;
        document.getElementById('editDocumentTitle').value = docData.title;
        document.getElementById('editDocumentUrl').value = docData.url;
        document.getElementById('editDocumentDescription').value = docData.description || '';
        
        // Get the modal element
        const modalElement = document.getElementById('editDocumentLinkModal');
        if (!modalElement) {
            console.error('Modal element not found');
            return;
        }
        
        // Try to get existing modal instance or create new one
        let editDocumentModal = bootstrap.Modal.getInstance(modalElement);
        if (!editDocumentModal) {
            editDocumentModal = new bootstrap.Modal(modalElement);
        }
        
        // Show the modal
        editDocumentModal.show();
    };

    window.deleteDocumentLink = function(documentId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('delete_document_link.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `document_id=${documentId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Document link has been deleted.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadTaskDetails(currentTaskId);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to delete document link.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error deleting document link:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the document link.',
                        icon: 'error'
                    });
                });
            }
        });
    };

    // Add event listener for saving document link
    document.getElementById('saveDocumentLink').addEventListener('click', function() {
        const form = document.getElementById('addDocumentLinkForm');
        const formData = new FormData(form);
        
        // Add current date and time using local formatting
        const now = new Date();
        const formatLocalDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        formData.append('date', formatLocalDate(now));
        formData.append('time', now.toTimeString().split(' ')[0]);
        
        fetch('add_document_link.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addDocumentLinkModal')).hide();
                form.reset();
                Swal.fire({
                    title: 'Success!',
                    text: 'Document link has been added successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                loadTaskDetails(currentTaskId);
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to add document link.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error adding document link:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while adding the document link.',
                icon: 'error'
            });
        });
    });

    // Add event listener for updating document link
    document.getElementById('updateDocumentLink').addEventListener('click', function() {
        const form = document.getElementById('editDocumentLinkForm');
        const formData = new FormData(form);
        
        // Validate required fields
        const title = formData.get('title');
        const url = formData.get('url');
        
        if (!title || !url) {
            Swal.fire({
                title: 'Error!',
                text: 'Title and URL are required fields.',
                icon: 'error'
            });
            return;
        }
        
        fetch('update_document_link.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide the modal
                const editDocumentModal = bootstrap.Modal.getInstance(document.getElementById('editDocumentLinkModal'));
                editDocumentModal.hide();
                
                // Reset the form
                form.reset();
                
                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: 'Document link has been updated successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                
                // Reload task details to show updated data
                loadTaskDetails(currentTaskId);
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to update document link.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error updating document link:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while updating the document link.',
                icon: 'error'
            });
        });
    });

    // Add event listener for saving new task
    document.getElementById('saveNewTask').addEventListener('click', function() {
        const form = document.getElementById('newTaskForm');
        const formData = new FormData(form);
        
        // Add current date and time using local formatting
        const now = new Date();
        const formatLocalDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        formData.append('date_create', formatLocalDate(now));
        formData.append('time_create', now.toTimeString().split(' ')[0]);
        formData.append('status', '0'); // Set default status to Created
        formData.append('user_creator', '<?php echo $my_profile_id; ?>'); // Add creator ID
        
        fetch('create_task.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('newTaskModal')).hide();
                form.reset();
                Swal.fire({
                    title: 'Success!',
                    text: 'Task has been created successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to create task.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error creating task:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while creating the task.',
                icon: 'error'
            });
        });
    });

    // Add event listener for task deletion
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-task')) {
            const taskId = e.target.closest('.delete-task').dataset.taskId;
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('task_id', taskId);
                    
                    fetch('delete_task.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Task has been deleted successfully.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to delete task.',
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting task:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while deleting the task.',
                            icon: 'error'
                        });
                    });
                }
            });
        }
    });

    // Function to format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Function to add task file
    window.addTaskFile = function(taskId) {
        document.getElementById('fileTaskId').value = taskId;
        const modal = new bootstrap.Modal(document.getElementById('addTaskFileModal'));
        modal.show();
    };

    // Function to delete task file
    window.deleteTaskFile = function(fileId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This file will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('file_id', fileId);

                fetch('delete_task_file.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'File has been deleted.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadTaskDetails(currentTaskId);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to delete file.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error deleting file:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the file.',
                        icon: 'error'
                    });
                });
            }
        });
    };

    // Add event listener for saving task file
    document.getElementById('saveTaskFile').addEventListener('click', function() {
        const form = document.getElementById('addTaskFileForm');
        const formData = new FormData(form);
        
        // Add current date and time using local formatting
        const now = new Date();
        const formatLocalDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        formData.append('date', formatLocalDate(now));
        formData.append('time', now.toTimeString().split(' ')[0]);
        
        fetch('add_task_file.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addTaskFileModal'));
                modal.hide();
                form.reset();
                
                Swal.fire({
                    title: 'Success!',
                    text: 'File has been added.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                
                loadTaskDetails(currentTaskId);
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to add file.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error adding file:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while adding the file.',
                icon: 'error'
            });
        });
    });

    window.deleteChecklistItem = function(checklistId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('checklist_id', checklistId);
                
                fetch('delete_checklist_item.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Checklist item has been deleted.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadTaskDetails(currentTaskId);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to delete checklist item.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error deleting checklist item:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the checklist item.',
                        icon: 'error'
                    });
                });
            }
        });
    };

    // Function to update checklist priority
    window.updateChecklistPriority = function(checklistId, priority) {
        const formData = new FormData();
        formData.append('checklist_id', checklistId);
        formData.append('priority', priority);
        
        fetch('update_checklist_priority_archive.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Priority updated successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                loadTaskDetails(currentTaskId);
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to update priority.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error updating priority:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while updating priority.',
                icon: 'error'
            });
        });
    };

    // Function to archive a checklist item
    window.archiveChecklistItem = function(checklistId) {
        Swal.fire({
            title: 'Archive Checklist Item?',
            text: "This item will be moved to archived items. You can unarchive it later.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, archive it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('checklist_id', checklistId);
                formData.append('archive_status', '1');
                
                fetch('update_checklist_priority_archive.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Archived!',
                            text: 'Checklist item has been archived.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        // Update archived count badge immediately
                        const archivedCountBadge = document.getElementById('archivedCount');
                        if (archivedCountBadge) {
                            const currentCount = parseInt(archivedCountBadge.textContent) || 0;
                            archivedCountBadge.textContent = currentCount + 1;
                        }
                        loadTaskDetails(currentTaskId);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to archive checklist item.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error archiving checklist item:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while archiving the checklist item.',
                        icon: 'error'
                    });
                });
            }
        });
    };

    // Function to unarchive a checklist item
    window.unarchiveChecklistItem = function(checklistId) {
        const formData = new FormData();
        formData.append('checklist_id', checklistId);
        formData.append('archive_status', '0');
        
        fetch('update_checklist_priority_archive.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
                        .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Unarchived!',
                            text: 'Checklist item has been restored.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        // Update archived count badge immediately
                        const archivedCountBadge = document.getElementById('archivedCount');
                        if (archivedCountBadge) {
                            const currentCount = parseInt(archivedCountBadge.textContent) || 0;
                            archivedCountBadge.textContent = Math.max(0, currentCount - 1);
                        }
                        loadTaskDetails(currentTaskId);
                    } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to unarchive checklist item.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error unarchiving checklist item:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while unarchiving the checklist item.',
                icon: 'error'
            });
        });
    };

    window.showChecklistDetails = function(checklist) {
        // Format dates if they exist
        const formatDate = (dateStr) => {
            if (!dateStr) return 'Not set';
            return new Date(dateStr).toLocaleDateString();
        };

        // Helper function to get file extension
        const getFileExtension = (filename) => {
            return filename.split('.').pop().toLowerCase();
        };

        // Helper function to check if file is an image
        const isImageFile = (filename) => {
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
            return imageExtensions.includes(getFileExtension(filename));
        };

        // Helper function to check if file is an audio file
        const isAudioFile = (filename) => {
            const audioExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'];
            return audioExtensions.includes(getFileExtension(filename));
        };

        // Update modal content
        document.getElementById('detailContent').textContent = checklist.content;
        document.getElementById('detailStartDate').textContent = formatDate(checklist.start_date);
        document.getElementById('detailEndDate').textContent = formatDate(checklist.end_date);
        document.getElementById('detailCreatedBy').textContent = checklist.user_name || 'Unknown';
        document.getElementById('detailCreatedDate').textContent = `${checklist.date} ${checklist.time}`;
        
        // Add status information
        document.getElementById('detailStatus').textContent = checklist.status === '1' ? 'Completed' : 'Pending';
        document.getElementById('detailCompletedBy').textContent = checklist.finished_by || 'Not completed yet';
        document.getElementById('detailCompletionDate').textContent = checklist.finished_at ? formatDate(checklist.finished_at) : 'Not completed yet';

        // Handle attachments
        const attachmentsContainer = document.getElementById('detailAttachments');
        attachmentsContainer.innerHTML = ''; // Clear existing attachments

        let hasAttachments = false;

        // Add file attachment if exists
        if (checklist.file_path) {
            hasAttachments = true;
            const fileExtension = getFileExtension(checklist.file_path);
            const isImage = isImageFile(checklist.file_path);
            const isAudio = isAudioFile(checklist.file_path);
            
            const attachmentDiv = document.createElement('div');
            attachmentDiv.className = 'attachment-item border rounded p-3';
            
            if (isImage) {
                // Show image preview
                attachmentDiv.innerHTML = `
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-image text-primary me-2"></i>
                        <span class="fw-bold">Image Attachment</span>
                    </div>
                    <div class="text-center mb-3">
                        <img src="uploads/checklists/files/${checklist.file_path}" 
                             alt="${checklist.file_path}" 
                             class="img-fluid rounded" 
                             style="max-height: 300px; max-width: 100%; object-fit: contain;">
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">${checklist.file_path}</small>
                        <a href="uploads/checklists/files/${checklist.file_path}" 
                           class="btn btn-sm btn-outline-primary" 
                           download="${checklist.file_path}">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                `;
            } else if (isAudio) {
                // Show audio player
                attachmentDiv.innerHTML = `
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-music text-primary me-2"></i>
                        <span class="fw-bold">Audio Attachment</span>
                    </div>
                    <div class="mb-3">
                        <audio controls class="w-100">
                            <source src="uploads/checklists/files/${checklist.file_path}" type="audio/${fileExtension}">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">${checklist.file_path}</small>
                        <a href="uploads/checklists/files/${checklist.file_path}" 
                           class="btn btn-sm btn-outline-primary" 
                           download="${checklist.file_path}">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                `;
            } else {
                // Show generic file with download button
                attachmentDiv.innerHTML = `
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-file text-primary me-2"></i>
                        <span class="fw-bold">File Attachment</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 fw-bold">${checklist.file_path}</p>
                            <small class="text-muted">File type: ${fileExtension.toUpperCase()}</small>
                        </div>
                        <a href="uploads/checklists/files/${checklist.file_path}" 
                           class="btn btn-sm btn-outline-primary" 
                           download="${checklist.file_path}">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                `;
            }
            
            attachmentsContainer.appendChild(attachmentDiv);
        }

        // Add audio attachment if exists (separate from file attachment)
        if (checklist.audio_path) {
            hasAttachments = true;
            const audioExtension = getFileExtension(checklist.audio_path);
            
            const audioDiv = document.createElement('div');
            audioDiv.className = 'attachment-item border rounded p-3';
            audioDiv.innerHTML = `
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-microphone text-success me-2"></i>
                    <span class="fw-bold">Audio Recording</span>
                </div>
                <div class="mb-3">
                    <audio controls class="w-100">
                        <source src="uploads/checklists/audio/${checklist.audio_path}" type="audio/${audioExtension}">
                        Your browser does not support the audio element.
                    </audio>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">${checklist.audio_path}</small>
                    <a href="uploads/checklists/audio/${checklist.audio_path}" 
                       class="btn btn-sm btn-outline-success" 
                       download="${checklist.audio_path}">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            `;
            
            attachmentsContainer.appendChild(audioDiv);
        }

        // If no attachments, show message
        if (!hasAttachments) {
            attachmentsContainer.innerHTML = '<p class="text-muted mb-0">No attachments</p>';
        }

        // Show the modal
        const detailsModal = new bootstrap.Modal(document.getElementById('checklistItemDetailsModal'));
        detailsModal.show();
    };

    window.addTaskNote = function(taskId) {
        document.getElementById('noteTaskId').value = taskId;
        // Clear any previous content
        document.getElementById('noteContent').value = '';
        document.getElementById('noteFile').value = '';
        // Show modal
        const addNoteModal = new bootstrap.Modal(document.getElementById('addTaskNoteModal'));
        addNoteModal.show();
    };

    window.editTaskNote = function(note) {
        document.getElementById('editNoteId').value = note.id;
        document.getElementById('editNoteContent').value = note.note;
        
        // Show current file if exists
        const currentFileDiv = document.getElementById('currentFile');
        if (note.file_name) {
            currentFileDiv.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-paperclip"></i> Current file: ${note.file_name}
                </div>
            `;
        } else {
            currentFileDiv.innerHTML = '';
        }
        
        // Show modal
        const editNoteModal = new bootstrap.Modal(document.getElementById('editTaskNoteModal'));
        editNoteModal.show();
    };

    window.deleteTaskNote = function(noteId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('note_id', noteId);
                
                fetch('delete_task_note.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Note has been deleted.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadTaskDetails(currentTaskId);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to delete note.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error deleting note:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the note.',
                        icon: 'error'
                    });
                });
            }
        });
    };

    // Add copy document link function
    window.copyDocumentLink = async function(url) {
        try {
            // Use the modern Clipboard API
            await navigator.clipboard.writeText(url);
            
            // Show success message
            Swal.fire({
                title: 'Link Copied!',
                text: 'The document link has been copied to your clipboard.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        } catch (err) {
            // Fallback for browsers that don't support Clipboard API
            const tempInput = document.createElement('input');
            tempInput.value = url;
            document.body.appendChild(tempInput);
            tempInput.select();
            
            try {
                document.execCommand('copy');
                // Show success message
                Swal.fire({
                    title: 'Link Copied!',
                    text: 'The document link has been copied to your clipboard.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            } catch (err) {
                // Show error message if both methods fail
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to copy link to clipboard.',
                    icon: 'error',
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            } finally {
                document.body.removeChild(tempInput);
            }
        }
    };

    // Voice recording functionality
    let mediaRecorder;
    let audioChunks = [];

    document.getElementById('startRecording').addEventListener('click', async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            
            // Show recording controls
            document.getElementById('startRecording').classList.add('d-none');
            document.getElementById('stopRecording').classList.remove('d-none');
            document.getElementById('recordingStatus').classList.remove('d-none');
            
            // Clear previous recording
            audioChunks = [];
            
            mediaRecorder.addEventListener('dataavailable', event => {
                audioChunks.push(event.data);
            });
            
            mediaRecorder.addEventListener('stop', () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                const audioUrl = URL.createObjectURL(audioBlob);
                const audioElement = document.getElementById('recordedAudio');
                audioElement.src = audioUrl;
                audioElement.classList.remove('d-none');
                
                // Store the blob globally for form submission
                window.recordedAudioBlob = audioBlob;
            });
            
            mediaRecorder.start();
        } catch (err) {
            console.error('Error accessing microphone:', err);
            Swal.fire({
                title: 'Error!',
                text: 'Could not access microphone. Please ensure you have granted microphone permissions.',
                icon: 'error'
            });
        }
    });

    document.getElementById('stopRecording').addEventListener('click', () => {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
            
            // Hide recording controls
            document.getElementById('startRecording').classList.remove('d-none');
            document.getElementById('stopRecording').classList.add('d-none');
            document.getElementById('recordingStatus').classList.add('d-none');
        }
    });

    // Function to archive a task
    function archiveTask(taskId) {
        Swal.fire({
            title: 'Archive Task?',
            text: 'This will move the task to archived status. You can still view it by filtering for archived tasks.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, archive it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('task_id', taskId);
                formData.append('status', '4'); // Set status to archived
                
                fetch('update_task.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Archived!',
                            text: 'Task has been archived successfully.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to archive task.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error archiving task:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while archiving the task.',
                        icon: 'error'
                    });
                });
            }
        });
    }
    
    // Function to unarchive a task
    function unarchiveTask(taskId) {
        Swal.fire({
            title: 'Unarchive Task?',
            text: 'This will move the task back to "In Progress" status.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, unarchive it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('task_id', taskId);
                formData.append('status', '1'); // Set status to "In Progress"
                
                fetch('update_task.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Unarchived!',
                            text: 'Task has been moved to "In Progress" status.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to unarchive task.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error unarchiving task:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while unarchiving the task.',
                        icon: 'error'
                    });
                });
            }
        });
    }
    
    // Function to delete a task
    function deleteTask(taskId) {
        Swal.fire({
            title: 'Delete Task?',
            text: 'This action cannot be undone. The task will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('task_id', taskId);
                
                fetch('delete_task.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Task has been deleted successfully.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to delete task.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error deleting task:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the task.',
                        icon: 'error'
                    });
                });
            }
        });
    }

    // Function to print a task
    function printTask(taskId) {
        // Show loading state
        Swal.fire({
            title: 'Loading...',
            text: 'Preparing task for printing',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`get_task_details.php?id=${taskId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Close loading dialog
                Swal.close();
                
                // Create print window content
                const printContent = createPrintContent(data);
                
                // Open new window for printing
                const printWindow = window.open('', '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
                printWindow.document.write(printContent);
                printWindow.document.close();
                
                // Wait for content to load then print
                printWindow.onload = function() {
                    printWindow.print();
                    printWindow.close();
                };
            })
            .catch(error => {
                console.error('Error loading task for printing:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to load task details for printing.',
                    icon: 'error'
                });
            });
    }
    
    // Function to create print content
    function createPrintContent(taskData) {
        const statusText = taskData.statusText || {
            '0': 'To Do',
            '1': 'In Progress',
            '2': 'Review',
            '3': 'Done',
            '4': 'Archived'
        }[taskData.status] || 'Unknown';
        
        const priorityText = taskData.priority || 'Medium';
        
        // Calculate progress
        const totalChecklists = taskData.checklists ? taskData.checklists.length : 0;
        const completedChecklists = taskData.checklists ? taskData.checklists.filter(item => item.status === '1').length : 0;
        const progressPercentage = totalChecklists > 0 ? Math.round((completedChecklists / totalChecklists) * 100) : 0;
        
        // Format dates
        const formatDate = (dateStr) => {
            if (!dateStr) return 'Not set';
            return new Date(dateStr).toLocaleDateString();
        };
        
        // Create hierarchical checklist HTML
        const renderChecklistItem = (item, level = 0, parentNumber = null) => {
            const indent = '&nbsp;'.repeat(level * 4);
            const status = item.status === '1' ? '✓ Completed' : '○ Pending';
            const statusClass = item.status === '1' ? 'completed' : 'pending';
            
            // Generate hierarchical number
            let itemNumber = '';
            if (taskData.id && parentNumber) {
                if (level === 0) {
                    // Root level items: taskId.parentNumber
                    itemNumber = `${taskData.id}.${parentNumber}`;
                } else {
                    // Child items: use the parent number passed down
                    itemNumber = parentNumber;
                }
            }
            
            let html = `
                <div class="checklist-item" style="margin-left: ${level * 20}px; margin-bottom: 8px;">
                    <span class="checklist-status ${statusClass}">${status}</span>
                    <span class="checklist-content">
                        ${itemNumber ? `<strong style="color: #007bff; margin-right: 8px;">${itemNumber}</strong>` : ''}${indent}${item.content}
                    </span>
                </div>
            `;
            
            if (item.children && item.children.length > 0) {
                item.children.forEach((child, childIndex) => {
                    // For children, use the current item's number as the base
                    const childNumber = `${itemNumber}.${childIndex + 1}`;
                    html += renderChecklistItem(child, level + 1, childNumber);
                });
            }
            
            return html;
        };
        
        const checklistsHtml = taskData.checklists && taskData.checklists.length > 0 
            ? taskData.checklists.map((item, index) => renderChecklistItem(item, 0, index + 1)).join('')
            : '<p class="no-data">No checklist items</p>';
        
        // Create notes HTML
        const notesHtml = taskData.notes && taskData.notes.length > 0 
            ? taskData.notes.map(note => `
                <div class="note-item" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
                    <div class="note-header" style="font-weight: bold; margin-bottom: 5px;">
                        ${note.user_name || 'Unknown User'} - ${note.formatted_date} ${note.formatted_time}
                    </div>
                    <div class="note-content" style="white-space: pre-wrap;">${note.note}</div>
                    ${note.file_name ? `
                        <div class="note-attachment" style="margin-top: 5px; font-size: 12px; color: #666;">
                            📎 Attachment: ${note.file_name}
                        </div>
                    ` : ''}
                </div>
            `).join('')
            : '<p class="no-data">No notes</p>';
        
        // Create files HTML
        const filesHtml = taskData.files && taskData.files.length > 0 
            ? taskData.files.map(file => `
                <div class="file-item" style="border: 1px solid #ddd; padding: 8px; margin-bottom: 8px; border-radius: 4px;">
                    <div style="font-weight: bold;">📎 ${file.original_name}</div>
                    <div style="font-size: 12px; color: #666;">
                        Added by ${file.user_name} on ${file.formatted_date} ${file.formatted_time}
                    </div>
                </div>
            `).join('')
            : '<p class="no-data">No files attached</p>';
        
        // Create document links HTML
        const documentsHtml = taskData.document_links && taskData.document_links.length > 0 
            ? taskData.document_links.map(doc => `
                <div class="document-item" style="border: 1px solid #ddd; padding: 8px; margin-bottom: 8px; border-radius: 4px;">
                    <div style="font-weight: bold;">🔗 ${doc.title}</div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">${doc.url}</div>
                    ${doc.description ? `<div style="font-size: 12px; margin-bottom: 5px;">${doc.description}</div>` : ''}
                    <div style="font-size: 12px; color: #666;">
                        Added by ${doc.user_name} on ${doc.formatted_date} ${doc.formatted_time}
                    </div>
                </div>
            `).join('')
            : '<p class="no-data">No document links</p>';
        
        // Create assigned users HTML
        const usersHtml = taskData.task_users && taskData.task_users.length > 0 
            ? taskData.task_users.map(user => `
                <div class="user-item" style="display: inline-block; margin-right: 15px; margin-bottom: 10px; text-align: center;">
                    <div style="font-weight: bold;">${user.name}</div>
                    <div style="font-size: 12px; color: #666;">${user.role || 'Member'}</div>
                </div>
            `).join('')
            : '<p class="no-data">No users assigned</p>';
        
        return `
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Task Report - ${taskData.title}</title>
                <style>
                    @media print {
                        body { margin: 0; padding: 20px; }
                        .no-print { display: none !important; }
                        .page-break { page-break-before: always; }
                    }
                    
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        color: #333;
                        max-width: 800px;
                        margin: 0 auto;
                        padding: 20px;
                    }
                    
                    .header {
                        text-align: center;
                        border-bottom: 3px solid #007bff;
                        padding-bottom: 20px;
                        margin-bottom: 30px;
                    }
                    
                    .task-title {
                        font-size: 28px;
                        font-weight: bold;
                        color: #007bff;
                        margin-bottom: 10px;
                    }
                    
                    .task-meta {
                        display: flex;
                        justify-content: center;
                        gap: 20px;
                        margin-bottom: 20px;
                    }
                    
                    .badge {
                        padding: 5px 12px;
                        border-radius: 20px;
                        font-size: 12px;
                        font-weight: bold;
                        text-transform: uppercase;
                    }
                    
                    .badge-primary { background-color: #007bff; color: white; }
                    .badge-success { background-color: #28a745; color: white; }
                    .badge-warning { background-color: #ffc107; color: #212529; }
                    .badge-danger { background-color: #dc3545; color: white; }
                    .badge-info { background-color: #17a2b8; color: white; }
                    .badge-secondary { background-color: #6c757d; color: white; }
                    .badge-dark { background-color: #343a40; color: white; }
                    
                    .progress-section {
                        background-color: #f8f9fa;
                        padding: 15px;
                        border-radius: 8px;
                        margin-bottom: 30px;
                    }
                    
                    .progress-bar {
                        background-color: #e9ecef;
                        height: 20px;
                        border-radius: 10px;
                        overflow: hidden;
                        margin: 10px 0;
                    }
                    
                    .progress-fill {
                        background-color: #007bff;
                        height: 100%;
                        transition: width 0.3s ease;
                    }
                    
                    .section {
                        margin-bottom: 30px;
                        page-break-inside: avoid;
                    }
                    
                    .section-title {
                        font-size: 18px;
                        font-weight: bold;
                        color: #007bff;
                        border-bottom: 2px solid #007bff;
                        padding-bottom: 5px;
                        margin-bottom: 15px;
                    }
                    
                    .info-grid {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 20px;
                        margin-bottom: 20px;
                    }
                    
                    .info-item {
                        background-color: #f8f9fa;
                        padding: 15px;
                        border-radius: 6px;
                        border-left: 4px solid #007bff;
                    }
                    
                    .info-label {
                        font-size: 12px;
                        font-weight: bold;
                        text-transform: uppercase;
                        color: #666;
                        margin-bottom: 5px;
                    }
                    
                    .info-value {
                        font-size: 14px;
                        font-weight: 500;
                    }
                    
                    .checklist-status {
                        font-weight: bold;
                        margin-right: 10px;
                    }
                    
                    .checklist-status.completed {
                        color: #28a745;
                    }
                    
                    .checklist-status.pending {
                        color: #6c757d;
                    }
                    
                    .checklist-content {
                        font-size: 14px;
                    }
                    
                    .no-data {
                        color: #666;
                        font-style: italic;
                        text-align: center;
                        padding: 20px;
                        background-color: #f8f9fa;
                        border-radius: 6px;
                    }
                    
                    .print-button {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background-color: #007bff;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 5px;
                        cursor: pointer;
                        font-size: 14px;
                    }
                    
                    .print-button:hover {
                        background-color: #0056b3;
                    }
                    
                    @media print {
                        .print-button { display: none; }
                    }
                </style>
            </head>
            <body>
                <button class="print-button no-print" onclick="window.print()">🖨️ Print</button>
                
                <div class="header">
                    <div class="task-title">${taskData.title}</div>
                    <div class="task-meta">
                        <span class="badge badge-${taskData.statusClass || 'secondary'}">${statusText}</span>
                        <span class="badge badge-${taskData.priorityClass || 'secondary'}">${priorityText}</span>
                    </div>
                </div>
                
                <div class="progress-section">
                    <div style="text-align: center; margin-bottom: 10px;">
                        <strong>Task Progress: ${progressPercentage}%</strong>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${progressPercentage}%;"></div>
                    </div>
                    <div style="text-align: center; font-size: 12px; color: #666;">
                        ${completedChecklists} of ${totalChecklists} tasks completed
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">📋 Task Information</div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Category</div>
                            <div class="info-value">${taskData.category || 'Not specified'}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Allo Section</div>
                            <div class="info-value">${taskData.allo_section || 'Not specified'}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Created By</div>
                            <div class="info-value">${taskData.creator_name || 'Unknown'}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Created Date</div>
                            <div class="info-value">${taskData.formatted_date || 'Unknown'}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Start Date</div>
                            <div class="info-value">${formatDate(taskData.date_start)}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Finish Date</div>
                            <div class="info-value">${formatDate(taskData.date_finish)}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Budget</div>
                            <div class="info-value">${taskData.budget || 'Not specified'}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Objective</div>
                            <div class="info-value">${taskData.objective || 'Not specified'}</div>
                        </div>
                    </div>
                    
                    ${taskData.description ? `
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">Description</div>
                            <div class="info-value" style="white-space: pre-wrap;">${taskData.description}</div>
                        </div>
                    ` : ''}
                    
                    ${taskData.risks ? `
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">Risks</div>
                            <div class="info-value" style="white-space: pre-wrap;">${taskData.risks}</div>
                        </div>
                    ` : ''}
                    
                    ${taskData.required_tools ? `
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">Required Tools</div>
                            <div class="info-value" style="white-space: pre-wrap;">${taskData.required_tools}</div>
                        </div>
                    ` : ''}
                </div>
                
                <div class="section">
                    <div class="section-title">👥 Assigned Team</div>
                    <div style="text-align: center;">
                        ${usersHtml}
                    </div>
                </div>
                
                <div class="section page-break">
                    <div class="section-title">✅ Checklist Items</div>
                    <div class="checklist-container">
                        ${checklistsHtml}
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">📝 Notes</div>
                    <div class="notes-container">
                        ${notesHtml}
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">📎 Attached Files</div>
                    <div class="files-container">
                        ${filesHtml}
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">🔗 Document Links</div>
                    <div class="documents-container">
                        ${documentsHtml}
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
                    <p>Generated on ${new Date().toLocaleString()}</p>
                    <p>AlloHub ERP System - Task Report</p>
                </div>
            </body>
            </html>
        `;
    }
});
</script>

<!-- Add Task File Modal -->
<div class="modal fade" id="addTaskFileModal" tabindex="-1" aria-labelledby="addTaskFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskFileModalLabel">Add File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTaskFileForm">
                    <input type="hidden" id="fileTaskId" name="task_id">
                    <div class="mb-3">
                        <label for="taskFile" class="form-label">File</label>
                        <input type="file" class="form-control" id="taskFile" name="file" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTaskFile">Add File</button>
            </div>
        </div>
    </div>
</div>

<!-- Calendar Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="calendarOffcanvas" aria-labelledby="calendarOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="calendarOffcanvasLabel">Calendar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Task Filter Section -->
        <div class="task-filter-section mb-4">
            <h6 class="mb-3">
                <i class="fas fa-filter"></i> Task Filter
                <button class="btn btn-sm btn-outline-primary ms-2" onclick="selectAllTasks()">
                    <i class="fas fa-check-square"></i> Select All
                </button>
                <button class="btn btn-sm btn-outline-secondary ms-1" onclick="deselectAllTasks()">
                    <i class="fas fa-square"></i> Deselect All
                </button>
            </h6>
            <div class="task-checkboxes-container" id="taskCheckboxesContainer">
                <!-- Task checkboxes will be populated here -->
                <div class="text-center text-muted">
                    <i class="fas fa-spinner fa-spin"></i> Loading tasks...
                </div>
            </div>
        </div>
        
        <div id="calendar"></div>
    </div>
</div>

<!-- Add FullCalendar CSS and JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<script>
// Global variables for task filtering
let allCalendarEvents = [];
let selectedTaskIds = new Set();
let calendar = null;

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendarOffcanvas = new bootstrap.Offcanvas(document.getElementById('calendarOffcanvas'));
    const taskDetailsOffcanvas = new bootstrap.Offcanvas(document.getElementById('taskDetailsOffcanvas'));
    
    // Load tasks for filter checkboxes
    loadTaskFilterCheckboxes();
    
    // Helper function to show meeting details
    function showMeetingDetails(meetingId) {
        fetch(`get_meeting_details.php?id=${meetingId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const content = document.getElementById('taskDetailsContent');
                    content.innerHTML = `
                        <div class="task-details">
                            <h4>📅 ${data.meeting.title}</h4>
                            <div class="task-meta">
                                <span class="badge badge-danger">Meeting</span>
                                <span class="badge badge-${data.meeting.broadcast_type === 'all' ? 'success' : 'warning'}">${data.meeting.broadcast_type}</span>
                            </div>
                            
                            <!-- Meeting Info -->
                            <div class="task-info-line mt-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="info-item">
                                            <small class="text-muted d-block mb-1">Creator</small>
                                            <p class="mb-0">${data.meeting.creator_name}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-item">
                                            <small class="text-muted d-block mb-1">Date & Time</small>
                                            <p class="mb-0">${data.meeting.meeting_date} at ${data.meeting.meeting_time}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-item">
                                            <small class="text-muted d-block mb-1">Broadcast Type</small>
                                            <p class="mb-0"><span class="badge bg-${data.meeting.broadcast_type === 'all' ? 'success' : 'warning'}">${data.meeting.broadcast_type}</span></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-item">
                                            <small class="text-muted d-block mb-1">Created</small>
                                            <p class="mb-0">${data.meeting.created_at}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            ${data.meeting.description ? `
                            <!-- Description -->
                            <div class="mt-4">
                                <h6>Description</h6>
                                <div class="alert alert-light">${data.meeting.description}</div>
                            </div>
                            ` : ''}
                            
                            <!-- Response Summary -->
                            <div class="mt-4">
                                <h6>Response Summary</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <h4 class="text-primary">${data.total_responses}</h4>
                                                <small>Total Responses</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <h4 class="text-success">${data.accepted}</h4>
                                                <small>Accepted</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <h4 class="text-danger">${data.declined}</h4>
                                                <small>Declined</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            ${data.responses && data.responses.length > 0 ? `
                            <!-- Individual Responses -->
                            <div class="mt-4">
                                <h6>Individual Responses</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Response</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${data.responses.map(response => `
                                                <tr>
                                                    <td>${response.user_name}</td>
                                                    <td><span class="text-${response.response === 'accept' ? 'success' : 'danger'}">${response.response === 'accept' ? '✅ Accept' : '❌ Decline'}</span></td>
                                                    <td><small>${response.created_at}</small></td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            ` : `
                            <div class="mt-4">
                                <div class="alert alert-info">No responses yet.</div>
                            </div>
                            `}
                        </div>
                    `;
                    
                    // Show the task details offcanvas
                    taskDetailsOffcanvas.show();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to load meeting details.',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error loading meeting details:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to load meeting details.',
                    icon: 'error'
                });
            });
    }
    
    // Helper function to show task details
    function showTaskDetails(taskId) {
        fetch(`get_task_details.php?id=${taskId}`)
            .then(response => response.json())
            .then(data => {
                const content = document.getElementById('taskDetailsContent');
                content.innerHTML = `
                    <div class="task-details">
                        <h4>${data.title}</h4>
                        <div class="task-meta">
                            <span class="badge badge-${data.statusClass}">${data.statusText}</span>
                            <span class="badge badge-${data.priorityClass}">${data.priority}</span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="task-progress mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Task Progress</small>
                                <small class="text-muted">
                                    ${data.checklists && data.checklists.length > 0 
                                        ? Math.round((data.checklists.filter(item => item.status === '1').length / data.checklists.length) * 100) 
                                        : 0}%
                                </small>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: ${data.checklists && data.checklists.length > 0 
                                        ? (data.checklists.filter(item => item.status === '1').length / data.checklists.length) * 100 
                                        : 0}%">
                                </div>
                            </div>
                        </div>

                        <!-- Task Info -->
                        <div class="task-info-line mt-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <small class="text-muted d-block mb-1">Created By</small>
                                        <div class="d-flex align-items-center">
                                            <img src="uploads/profiles/${data.creator_avatar || 'no-profile.jpg'}" 
                                                 alt="${data.creator_name}" 
                                                 class="rounded-circle me-2" 
                                                 style="width: 24px; height: 24px; object-fit: cover;">
                                            <span class="text-truncate">${data.creator_name}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <small class="text-muted d-block mb-1">Created Date</small>
                                        <p class="mb-0">${data.formatted_date}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <small class="text-muted d-block mb-1">Start Date</small>
                                        <p class="mb-0">${data.date_start || 'Not set'}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <small class="text-muted d-block mb-1">Finish Date</small>
                                        <p class="mb-0">${data.date_finish || 'Not set'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <ul class="nav nav-tabs mt-4" id="taskTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab">
                                    <i class="fas fa-tasks"></i> Tasks
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="taskTabsContent">
                            <!-- Tasks Tab -->
                            <div class="tab-pane fade show active" id="tasks" role="tabpanel">
                                <div class="task-checklists">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Checklist</h6>
                                        <button class="btn btn-sm btn-primary" onclick="addChecklistItem(${data.id})">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button>
                                    </div>
                                    <div class="checklist-items" id="checklistItemsContainer">
                                        <!-- Checklists will be rendered here by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Show the task details offcanvas
                taskDetailsOffcanvas.show();
            })
            .catch(error => {
                console.error('Error loading task details:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to load task details.',
                    icon: 'error'
                });
            });
    }
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridYear,dayGridMonth,timeGridWeek,timeGridDay'
        },
        views: {
            dayGridYear: {
                type: 'dayGrid',
                duration: { years: 1 },
                buttonText: 'Year'
            }
        },
        editable: true, // Enable drag and drop
        eventDrop: function(info) {
            const taskId = info.event.id.replace('task_', '');
            const newStartDate = info.event.start;
            const newEndDate = info.event.end || info.event.start;
            
            // Use local date formatting instead of UTC to prevent timezone shift
            const formatLocalDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            // Update the dates in the database
            const formData = new FormData();
            formData.append('task_id', taskId);
            formData.append('date_start', formatLocalDate(newStartDate));
            formData.append('time_start', newStartDate.toTimeString().split(' ')[0]);
            formData.append('date_finish', formatLocalDate(newEndDate));
            formData.append('time_finish', newEndDate.toTimeString().split(' ')[0]);
            
            fetch('update_task.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to update task dates.',
                        icon: 'error'
                    });
                    // Revert the event if there was an error
                    info.revert();
                } else {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Task dates updated successfully',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                console.error('Error updating task dates:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while updating task dates.',
                    icon: 'error'
                });
                // Revert the event if there was an error
                info.revert();
            });
        },
        eventResize: function(info) {
            const taskId = info.event.id.replace('task_', '');
            const newStartDate = info.event.start;
            const newEndDate = info.event.end;
            
            // Use local date formatting instead of UTC to prevent timezone shift
            const formatLocalDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            // Update the dates in the database
            const formData = new FormData();
            formData.append('task_id', taskId);
            formData.append('date_start', formatLocalDate(newStartDate));
            formData.append('date_finish', formatLocalDate(newEndDate));
            formData.append('time_start', newStartDate.toTimeString().split(' ')[0]);
            formData.append('time_finish', newEndDate.toTimeString().split(' ')[0]);
            
            fetch('update_task.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to update task dates.',
                        icon: 'error'
                    });
                    // Revert the event if there was an error
                    info.revert();
                } else {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Task dates updated successfully',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                console.error('Error updating task dates:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while updating task dates.',
                    icon: 'error'
                });
                // Revert the event if there was an error
                info.revert();
            });
        },
        events: function(info, successCallback, failureCallback) {
            fetch('get_calendar_events.php')
                .then(response => response.json())
                .then(events => {
                    // Store all events globally
                    allCalendarEvents = events;
                    
                    // Filter events based on selected task IDs
                    const filteredEvents = events.filter(event => {
                        if (event.extendedProps.type === 'meeting') {
                            // Always show meetings
                            return true;
                        } else if (event.extendedProps.type === 'task') {
                            // Only show tasks that are selected
                            return selectedTaskIds.has(event.extendedProps.task_id.toString());
                        }
                        return true;
                    });
                    
                    successCallback(filteredEvents);
                })
                .catch(error => {
                    console.error('Error fetching calendar events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            const eventType = info.event.extendedProps.type;
            
            // Close calendar offcanvas first
            calendarOffcanvas.hide();
            
            // Wait for the calendar offcanvas to close before showing details
            setTimeout(() => {
                if (eventType === 'meeting') {
                    // Handle meeting event
                    const meetingId = info.event.extendedProps.meeting_id;
                    showMeetingDetails(meetingId);
                } else {
                    // Handle task event - use loadTaskDetails instead of showTaskDetails for complete functionality
                    const taskId = info.event.extendedProps.task_id;
                    loadTaskDetails(taskId);
                }
            }, 300); // Wait for 300ms to ensure smooth transition
        },
        eventDidMount: function(info) {
            const eventType = info.event.extendedProps.type;
            
            if (eventType === 'meeting') {
                // Add meeting-event class for red background styling
                info.el.classList.add('meeting-event');
                
                // Add tooltip for meeting events
                const meetingTitle = info.event.extendedProps.meeting_title;
                const createdBy = info.event.extendedProps.created_by;
                const creatorName = info.event.extendedProps.creator_name;
                
                info.el.title = `Meeting: ${meetingTitle}\nCreated by: ${createdBy}\nCreator: ${creatorName}`;
            } else {
                // Add tooltip for task events
                const taskTitle = info.event.extendedProps.task_title;
                const createdBy = info.event.extendedProps.created_by;
                const status = info.event.extendedProps.status === '1' ? 'Completed' : 'Pending';
                
                info.el.title = `Task: ${taskTitle}\nCreated by: ${createdBy}\nStatus: ${status}`;
            }
        }
    });
    calendar.render();
});

// Function to load task filter checkboxes
function loadTaskFilterCheckboxes() {
    fetch('get_calendar_events.php')
        .then(response => response.json())
        .then(events => {
            const container = document.getElementById('taskCheckboxesContainer');
            const taskEvents = events.filter(event => event.extendedProps.type === 'task');
            
            if (taskEvents.length === 0) {
                container.innerHTML = '<div class="text-center text-muted">No tasks found</div>';
                return;
            }
            
            // Sort tasks by title
            taskEvents.sort((a, b) => a.extendedProps.task_title.localeCompare(b.extendedProps.task_title));
            
            let html = '';
            taskEvents.forEach(event => {
                const taskId = event.extendedProps.task_id;
                const taskTitle = event.extendedProps.task_title;
                const status = event.extendedProps.status;
                
                // Map status to text and class
                let statusText = '';
                let statusClass = '';
                switch(status) {
                    case '0': statusText = 'To Do'; statusClass = 'created'; break;
                    case '1': statusText = 'In Progress'; statusClass = 'in-progress'; break;
                    case '2': statusText = 'Review'; statusClass = 'review'; break;
                    case '3': statusText = 'Done'; statusClass = 'completed'; break;
                    default: statusText = 'Unknown'; statusClass = 'created'; break;
                }
                
                html += `
                    <div class="task-checkbox-item">
                        <input type="checkbox" id="task_${taskId}" value="${taskId}" checked onchange="toggleTaskFilter(${taskId})">
                        <label for="task_${taskId}" title="${taskTitle}">${taskTitle}</label>
                        <span class="task-status ${statusClass}">${statusText}</span>
                    </div>
                `;
                
                // Add to selected tasks by default
                selectedTaskIds.add(taskId.toString());
            });
            
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading task filter checkboxes:', error);
            document.getElementById('taskCheckboxesContainer').innerHTML = 
                '<div class="text-center text-danger">Error loading tasks</div>';
        });
}

// Function to toggle task filter
function toggleTaskFilter(taskId) {
    const taskIdStr = taskId.toString();
    const checkbox = document.getElementById(`task_${taskId}`);
    
    if (checkbox.checked) {
        selectedTaskIds.add(taskIdStr);
    } else {
        selectedTaskIds.delete(taskIdStr);
    }
    
    // Refresh calendar events
    if (calendar) {
        calendar.refetchEvents();
    }
}

// Function to select all tasks
function selectAllTasks() {
    const checkboxes = document.querySelectorAll('#taskCheckboxesContainer input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
        selectedTaskIds.add(checkbox.value);
    });
    
    if (calendar) {
        calendar.refetchEvents();
    }
}

// Function to deselect all tasks
function deselectAllTasks() {
    const checkboxes = document.querySelectorAll('#taskCheckboxesContainer input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        selectedTaskIds.delete(checkbox.value);
    });
    
    if (calendar) {
        calendar.refetchEvents();
    }
}
</script>

<!-- Edit Checklist Item Modal -->
<div class="modal fade" id="editChecklistItemModal" tabindex="-1" aria-labelledby="editChecklistItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editChecklistItemModalLabel">Edit Checklist Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editChecklistItemForm">
                    <input type="hidden" id="editChecklistId" name="checklist_id">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="editChecklistContent" class="form-label">Content</label>
                            <textarea class="form-control" id="editChecklistContent" name="content" rows="3" required></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editChecklistParent" class="form-label">Parent Checklist (Optional)</label>
                            <select class="form-select" id="editChecklistParent" name="parent_id">
                                <option value="">No Parent (Main Item)</option>
                                <!-- Parent options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editChecklistStartDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="editChecklistStartDate" name="start_date">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="editChecklistEndDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="editChecklistEndDate" name="end_date">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="editChecklistLabel" class="form-label">Label (Optional)</label>
                            <select class="form-select" id="editChecklistLabel" name="label">
                                <option value="">No Label</option>
                                <option value="feature">Feature</option>
                                <option value="debug">Debug</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="editChecklistFile" class="form-label">File Attachment</label>
                            <input type="file" class="form-control" id="editChecklistFile" name="file">
                            <div id="currentFile" class="mt-2"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="editChecklistAudio" class="form-label">Audio Attachment</label>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary" id="editStartRecording">
                                        <i class="fas fa-microphone"></i> Start Recording
                                    </button>
                                    <button type="button" class="btn btn-danger d-none" id="editStopRecording">
                                        <i class="fas fa-stop"></i> Stop Recording
                                    </button>
                                </div>
                                <div id="editRecordingStatus" class="text-muted small d-none">
                                    Recording in progress...
                                </div>
                                <audio id="editRecordedAudio" controls class="d-none mt-2"></audio>
                                <input type="hidden" id="editRecordedAudioBlob" name="recorded_audio">
                                <div id="currentAudio" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEditChecklistItem">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
// ... existing code ...

window.editChecklistItem = function(checklistStr) {
    let checklist;
    try {
        checklist = typeof checklistStr === 'string' ? JSON.parse(checklistStr) : checklistStr;
    } catch (e) {
        console.error('Failed to parse checklist data:', e, checklistStr);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to load checklist item for editing.',
            icon: 'error'
        });
        return;
    }
    
    // Populate the form with checklist data
    document.getElementById('editChecklistId').value = checklist.id;
    document.getElementById('editChecklistContent').value = checklist.content;
    document.getElementById('editChecklistStartDate').value = checklist.start_date || '';
    document.getElementById('editChecklistEndDate').value = checklist.end_date || '';
    document.getElementById('editChecklistLabel').value = checklist.label || '';
    
    // Add task_id to the form
    const taskIdInput = document.createElement('input');
    taskIdInput.type = 'hidden';
    taskIdInput.name = 'task_id';
    taskIdInput.value = checklist.task_id;
    document.getElementById('editChecklistItemForm').appendChild(taskIdInput);
    
    // Populate parent dropdown and set current parent
    populateParentDropdown(checklist.task_id, 'editChecklistParent', checklist.id);
    setTimeout(() => {
        document.getElementById('editChecklistParent').value = checklist.parent_id || '';
    }, 100);
    
    // Show current file if exists
    const currentFileDiv = document.getElementById('currentFile');
    if (checklist.file_path) {
        currentFileDiv.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-paperclip"></i> Current file: ${checklist.file_path}
                <button type="button" class="btn btn-sm btn-danger float-end" onclick="removeChecklistFile(${checklist.id})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    } else {
        currentFileDiv.innerHTML = '';
    }
    
    // Show current audio if exists
    const currentAudioDiv = document.getElementById('currentAudio');
    if (checklist.audio_path) {
        currentAudioDiv.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-microphone"></i> Current audio recording
                <button type="button" class="btn btn-sm btn-danger float-end" onclick="removeChecklistAudio(${checklist.id})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    } else {
        currentAudioDiv.innerHTML = '';
    }
    
    // Show modal
    const editModal = new bootstrap.Modal(document.getElementById('editChecklistItemModal'));
    editModal.show();
};

// Edit recording functionality
let editMediaRecorder;
let editAudioChunks = [];

// Add event listeners for edit recording
document.addEventListener('DOMContentLoaded', function() {
    // Edit recording start button
    const editStartRecordingBtn = document.getElementById('editStartRecording');
    if (editStartRecordingBtn) {
        editStartRecordingBtn.addEventListener('click', async () => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                editMediaRecorder = new MediaRecorder(stream);
                
                // Show recording controls
                document.getElementById('editStartRecording').classList.add('d-none');
                document.getElementById('editStopRecording').classList.remove('d-none');
                document.getElementById('editRecordingStatus').classList.remove('d-none');
                
                // Clear previous recording
                editAudioChunks = [];
                
                editMediaRecorder.addEventListener('dataavailable', event => {
                    editAudioChunks.push(event.data);
                });
                
                editMediaRecorder.addEventListener('stop', () => {
                    const audioBlob = new Blob(editAudioChunks, { type: 'audio/wav' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    const audioElement = document.getElementById('editRecordedAudio');
                    audioElement.src = audioUrl;
                    audioElement.classList.remove('d-none');
                    
                    // Store the blob globally for form submission
                    window.editRecordedAudioBlob = audioBlob;
                });
                
                editMediaRecorder.start();
            } catch (err) {
                console.error('Error accessing microphone:', err);
                Swal.fire({
                    title: 'Error!',
                    text: 'Could not access microphone. Please ensure you have granted microphone permissions.',
                    icon: 'error'
                });
            }
        });
    }

    // Edit recording stop button
    const editStopRecordingBtn = document.getElementById('editStopRecording');
    if (editStopRecordingBtn) {
        editStopRecordingBtn.addEventListener('click', () => {
            if (editMediaRecorder && editMediaRecorder.state !== 'inactive') {
                editMediaRecorder.stop();
                editMediaRecorder.stream.getTracks().forEach(track => track.stop());
                
                // Hide recording controls
                document.getElementById('editStartRecording').classList.remove('d-none');
                document.getElementById('editStopRecording').classList.add('d-none');
                document.getElementById('editRecordingStatus').classList.add('d-none');
            }
        });
    }

    // Add event listener for saving edited checklist item
    const saveEditChecklistItemBtn = document.getElementById('saveEditChecklistItem');
    if (saveEditChecklistItemBtn) {
        saveEditChecklistItemBtn.addEventListener('click', function() {
            // Disable the button to prevent double submission
            this.disabled = true;
            
            const form = document.getElementById('editChecklistItemForm');
            const formData = new FormData(form);
            
            // Add the recorded audio blob if it exists
                            // Add the recorded audio blob if it exists and is valid
        if (window.editRecordedAudioBlob && window.editRecordedAudioBlob instanceof Blob && window.editRecordedAudioBlob.size > 0) {
            try {
                formData.append('audio', window.editRecordedAudioBlob, 'recorded_audio.wav');
                console.log('Edit audio blob appended successfully:', window.editRecordedAudioBlob.size, 'bytes');
            } catch (error) {
                console.error('Error appending edit audio blob:', error);
                window.editRecordedAudioBlob = null; // Clear invalid blob
            }
        } else if (window.editRecordedAudioBlob) {
            console.warn('Invalid edit audio blob detected, clearing:', typeof window.editRecordedAudioBlob);
            window.editRecordedAudioBlob = null;
        }
            
            // Log form data for debugging
            console.log('Sending form data:', Object.fromEntries(formData));
            
            fetch('edit_checklist_item.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data); // Debug log
                if (data.success) {
                    // Close only the edit modal
                    const editModal = bootstrap.Modal.getInstance(document.getElementById('editChecklistItemModal'));
                    if (editModal) {
                        editModal.hide();
                    }
                    
                    // Reset only the edit form
                    form.reset();
                    document.getElementById('editRecordedAudio').classList.add('d-none');
                    window.editRecordedAudioBlob = null;
                    document.getElementById('currentFile').innerHTML = '';
                    document.getElementById('currentAudio').innerHTML = '';
                    
                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: 'Checklist item has been updated successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Update the checklist item in the UI without reloading
                    const checklistId = formData.get('checklist_id');
                    const newContent = formData.get('content');
                    const newStartDate = formData.get('start_date');
                    const newEndDate = formData.get('end_date');
                    
                    // Update the checklist text
                    const checklistText = document.getElementById(`checklist-text-${checklistId}`);
                    if (checklistText) {
                        checklistText.textContent = newContent;
                    }

                    // Update the details modal if it's open
                    const detailsModal = document.getElementById('checklistItemDetailsModal');
                    if (detailsModal && detailsModal.classList.contains('show')) {
                        // Update the content
                        const detailContent = document.getElementById('detailContent');
                        if (detailContent) {
                            detailContent.textContent = newContent;
                        }

                        // Update the dates
                        const detailStartDate = document.getElementById('detailStartDate');
                        if (detailStartDate && newStartDate) {
                            detailStartDate.textContent = new Date(newStartDate).toLocaleDateString();
                        }

                        const detailEndDate = document.getElementById('detailEndDate');
                        if (detailEndDate && newEndDate) {
                            detailEndDate.textContent = new Date(newEndDate).toLocaleDateString();
                        }

                        // Update attachments if they exist
                        const attachmentsContainer = document.getElementById('detailAttachments');
                        if (attachmentsContainer) {
                            // Clear existing attachments
                            attachmentsContainer.innerHTML = '';

                            let hasAttachments = false;

                            // Helper function to get file extension
                            const getFileExtension = (filename) => {
                                return filename.split('.').pop().toLowerCase();
                            };

                            // Helper function to check if file is an image
                            const isImageFile = (filename) => {
                                const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
                                return imageExtensions.includes(getFileExtension(filename));
                            };

                            // Helper function to check if file is an audio file
                            const isAudioFile = (filename) => {
                                const audioExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'];
                                return audioExtensions.includes(getFileExtension(filename));
                            };

                            // Add file attachment if exists
                            const fileInput = document.getElementById('editChecklistFile');
                            if (fileInput && fileInput.files.length > 0) {
                                hasAttachments = true;
                                const file = fileInput.files[0];
                                const fileExtension = getFileExtension(file.name);
                                const isImage = isImageFile(file.name);
                                const isAudio = isAudioFile(file.name);
                                
                                const attachmentDiv = document.createElement('div');
                                attachmentDiv.className = 'attachment-item border rounded p-3';
                                
                                if (isImage) {
                                    // Show image preview
                                    attachmentDiv.innerHTML = `
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-image text-primary me-2"></i>
                                            <span class="fw-bold">Image Attachment</span>
                                        </div>
                                        <div class="text-center mb-3">
                                            <img src="${URL.createObjectURL(file)}" 
                                                 alt="${file.name}" 
                                                 class="img-fluid rounded" 
                                                 style="max-height: 300px; max-width: 100%; object-fit: contain;">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">${file.name}</small>
                                            <a href="${URL.createObjectURL(file)}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               download="${file.name}">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    `;
                                } else if (isAudio) {
                                    // Show audio player
                                    attachmentDiv.innerHTML = `
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-music text-primary me-2"></i>
                                            <span class="fw-bold">Audio Attachment</span>
                                        </div>
                                        <div class="mb-3">
                                            <audio controls class="w-100">
                                                <source src="${URL.createObjectURL(file)}" type="audio/${fileExtension}">
                                                Your browser does not support the audio element.
                                            </audio>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">${file.name}</small>
                                            <a href="${URL.createObjectURL(file)}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               download="${file.name}">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    `;
                                } else {
                                    // Show generic file with download button
                                    attachmentDiv.innerHTML = `
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-file text-primary me-2"></i>
                                            <span class="fw-bold">File Attachment</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-1 fw-bold">${file.name}</p>
                                                <small class="text-muted">File type: ${fileExtension.toUpperCase()}</small>
                                            </div>
                                            <a href="${URL.createObjectURL(file)}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               download="${file.name}">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    `;
                                }
                                
                                attachmentsContainer.appendChild(attachmentDiv);
                            }

                            // Add audio attachment if exists (separate from file attachment)
                            if (window.editRecordedAudioBlob) {
                                const audioBlob = URL.createObjectURL(window.editRecordedAudioBlob);
                                hasAttachments = true;
                                
                                const audioDiv = document.createElement('div');
                                audioDiv.className = 'attachment-item border rounded p-3';
                                audioDiv.innerHTML = `
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-microphone text-success me-2"></i>
                                        <span class="fw-bold">Audio Recording</span>
                                    </div>
                                    <div class="mb-3">
                                        <audio controls class="w-100">
                                            <source src="${audioBlob}" type="audio/wav">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">New Audio Recording</small>
                                        <a href="${audioBlob}" 
                                           class="btn btn-sm btn-outline-success" 
                                           download="audio_recording.wav">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                `;
                                
                                attachmentsContainer.appendChild(audioDiv);
                            }

                            // If no attachments, show message
                            if (!hasAttachments) {
                                attachmentsContainer.innerHTML = '<p class="text-muted mb-0">No attachments</p>';
                            }
                        }
                    }

                    // If we're in task details view and loadTaskDetails exists, update the task details
                    const taskId = formData.get('task_id');
                    if (taskId && typeof loadTaskDetails === 'function') {
                        loadTaskDetails(taskId);
                    }
                } else {
                    throw new Error(data.message || 'Failed to update checklist item');
                }
            })
            .catch(error => {
                console.error('Error updating checklist item:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'An error occurred while updating the checklist item.',
                    icon: 'error'
                });
            })
            .finally(() => {
                // Re-enable the button after the request is complete
                this.disabled = false;
            });
        });
    }
});

window.removeChecklistFile = function(checklistId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will remove the current file attachment!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('checklist_id', checklistId);
            formData.append('remove_file', '1');
            
            fetch('edit_checklist_item.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('currentFile').innerHTML = '';
                    Swal.fire({
                        title: 'Removed!',
                        text: 'File attachment has been removed.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to remove file attachment.',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error removing file:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while removing the file attachment.',
                    icon: 'error'
                });
            });
        }
    });
};

window.removeChecklistAudio = function(checklistId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will remove the current audio recording!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('checklist_id', checklistId);
            formData.append('remove_audio', '1');
            
            fetch('edit_checklist_item.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('currentAudio').innerHTML = '';
                    Swal.fire({
                        title: 'Removed!',
                        text: 'Audio recording has been removed.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to remove audio recording.',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error removing audio:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while removing the audio recording.',
                    icon: 'error'
                });
            });
        }
    });
};

// ... existing code ...
</script>

<!-- Add Checklist Item Modal -->
<div class="modal fade" id="addChecklistItemModal" tabindex="-1" aria-labelledby="addChecklistItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addChecklistItemModalLabel">Add Checklist Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addChecklistItemForm">
                    <input type="hidden" id="checklistTaskId" name="task_id">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="checklistContent" class="form-label">Content</label>
                            <textarea class="form-control" id="checklistContent" name="content" rows="3" required></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="checklistParent" class="form-label">Parent Checklist (Optional)</label>
                            <select class="form-select" id="checklistParent" name="parent_id">
                                <option value="">No Parent (Main Item)</option>
                                <!-- Parent options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="checklistPriority" class="form-label">Priority</label>
                            <select class="form-select" id="checklistPriority" name="priority">
                                <option value="1">Low</option>
                                <option value="2" selected>Medium</option>
                                <option value="3">High</option>
                                <option value="4">Critical</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="checklistStartDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="checklistStartDate" name="start_date">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="checklistEndDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="checklistEndDate" name="end_date">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="checklistLabel" class="form-label">Label (Optional)</label>
                            <select class="form-select" id="checklistLabel" name="label">
                                <option value="">No Label</option>
                                <option value="feature">Feature</option>
                                <option value="debug">Debug</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="checklistFile" class="form-label">File Attachment</label>
                            <input type="file" class="form-control" id="checklistFile" name="file">
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="checklistAudio" class="form-label">Audio Attachment</label>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary" id="startRecording">
                                        <i class="fas fa-microphone"></i> Start Recording
                                    </button>
                                    <button type="button" class="btn btn-danger d-none" id="stopRecording">
                                        <i class="fas fa-stop"></i> Stop Recording
                                    </button>
                                </div>
                                <div id="recordingStatus" class="text-muted small d-none">
                                    Recording in progress...
                                </div>
                                <audio id="recordedAudio" controls class="d-none mt-2"></audio>
                                <input type="hidden" id="recordedAudioBlob" name="recorded_audio">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveChecklistItem">Add Item</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Task Note Modal -->
<div class="modal fade" id="addTaskNoteModal" tabindex="-1" aria-labelledby="addTaskNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskNoteModalLabel">Add Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTaskNoteForm">
                    <input type="hidden" id="noteTaskId" name="task_id">
                    <div class="mb-3">
                        <label for="noteContent" class="form-label">Note</label>
                        <textarea class="form-control" id="noteContent" name="note" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="noteFile" class="form-label">File Attachment (Optional)</label>
                        <input type="file" class="form-control" id="noteFile" name="file">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTaskNote">Add Note</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Task Note Modal -->
<div class="modal fade" id="editTaskNoteModal" tabindex="-1" aria-labelledby="editTaskNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskNoteModalLabel">Edit Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskNoteForm">
                    <input type="hidden" id="editNoteId" name="note_id">
                    <div class="mb-3">
                        <label for="editNoteContent" class="form-label">Note</label>
                        <textarea class="form-control" id="editNoteContent" name="note" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editNoteFile" class="form-label">File Attachment (Optional)</label>
                        <input type="file" class="form-control" id="editNoteFile" name="file">
                        <div id="currentFile" class="mt-2"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateTaskNote">Update Note</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Document Link Modal -->
<div class="modal fade" id="addDocumentLinkModal" tabindex="-1" aria-labelledby="addDocumentLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocumentLinkModalLabel">Add Document Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDocumentLinkForm">
                    <input type="hidden" id="documentTaskId" name="task_id">
                    <div class="mb-3">
                        <label for="documentTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="documentTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="documentUrl" class="form-label">URL</label>
                        <input type="url" class="form-control" id="documentUrl" name="url" required>
                    </div>
                    <div class="mb-3">
                        <label for="documentDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="documentDescription" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDocumentLink">Add Link</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Document Link Modal -->
<div class="modal fade" id="editDocumentLinkModal" tabindex="-1" aria-labelledby="editDocumentLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDocumentLinkModalLabel">Edit Document Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDocumentLinkForm">
                    <input type="hidden" id="editDocumentId" name="document_id">
                    <div class="mb-3">
                        <label for="editDocumentTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="editDocumentTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDocumentUrl" class="form-label">URL</label>
                        <input type="url" class="form-control" id="editDocumentUrl" name="url" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDocumentDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="editDocumentDescription" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateDocumentLink">Update Link</button>
            </div>
        </div>
    </div>
</div>

<!-- Checklist Item Details Modal -->
<div class="modal fade" id="checklistItemDetailsModal" tabindex="-1" aria-labelledby="checklistItemDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checklistItemDetailsModalLabel">Checklist Item Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <small class="text-muted d-block mb-1">Content</small>
                            <p class="mb-0" id="detailContent"></p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <small class="text-muted d-block mb-1">Status</small>
                            <p class="mb-0" id="detailStatus"></p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <small class="text-muted d-block mb-1">Start Date</small>
                            <p class="mb-0" id="detailStartDate"></p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <small class="text-muted d-block mb-1">End Date</small>
                            <p class="mb-0" id="detailEndDate"></p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <small class="text-muted d-block mb-1">Created By</small>
                            <p class="mb-0" id="detailCreatedBy"></p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <small class="text-muted d-block mb-1">Created Date</small>
                            <p class="mb-0" id="detailCreatedDate"></p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <small class="text-muted d-block mb-1">Completed By</small>
                            <p class="mb-0" id="detailCompletedBy"></p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-item">
                            <small class="text-muted d-block mb-1">Completion Date</small>
                            <p class="mb-0" id="detailCompletionDate"></p>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <h6>Attachments</h6>
                    <div id="detailAttachments">
                        <!-- Attachments will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- New Task Modal -->
<div class="modal fade" id="newTaskModal" tabindex="-1" aria-labelledby="newTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newTaskModalLabel">Create New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newTaskForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="taskTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="taskTitle" name="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="taskCategory" class="form-label">Category</label>
                            <select class="form-select" id="taskCategory" name="category" required>
                                <option value="IT">IT</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Product management">Product management</option>
                                <option value="Product Development">Product Development</option>
                                <option value="Strategy planning">Strategy planning</option>
                                <option value="Business planning">Business planning</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="taskAlloSection" class="form-label">Allo Section</label>
                            <select class="form-select" id="taskAlloSection" name="allo_section" required>
                                <option value="">Select Allo Section</option>
                                <option value="allolancer">allolancer</option>
                                <option value="allohub erp">allohub erp</option>
                                <option value="alloAi">alloAi</option>
                                <option value="private">private</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="taskPriority" class="form-label">Priority</label>
                            <select class="form-select" id="taskPriority" name="priority" required>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                                <option value="Hotfix">Hotfix</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="taskLabel" class="form-label">Label</label>
                            <select class="form-select" id="taskLabel" name="label">
                                <option value="">Select Label (Optional)</option>
                                <option value="feature">Feature</option>
                                <option value="debug">Debug</option>
                                <option value="QA">QA</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="taskDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="taskDescription" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveNewTask">Create Task</button>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer-dashboard.php' ?> 