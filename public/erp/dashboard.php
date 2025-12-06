<?php include 'views/headin2.php' ?>


<div class="dashboard-container">
    <?php include 'views/header-sidebar.php' ?>
    
    <!-- AI Search Bar -->
    <div class="ai-search-section mb-4">
        <div class="card">
            <div class="card-body">
                <div class="ai-search-container">
                    <div class="search-header">
                        <h5 class="mb-3">
                            <i class="fas fa-robot text-primary me-2"></i>
                            AI-Powered Search
                        </h5>
                        <p class="text-muted mb-0">Ask me anything about your tasks, meetings, or projects</p>
                    </div>
                    <div class="search-input-group">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">
                                <i class="fas fa-search"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control form-control-lg" 
                                id="aiSearchInput" 
                                placeholder="e.g., Show me all tasks due this week, Find meetings about project X, What's my progress on..."
                                autocomplete="off"
                            >
                            <button class="btn btn-primary btn-lg" type="button" id="aiSearchBtn">
                                <i class="fas fa-magic me-2"></i>Search
                            </button>
                        </div>
                    </div>
                    <div class="search-suggestions mt-3">
                        <div class="suggestion-chips">
                            <span class="suggestion-chip" onclick="fillSearchInput('Show me all tasks due this week')">
                                <i class="fas fa-calendar-week me-1"></i>Tasks due this week
                            </span>
                            <span class="suggestion-chip" onclick="fillSearchInput('Find meetings about project X')">
                                <i class="fas fa-users me-1"></i>Project meetings
                            </span>
                            <span class="suggestion-chip" onclick="fillSearchInput('What\'s my progress on completed tasks')">
                                <i class="fas fa-chart-line me-1"></i>Progress report
                            </span>
                            <span class="suggestion-chip" onclick="fillSearchInput('Show pending checklists')">
                                <i class="fas fa-tasks me-1"></i>Pending items
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ERP Board 2 - Agile Workflow Section -->
    <div class="erp-board-section mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-kanban me-2"></i>ERP Board 2 - Agile Workflow
                </h5>
                <div class="board-controls">
                    <button class="btn btn-sm btn-outline-primary" onclick="addSprintNote()">
                        <i class="fas fa-plus me-1"></i>Add Note
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="refreshBoard()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="agile-workflow-board">
                    <!-- Saturday - Sprint Planning & Retrospective -->
                    <div class="workflow-column">
                        <div class="column-header saturday">
                            <h6><i class="fas fa-calendar-day me-2"></i>Saturday</h6>
                            <span class="phase-badge">Sprint Planning & Retrospective</span>
                        </div>
                        <div class="column-content" id="saturday-column">
                            <div class="workflow-item planning-item">
                                <div class="item-header">
                                    <i class="fas fa-users text-primary"></i>
                                    <span class="item-title">Sprint Planning Meeting</span>
                                </div>
                                <div class="item-content">
                                    <p class="item-description">Plan sprint goals, tasks, and timeline</p>
                                    <div class="item-meta">
                                        <span class="badge bg-primary">Planning</span>
                                        <small class="text-muted">9:00 AM - 11:00 AM</small>
                                    </div>
                                </div>
                            </div>
                            <div class="workflow-item retrospective-item">
                                <div class="item-header">
                                    <i class="fas fa-chart-line text-warning"></i>
                                    <span class="item-title">Sprint Retrospective</span>
                                </div>
                                <div class="item-content">
                                    <p class="item-description">Review sprint performance and improvements</p>
                                    <div class="item-meta">
                                        <span class="badge bg-warning">Retrospective</span>
                                        <small class="text-muted">2:00 PM - 3:00 PM</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column-footer">
                            <button class="btn btn-sm btn-outline-primary w-100" onclick="addNote('saturday')">
                                <i class="fas fa-plus me-1"></i>Add Note
                            </button>
                        </div>
                    </div>

                    <!-- Sunday-Friday - Work -->
                    <div class="workflow-column">
                        <div class="column-header work-days">
                            <h6><i class="fas fa-tools me-2"></i>Sunday-Friday</h6>
                            <span class="phase-badge">Work</span>
                        </div>
                        <div class="column-content" id="work-column">
                            <div class="workflow-item work-item">
                                <div class="item-header">
                                    <i class="fas fa-code text-success"></i>
                                    <span class="item-title">Development Work</span>
                                </div>
                                <div class="item-content">
                                    <p class="item-description">Active development and task completion</p>
                                    <div class="item-meta">
                                        <span class="badge bg-success">In Progress</span>
                                        <small class="text-muted">Daily Tasks</small>
                                    </div>
                                </div>
                            </div>
                            <div class="workflow-item work-item">
                                <div class="item-header">
                                    <i class="fas fa-bug text-danger"></i>
                                    <span class="item-title">Bug Fixes</span>
                                </div>
                                <div class="item-content">
                                    <p class="item-description">Address issues and bugs</p>
                                    <div class="item-meta">
                                        <span class="badge bg-danger">Critical</span>
                                        <small class="text-muted">As Needed</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column-footer">
                            <button class="btn btn-sm btn-outline-success w-100" onclick="addNote('work')">
                                <i class="fas fa-plus me-1"></i>Add Note
                            </button>
                        </div>
                    </div>

                    <!-- Friday - Sprint Review -->
                    <div class="workflow-column">
                        <div class="column-header friday">
                            <h6><i class="fas fa-calendar-check me-2"></i>Friday</h6>
                            <span class="phase-badge">Sprint Review</span>
                        </div>
                        <div class="column-content" id="friday-column">
                            <div class="workflow-item review-item">
                                <div class="item-header">
                                    <i class="fas fa-presentation text-info"></i>
                                    <span class="item-title">Sprint Review Meeting</span>
                                </div>
                                <div class="item-content">
                                    <p class="item-description">Demonstrate completed work to stakeholders</p>
                                    <div class="item-meta">
                                        <span class="badge bg-info">Review</span>
                                        <small class="text-muted">3:00 PM - 4:00 PM</small>
                                    </div>
                                </div>
                            </div>
                            <div class="workflow-item review-item">
                                <div class="item-header">
                                    <i class="fas fa-clipboard-check text-success"></i>
                                    <span class="item-title">Sprint Completion</span>
                                </div>
                                <div class="item-content">
                                    <p class="item-description">Finalize sprint deliverables</p>
                                    <div class="item-meta">
                                        <span class="badge bg-success">Complete</span>
                                        <small class="text-muted">End of Sprint</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column-footer">
                            <button class="btn btn-sm btn-outline-info w-100" onclick="addNote('friday')">
                                <i class="fas fa-plus me-1"></i>Add Note
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Meetings Section -->
    <div class="todays-meetings-section mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-day me-2"></i>Today's Meetings
                </h5>
                <span class="badge bg-primary" id="meetingsCount">0</span>
            </div>
            <div class="card-body p-0">
                <div id="todaysMeetingsList" class="todays-meetings-list">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin"></i> Loading today's meetings...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Stats Cards -->
    <div class="dashboard-stats-container">
        <div class="row g-4">
            <!-- Total Tasks -->
            <div class="col-lg-4 col-md-6">
                <div class="stats-card stats-card-primary">
                    <div class="stats-card-body">
                        <div class="stats-card-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stats-card-content">
                            <div class="stats-card-number" data-target="0" id="totalTasks">0</div>
                            <div class="stats-card-label">Total Tasks</div>
                        </div>
                    </div>
                    <div class="stats-card-footer">
                        <span class="stats-card-footer-text">All assigned tasks</span>
                    </div>
                </div>
            </div>

            <!-- Pending Checklists -->
            <div class="col-lg-4 col-md-6">
                <div class="stats-card stats-card-warning">
                    <div class="stats-card-body">
                        <div class="stats-card-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stats-card-content">
                            <div class="stats-card-number" data-target="0" id="pendingChecklists">0</div>
                            <div class="stats-card-label">Pending Checklists</div>
                        </div>
                    </div>
                    <div class="stats-card-footer">
                        <span class="stats-card-footer-text">Status: 0</span>
                    </div>
                </div>
            </div>

            <!-- Completed Checklists -->
            <div class="col-lg-4 col-md-6">
                <div class="stats-card stats-card-success">
                    <div class="stats-card-body">
                        <div class="stats-card-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stats-card-content">
                            <div class="stats-card-number" data-target="0" id="completedChecklists">0</div>
                            <div class="stats-card-label">Completed Checklists</div>
                        </div>
                    </div>
                    <div class="stats-card-footer">
                        <span class="stats-card-footer-text">Status: 1</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Broadcast Section -->
    <div class="broadcast-section mt-4">
        <div class="row">
            <!-- Latest Meetings -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Pending Meetings
                        </h5>
                        <a href="broadcast_history" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div id="meetingsList" class="broadcast-list">
                            <div class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Voting Polls -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-poll me-2"></i>Pending Voting Polls
                        </h5>
                        <a href="broadcast_history" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div id="votingList" class="broadcast-list">
                            <div class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </div>
                        </div>
                    </div>
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

<style>
/* Dashboard Stats Cards Styles */
.dashboard-stats-container {
    margin-bottom: 2rem;
}

.stats-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
    border: none;
    height: 100%;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stats-card-body {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}

.stats-card-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    transition: transform 0.3s ease;
}

.stats-card:hover .stats-card-icon {
    transform: scale(1.1);
}

.stats-card-content {
    text-align: right;
    flex: 1;
}

.stats-card-number {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.25rem;
    color: #2c3e50;
    transition: all 0.3s ease;
}

.stats-card-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stats-card-footer {
    background: rgba(0, 0, 0, 0.05);
    padding: 0.75rem 1.5rem;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.stats-card-footer-text {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 500;
}

/* Card Color Variants */
.stats-card-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stats-card-primary .stats-card-icon {
    background: rgba(255, 255, 255, 0.2);
}

.stats-card-primary .stats-card-number,
.stats-card-primary .stats-card-label {
    color: white;
}

.stats-card-primary .stats-card-footer {
    background: rgba(255, 255, 255, 0.1);
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.stats-card-primary .stats-card-footer-text {
    color: rgba(255, 255, 255, 0.8);
}

.stats-card-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.stats-card-success .stats-card-icon {
    background: rgba(255, 255, 255, 0.2);
}

.stats-card-success .stats-card-number,
.stats-card-success .stats-card-label {
    color: white;
}

.stats-card-success .stats-card-footer {
    background: rgba(255, 255, 255, 0.1);
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.stats-card-success .stats-card-footer-text {
    color: rgba(255, 255, 255, 0.8);
}

.stats-card-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stats-card-warning .stats-card-icon {
    background: rgba(255, 255, 255, 0.2);
}

.stats-card-warning .stats-card-number,
.stats-card-warning .stats-card-label {
    color: white;
}

.stats-card-warning .stats-card-footer {
    background: rgba(255, 255, 255, 0.1);
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.stats-card-warning .stats-card-footer-text {
    color: rgba(255, 255, 255, 0.8);
}

.stats-card-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.stats-card-info .stats-card-icon {
    background: rgba(255, 255, 255, 0.2);
}

.stats-card-info .stats-card-number,
.stats-card-info .stats-card-label {
    color: white;
}

.stats-card-info .stats-card-footer {
    background: rgba(255, 255, 255, 0.1);
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.stats-card-info .stats-card-footer-text {
    color: rgba(255, 255, 255, 0.8);
}

/* Number Animation */
@keyframes countUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stats-card-number.animating {
    animation: countUp 0.6s ease-out;
}

/* Today's Meetings Section Styles */
.todays-meetings-section {
    margin-bottom: 2rem;
}

.todays-meetings-list {
    max-height: none;
    overflow: visible;
}

.todays-meeting-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 12px;
    margin: 8px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.todays-meeting-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #28a745);
    opacity: 0.8;
}

.todays-meeting-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.todays-meeting-single-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.meeting-info {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0; /* Allow text truncation */
}

.meeting-title {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    white-space: nowrap;
}

.meeting-description {
    color: #666;
    font-size: 0.9rem;
    font-weight: normal;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 300px;
}

.meeting-time {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    padding: 4px 10px;
    border-radius: 15px;
    font-weight: 600;
    font-size: 0.8rem;
    box-shadow: 0 1px 4px rgba(0,123,255,0.3);
    white-space: nowrap;
    flex-shrink: 0;
}

.todays-meeting-login-btn {
    padding: 6px 16px;
    font-size: 0.85rem;
    font-weight: 600;
    border-radius: 20px;
    border: none;
    transition: all 0.3s ease;
    box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    min-width: 120px;
}

.todays-meeting-login-btn:enabled {
    cursor: pointer;
}

.todays-meeting-login-btn:enabled:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

.todays-meeting-login-btn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.todays-meeting-broadcast-type {
    font-size: 0.75rem;
    padding: 4px 10px;
    border-radius: 12px;
    background: #e9ecef;
    color: #495057;
    border: 1px solid #dee2e6;
}

.no-todays-meetings {
    text-align: center;
    color: #666;
    padding: 40px 20px;
}

.no-todays-meetings i {
    font-size: 3em;
    margin-bottom: 15px;
    opacity: 0.5;
    color: #007bff;
}

.no-todays-meetings h6 {
    color: #333;
    margin-bottom: 8px;
}

.no-todays-meetings p {
    color: #666;
    margin: 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .todays-meeting-item {
        margin: 6px;
        padding: 10px;
    }
    
    .todays-meeting-single-line {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }
    
    .meeting-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .meeting-description {
        max-width: none;
        white-space: normal;
        line-height: 1.3;
    }
    
    .meeting-time {
        align-self: flex-start;
        font-size: 0.75rem;
        padding: 3px 8px;
    }
    
    .todays-meeting-login-btn {
        width: 100%;
        padding: 8px 16px;
    }
    
    .meeting-title {
        font-size: 0.95rem;
    }
}

.broadcast-list {
    max-height: 400px;
    overflow-y: auto;
}

.broadcast-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background: #fff;
    transition: all 0.3s ease;
    cursor: pointer;
}

.broadcast-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.broadcast-item.meeting {
    border-left: 4px solid #007bff;
}

.broadcast-item.voting {
    border-left: 4px solid #28a745;
}

.broadcast-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
}

.broadcast-title {
    font-weight: 600;
    color: #333;
    margin: 0;
    flex: 1;
}

.broadcast-meta {
    font-size: 0.85em;
    color: #666;
    margin-bottom: 8px;
}

.broadcast-description {
    color: #555;
    font-size: 0.9em;
    margin-bottom: 10px;
    line-height: 1.4;
}

.broadcast-stats {
    display: flex;
    gap: 15px;
    font-size: 0.85em;
    color: #666;
}

.broadcast-stat {
    display: flex;
    align-items: center;
    gap: 5px;
}

.broadcast-actions {
    display: flex;
    gap: 8px;
    margin-top: 10px;
}

.btn-respond {
    padding: 4px 12px;
    font-size: 0.8em;
    border-radius: 15px;
}

.btn-accept {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

.btn-decline {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.btn-vote {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.broadcast-type-badge {
    font-size: 0.7em;
    padding: 2px 6px;
    border-radius: 10px;
    background: #f8f9fa;
    color: #666;
}

.broadcast-options {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    align-items: center;
}

.broadcast-options .badge {
    font-size: 0.75em;
    padding: 4px 8px;
    border-radius: 12px;
    background: #e9ecef;
    color: #495057;
    border: 1px solid #dee2e6;
}

.no-broadcasts {
    text-align: center;
    color: #666;
    padding: 20px;
}

.no-broadcasts i {
    font-size: 2em;
    margin-bottom: 10px;
    opacity: 0.5;
}

/* New Voting Modal Styles */
.voting-modal-container {
    z-index: 9999;
}

.voting-modal-popup {
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.voting-modal-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}

.voting-modal-content {
    text-align: left;
    padding: 0;
}

.voting-description {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 4px solid #007bff;
}

.voting-stats {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 20px;
    color: white;
    margin: 0 -10px 20px -10px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.85rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.voting-options {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 5px;
}

.voting-option-card {
    background: #fff;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 15px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.voting-option-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.15);
    transform: translateY(-2px);
}

.voting-option-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #28a745);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.voting-option-card:hover::before {
    opacity: 1;
}

.voting-option-header h6 {
    color: #333;
    font-weight: 600;
    margin: 0;
}

.vote-count-badge {
    background: #007bff;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.progress {
    background-color: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    background: linear-gradient(90deg, #007bff, #28a745);
    border-radius: 10px;
    transition: width 0.6s ease;
}

.voting-option-footer {
    margin-top: 10px;
}

.vote-btn {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    border-radius: 20px;
    padding: 6px 16px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,123,255,0.3);
}

.vote-btn:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.4);
}

.vote-btn:disabled {
    background: #6c757d;
    transform: none;
    box-shadow: none;
    cursor: not-allowed;
}

/* Scrollbar styling for voting options */
.voting-options::-webkit-scrollbar {
    width: 6px;
}

.voting-options::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.voting-options::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.voting-options::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* ERP Board 2 - Agile Workflow Styles */
.erp-board-section {
    margin-bottom: 2rem;
}

.agile-workflow-board {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    overflow-x: auto;
    min-height: 400px;
}

.workflow-column {
    flex: 1;
    min-width: 280px;
    max-width: 320px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
}

.workflow-column:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.column-header {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    border-radius: 12px 12px 0 0;
    text-align: center;
}

.column-header h6 {
    margin: 0;
    font-weight: 600;
    color: #333;
}

.phase-badge {
    display: inline-block;
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 12px;
    margin-top: 0.5rem;
    font-weight: 500;
}

.column-header.saturday {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.column-header.saturday .phase-badge {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.column-header.work-days {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.column-header.work-days .phase-badge {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.column-header.friday {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.column-header.friday .phase-badge {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.column-content {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
    max-height: 300px;
}

.workflow-item {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.workflow-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #28a745);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.workflow-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.workflow-item:hover::before {
    opacity: 1;
}

.item-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.item-title {
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
}

.item-content {
    margin-top: 0.5rem;
}

.item-description {
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.item-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.column-footer {
    padding: 1rem;
    border-top: 1px solid #e9ecef;
    border-radius: 0 0 12px 12px;
}

/* Item type specific styles */
.planning-item {
    border-left: 4px solid #007bff;
}

.retrospective-item {
    border-left: 4px solid #ffc107;
}

.work-item {
    border-left: 4px solid #28a745;
}

.review-item {
    border-left: 4px solid #17a2b8;
}

/* Editable note styles */
.editable-note {
    background: #fff3cd;
    border: 1px dashed #ffc107;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    position: relative;
}

.editable-note textarea {
    width: 100%;
    min-height: 80px;
    border: none;
    background: transparent;
    resize: vertical;
    font-size: 0.85rem;
    line-height: 1.4;
    color: #333;
}

.editable-note textarea:focus {
    outline: none;
    background: white;
    border-radius: 4px;
    padding: 0.5rem;
}

.note-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
    justify-content: flex-end;
}

.note-actions .btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* Board controls */
.board-controls {
    display: flex;
    gap: 0.5rem;
}

/* Scrollbar styling for column content */
.column-content::-webkit-scrollbar {
    width: 4px;
}

.column-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.column-content::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.column-content::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .agile-workflow-board {
        flex-direction: column;
        gap: 1rem;
    }
    
    .workflow-column {
        min-width: auto;
        max-width: none;
    }
    
    .board-controls {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .board-controls .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .voting-modal-popup {
        margin: 10px;
        width: calc(100% - 20px) !important;
    }
    
    .voting-stats {
        padding: 15px;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .voting-option-card {
        padding: 12px;
    }
}

/* AI Search Section Styles */
.ai-search-section {
    margin-bottom: 2rem;
}

.ai-search-container {
    text-align: center;
}

.search-header h5 {
    color: #2c3e50;
    font-weight: 600;
}

.search-header p {
    font-size: 0.95rem;
    color: #6c757d;
}

.search-input-group {
    max-width: 800px;
    margin: 0 auto;
}

.search-input-group .form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px 0 0 12px;
    font-size: 1rem;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.search-input-group .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: none;
}

.search-input-group .input-group-text {
    border: 2px solid #007bff;
    border-right: none;
    border-radius: 12px 0 0 12px;
    padding: 0.75rem 1rem;
}

.search-input-group .btn {
    border-radius: 0 12px 12px 0;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid #007bff;
}

.search-input-group .btn:hover {
    background: #0056b3;
    border-color: #0056b3;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.suggestion-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin-top: 1rem;
}

.suggestion-chip {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 25px;
    font-size: 0.85rem;
    color: #495057;
    cursor: pointer;
    transition: all 0.3s ease;
    user-select: none;
}

.suggestion-chip:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    border-color: #adb5bd;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.suggestion-chip i {
    font-size: 0.8rem;
    opacity: 0.7;
}

/* Responsive adjustments for AI search */
@media (max-width: 768px) {
    .search-input-group {
        max-width: 100%;
    }
    
    .search-input-group .input-group {
        flex-direction: column;
    }
    
    .search-input-group .form-control {
        border-radius: 12px;
        margin-bottom: 10px;
    }
    
    .search-input-group .input-group-text {
        border-radius: 12px;
        border: 2px solid #007bff;
        margin-bottom: 10px;
    }
    
    .search-input-group .btn {
        border-radius: 12px;
        width: 100%;
    }
    
    .suggestion-chips {
        gap: 8px;
    }
    
    .suggestion-chip {
        padding: 6px 12px;
        font-size: 0.8rem;
    }
}

/* AI Response Modal Styles */
.ai-response-modal {
    z-index: 9999;
}

.ai-response-popup {
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.ai-response-header {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}

.ai-response-content {
    text-align: left;
    padding: 0;
}

.query-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 4px solid #6c757d;
}

.query-text {
    font-style: italic;
    color: #495057;
    font-size: 1rem;
}

.response-section {
    background: #e3f2fd;
    border-radius: 8px;
    padding: 15px;
    border-left: 4px solid #2196f3;
}

.response-text {
    color: #1565c0;
    font-size: 1rem;
    line-height: 1.5;
}

.actions-section {
    background: #f3e5f5;
    border-radius: 8px;
    padding: 15px;
    border-left: 4px solid #9c27b0;
}

.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.action-buttons .btn {
    border-radius: 20px;
    font-size: 0.85rem;
    padding: 6px 16px;
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Progress Report Styles */
.progress-report {
    text-align: center;
}

.progress-stat {
    margin-bottom: 1rem;
}

.progress-number {
    font-size: 2rem;
    font-weight: 700;
    color: #007bff;
    margin-bottom: 0.5rem;
}

.progress-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.progress-chart {
    max-width: 400px;
    margin: 0 auto;
}

.progress-chart .progress {
    height: 12px;
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-chart .progress-bar {
    background: linear-gradient(90deg, #007bff, #28a745);
    border-radius: 10px;
    transition: width 0.6s ease;
}

/* Focus effects for search input */
.search-input-group.focused .form-control {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.search-input-group.focused .input-group-text {
    border-color: #007bff;
    background: #0056b3;
}

/* Context Section Styles */
.context-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 4px solid #17a2b8;
}

.context-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 10px;
}

.context-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.context-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #007bff;
}

.context-item i {
    font-size: 1.2rem;
    width: 20px;
    text-align: center;
}

.context-item span {
    font-size: 0.9rem;
    font-weight: 500;
    color: #495057;
}

/* Responsive adjustments for context grid */
@media (max-width: 768px) {
    .context-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .context-item {
        padding: 8px;
    }
    
    .context-item span {
        font-size: 0.85rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadTodaysMeetings();
    loadDashboardBroadcasts();
    loadDashboardStats();
    loadNotesFromStorage(); // Load saved notes from ERP Board 2
    
    // Initialize AI search functionality
    initializeAISearch();
    
    // Refresh every 30 seconds
    setInterval(() => {
        loadTodaysMeetings();
        loadDashboardBroadcasts();
        loadDashboardStats();
    }, 30000);
    
    // Clean up timers when page is unloaded
    window.addEventListener('beforeunload', function() {
        clearMeetingTimers();
    });
});

// AI Search Functions
function initializeAISearch() {
    const searchInput = document.getElementById('aiSearchInput');
    const searchBtn = document.getElementById('aiSearchBtn');
    
    // Add event listeners
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performAISearch();
        }
    });
    
    searchBtn.addEventListener('click', performAISearch);
    
    // Add input focus effects
    searchInput.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    searchInput.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
    });
}

function fillSearchInput(text) {
    const searchInput = document.getElementById('aiSearchInput');
    searchInput.value = text;
    searchInput.focus();
}

function performAISearch() {
    const searchInput = document.getElementById('aiSearchInput');
    const searchQuery = searchInput.value.trim();
    
    if (!searchQuery) {
        Swal.fire({
            title: 'Search Query Required',
            text: 'Please enter a search query to continue',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Show loading state
    const searchBtn = document.getElementById('aiSearchBtn');
    const originalText = searchBtn.innerHTML;
    searchBtn.disabled = true;
    searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';
    
    // Call the real AI API
    const apiUrl = `http://ham-iran.ir/files/p/message-apps.php?message=${encodeURIComponent(searchQuery)}`;
    
    // Add timeout to prevent hanging
    const timeoutPromise = new Promise((_, reject) => {
        setTimeout(() => reject(new Error('API timeout')), 10000); // 10 second timeout
    });
    
    Promise.race([
        fetch(apiUrl),
        timeoutPromise
    ])
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                // Get real data from database and show AI response
                getDatabaseDataAndShowResponse(searchQuery, data.result);
            } else {
                throw new Error('API returned error status');
            }
        })
        .catch(error => {
            console.error('API Error:', error);
            // Fallback to local response if API fails
            const fallbackResponse = generateFallbackResponse(searchQuery);
            getDatabaseDataAndShowResponse(searchQuery, fallbackResponse);
        })
        .finally(() => {
            // Reset button
            searchBtn.disabled = false;
            searchBtn.innerHTML = originalText;
        });
}

function getDatabaseDataAndShowResponse(query, aiResponse) {
    // Get current dashboard data for context
    const dashboardData = {
        totalTasks: document.getElementById('totalTasks')?.textContent || '0',
        pendingChecklists: document.getElementById('pendingChecklists')?.textContent || '0',
        completedChecklists: document.getElementById('completedChecklists')?.textContent || '0',
        meetingsCount: document.getElementById('meetingsCount')?.textContent || '0'
    };
    
    // Show AI response with real data context
    showAIResponseWithData(query, aiResponse, dashboardData);
}

function showAIResponseWithData(query, aiResponse, dashboardData) {
    // Create contextual response based on AI response and dashboard data
    const contextualResponse = createContextualResponse(query, aiResponse, dashboardData);
    
    Swal.fire({
        title: `<div class="ai-response-header">
                    <i class="fas fa-robot text-primary me-2"></i>
                    AI Search Results
                </div>`,
        html: `
            <div class="ai-response-content">
                <div class="query-section mb-3">
                    <h6 class="text-muted mb-2">Your Query:</h6>
                    <div class="query-text">"${query}"</div>
                </div>
                <div class="response-section">
                    <h6 class="text-primary mb-2">AI Response:</h6>
                    <div class="response-text">${aiResponse}</div>
                </div>
                <div class="context-section mt-3">
                    <h6 class="text-info mb-2">Current Dashboard Status:</h6>
                    <div class="context-grid">
                        <div class="context-item">
                            <i class="fas fa-tasks text-primary"></i>
                            <span>Total Tasks: ${dashboardData.totalTasks}</span>
                        </div>
                        <div class="context-item">
                            <i class="fas fa-clock text-warning"></i>
                            <span>Pending: ${dashboardData.pendingChecklists}</span>
                        </div>
                        <div class="context-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Completed: ${dashboardData.completedChecklists}</span>
                        </div>
                        <div class="context-item">
                            <i class="fas fa-calendar-day text-info"></i>
                            <span>Today's Meetings: ${dashboardData.meetingsCount}</span>
                        </div>
                    </div>
                </div>
                ${contextualResponse.actions ? `
                    <div class="actions-section mt-3">
                        <h6 class="text-success mb-2">Suggested Actions:</h6>
                        <div class="action-buttons">
                            ${contextualResponse.actions.map(action => `
                                <button class="btn btn-outline-primary btn-sm me-2 mb-2" onclick="${action.onclick}">
                                    <i class="${action.icon} me-1"></i>${action.label}
                                </button>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        `,
        width: '700px',
        showConfirmButton: true,
        confirmButtonText: 'Close',
        confirmButtonColor: '#007bff',
        customClass: {
            container: 'ai-response-modal',
            popup: 'ai-response-popup'
        }
    });
}

function createContextualResponse(query, aiResponse, dashboardData) {
    const lowerQuery = query.toLowerCase();
    const lowerResponse = aiResponse.toLowerCase();
    
    // Analyze query and AI response to suggest relevant actions
    const actions = [];
    
    // Task-related actions
    if (lowerQuery.includes('task') || lowerResponse.includes('task')) {
        actions.push({
            label: 'View All Tasks',
            icon: 'fas fa-tasks',
            onclick: 'window.location.href=\'tasks\''
        });
        
        if (parseInt(dashboardData.pendingChecklists) > 0) {
            actions.push({
                label: 'View Pending Items',
                icon: 'fas fa-clock',
                onclick: 'scrollToDashboard()'
            });
        }
    }
    
    // Meeting-related actions
    if (lowerQuery.includes('meeting') || lowerResponse.includes('meeting')) {
        if (parseInt(dashboardData.meetingsCount) > 0) {
            actions.push({
                label: 'Today\'s Meetings',
                icon: 'fas fa-calendar-day',
                onclick: 'scrollToMeetings()'
            });
        }
        
        actions.push({
            label: 'Broadcast History',
            icon: 'fas fa-history',
            onclick: 'window.location.href=\'broadcast_history\''
        });
    }
    
    // Progress-related actions
    if (lowerQuery.includes('progress') || lowerResponse.includes('progress')) {
        actions.push({
            label: 'Progress Report',
            icon: 'fas fa-chart-line',
            onclick: 'showProgressReport()'
        });
        
        actions.push({
            label: 'Dashboard Stats',
            icon: 'fas fa-tachometer-alt',
            onclick: 'scrollToDashboard()'
        });
    }
    
    // Default actions if no specific ones found
    if (actions.length === 0) {
        actions.push(
            {
                label: 'View Dashboard',
                icon: 'fas fa-tachometer-alt',
                onclick: 'scrollToDashboard()'
            },
            {
                label: 'Go to Tasks',
                icon: 'fas fa-tasks',
                onclick: 'window.location.href=\'tasks\''
            }
        );
    }
    
    return { actions };
}

function generateFallbackResponse(query) {
    // Fallback response when API fails
    const lowerQuery = query.toLowerCase();
    
    if (lowerQuery.includes('task')) {
        return "I can help you with task management. Based on your dashboard, you have tasks to work on. Would you like me to show you your current task list?";
    } else if (lowerQuery.includes('meeting')) {
        return "I can help you find meeting information. You have access to today's meetings and meeting history. Would you like me to show you the current meeting schedule?";
    } else if (lowerQuery.includes('progress')) {
        return "I can show you your current progress. Based on your dashboard data, you're making progress on your tasks. Would you like a detailed progress report?";
    } else {
        return "I understand you're asking about your work. I can help you with tasks, meetings, progress tracking, and more. Try asking me about specific items like 'tasks due this week' or 'meeting schedule'.";
    }
}

// Old AI response functions removed - now using real API integration

// Action functions for AI response buttons
function filterTasksByDueDate() {
    Swal.fire({
        title: 'Redirecting...',
        text: 'Taking you to tasks page with due date filter',
        icon: 'info',
        timer: 2000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = 'tasks?filter=due_soon';
    });
}

function showCalendarView() {
    Swal.fire({
        title: 'Calendar View',
        text: 'Calendar functionality is available in the tasks page',
        icon: 'info',
        confirmButtonText: 'Go to Tasks',
        showCancelButton: true,
        cancelButtonText: 'Stay Here'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'tasks';
        }
    });
}

function scrollToMeetings() {
    const meetingsSection = document.querySelector('.todays-meetings-section');
    if (meetingsSection) {
        meetingsSection.scrollIntoView({ behavior: 'smooth' });
    }
}

function scrollToDashboard() {
    const dashboardStats = document.querySelector('.dashboard-stats-container');
    if (dashboardStats) {
        dashboardStats.scrollIntoView({ behavior: 'smooth' });
    }
}

function showProgressReport() {
    Swal.fire({
        title: 'Progress Report',
        html: `
            <div class="progress-report">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="progress-stat">
                            <div class="progress-number" id="progressTotalTasks">0</div>
                            <div class="progress-label">Total Tasks</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="progress-stat">
                            <div class="progress-number" id="progressPending">0</div>
                            <div class="progress-label">Pending</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="progress-stat">
                            <div class="progress-number" id="progressCompleted">0</div>
                            <div class="progress-label">Completed</div>
                        </div>
                    </div>
                </div>
                <div class="progress-chart mt-3">
                    <div class="progress">
                        <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="text-muted mt-2 d-block">Overall Progress: <span id="progressPercentage">0%</span></small>
                </div>
            </div>
        `,
        width: '600px',
        showConfirmButton: true,
        confirmButtonText: 'Close',
        didOpen: () => {
            // Load current stats for progress report
            const totalTasks = document.getElementById('totalTasks').textContent;
            const pendingChecklists = document.getElementById('pendingChecklists').textContent;
            const completedChecklists = document.getElementById('completedChecklists').textContent;
            
            document.getElementById('progressTotalTasks').textContent = totalTasks;
            document.getElementById('progressPending').textContent = pendingChecklists;
            document.getElementById('progressCompleted').textContent = completedChecklists;
            
            // Calculate progress percentage
            const total = parseInt(totalTasks) || 0;
            const completed = parseInt(completedChecklists) || 0;
            const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
            
            document.getElementById('progressBar').style.width = percentage + '%';
            document.getElementById('progressPercentage').textContent = percentage + '%';
        }
    });
}

function addNewChecklistItem() {
    Swal.fire({
        title: 'Add Checklist Item',
        text: 'This will take you to the tasks page where you can add new checklist items',
        icon: 'info',
        confirmButtonText: 'Go to Tasks',
        showCancelButton: true,
        cancelButtonText: 'Stay Here'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'tasks';
        }
    });
}

function loadTodaysMeetings() {
    console.log('Loading today\'s meetings...');
    
    // Clear existing timers before loading new data
    clearMeetingTimers();
    
    fetch('get_todays_meetings.php')
        .then(response => response.json())
        .then(data => {
            console.log('Today\'s meetings loaded:', data);
            if (data.success) {
                renderTodaysMeetings(data.meetings);
                updateMeetingsCount(data.meetings.length);
            } else {
                console.error('Failed to load today\'s meetings:', data.message);
                showTodaysMeetingsError(data.message);
            }
        })
        .catch(error => {
            console.error('Error loading today\'s meetings:', error);
            showTodaysMeetingsError('Network error. Please refresh the page.');
        });
}

function clearMeetingTimers() {
    // Clear all existing timers
    document.querySelectorAll('[data-meeting-id]').forEach(button => {
        if (button.dataset.timer) {
            clearInterval(parseInt(button.dataset.timer));
            delete button.dataset.timer;
        }
    });
}

function renderTodaysMeetings(meetings) {
    const container = document.getElementById('todaysMeetingsList');
    
    if (meetings.length === 0) {
        container.innerHTML = `
            <div class="no-todays-meetings">
                <i class="fas fa-calendar-check"></i>
                <h6>No Meetings Today</h6>
                <p>You have no meetings scheduled for today</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = meetings.map(meeting => `
        <div class="todays-meeting-item">
            <div class="todays-meeting-single-line">
                <div class="meeting-time">${meeting.time}</div>
                <div class="meeting-info">
                    <span class="meeting-title">${meeting.title}</span>
                    ${meeting.description ? `<span class="meeting-description">- ${meeting.description}</span>` : ''}
                </div>
                <button 
                    class="btn todays-meeting-login-btn btn-secondary" 
                    disabled
                    onclick="joinMeeting(${meeting.id}, '${meeting.title}', event)"
                    data-meeting-id="${meeting.id}"
                    data-meeting-title="${meeting.title}"
                >
                    <i class="fas fa-clock me-1"></i>Loading...
                </button>
            </div>
        </div>
    `).join('');
    
    // Set up timers for button activation
    setupMeetingTimers(meetings);
}

function formatTimeRemaining(seconds) {
    if (seconds <= 0) return '0:00';
    
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const remainingSeconds = seconds % 60;
    
    if (hours > 0) {
        return `${hours}:${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
    } else {
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }
}

function setupMeetingTimers(meetings) {
    meetings.forEach(meeting => {
        const button = document.querySelector(`[data-meeting-id="${meeting.id}"]`);
        if (!button) return;
        
        // All timestamps are now in Sweden timezone
        const now = Math.floor(Date.now() / 1000);
        const threeMinutesBefore = meeting.three_minutes_before;
        const meetingStart = meeting.meeting_datetime;
        const meetingEnd = meeting.meeting_end;
        
        // Debug: Log the timestamp values (now in Sweden timezone)
        console.log(`Meeting ${meeting.id} timestamps (Sweden timezone):`, {
            now,
            threeMinutesBefore,
            meetingStart,
            meetingEnd,
            nowFormatted: new Date(now * 1000).toLocaleString('sv-SE', {timeZone: 'Europe/Stockholm'}),
            threeMinutesBeforeFormatted: new Date(threeMinutesBefore * 1000).toLocaleString('sv-SE', {timeZone: 'Europe/Stockholm'}),
            meetingStartFormatted: new Date(meetingStart * 1000).toLocaleString('sv-SE', {timeZone: 'Europe/Stockholm'}),
            meetingEndFormatted: new Date(meetingEnd * 1000).toLocaleString('sv-SE', {timeZone: 'Europe/Stockholm'})
        });
        
        // Validate timestamp ranges
        const oneDayInSeconds = 24 * 60 * 60;
        const oneYearInSeconds = 365 * oneDayInSeconds;
        
        if (threeMinutesBefore < now - oneDayInSeconds || threeMinutesBefore > now + oneYearInSeconds) {
            console.error(`Meeting ${meeting.id} has unreasonable three_minutes_before timestamp:`, threeMinutesBefore);
        }
        if (meetingStart < now - oneDayInSeconds || meetingStart > now + oneYearInSeconds) {
            console.error(`Meeting ${meeting.id} has unreasonable meeting_start timestamp:`, meetingStart);
        }
        if (meetingEnd < now - oneDayInSeconds || meetingEnd > now + oneYearInSeconds) {
            console.error(`Meeting ${meeting.id} has unreasonable meeting_end timestamp:`, meetingEnd);
        }
        
        // Set up countdown timer
        const updateButtonText = () => {
            const currentTime = Math.floor(Date.now() / 1000);
            
            // Validate timestamps
            if (!threeMinutesBefore || !meetingStart || !meetingEnd) {
                console.error(`Invalid timestamps for meeting ${meeting.id}:`, {
                    threeMinutesBefore,
                    meetingStart,
                    meetingEnd
                });
                button.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i>Time Error`;
                button.disabled = true;
                button.className = 'btn todays-meeting-login-btn btn-danger';
                return;
            }
            
            if (currentTime < threeMinutesBefore) {
                // Meeting is in the future - show countdown to activation
                const timeRemaining = threeMinutesBefore - currentTime;
                if (timeRemaining > 0) {
                    button.innerHTML = `<i class="fas fa-clock me-1"></i>Join in ${formatTimeRemaining(timeRemaining)}`;
                } else {
                    button.innerHTML = `<i class="fas fa-clock me-1"></i>Join in 0:00`;
                }
                button.disabled = true;
                button.className = 'btn todays-meeting-login-btn btn-secondary';
            } else if (currentTime >= threeMinutesBefore && currentTime < meetingStart) {
                // Meeting is starting soon - show countdown to start
                const timeRemaining = meetingStart - currentTime;
                if (timeRemaining > 0) {
                    button.innerHTML = `<i class="fas fa-clock me-1"></i>Join in ${formatTimeRemaining(timeRemaining)}`;
                } else {
                    button.innerHTML = `<i class="fas fa-clock me-1"></i>Join in 0:00`;
                }
                button.disabled = true;
                button.className = 'btn todays-meeting-login-btn btn-warning';
            } else if (currentTime >= meetingStart && currentTime < meetingEnd) {
                // Meeting is active
                button.innerHTML = `<i class="fas fa-sign-in-alt me-1"></i>Join Meeting`;
                button.disabled = false;
                button.className = 'btn todays-meeting-login-btn btn-success';
            } else {
                // Meeting has ended
                button.innerHTML = `<i class="fas fa-clock me-1"></i>Meeting Ended`;
                button.disabled = true;
                button.className = 'btn todays-meeting-login-btn btn-secondary';
            }
        };
        
        // Update immediately
        updateButtonText();
        
        // Update every second
        const timer = setInterval(() => {
            const currentTime = Math.floor(Date.now() / 1000);
            
            if (currentTime >= meetingEnd) {
                // Meeting has ended, stop the timer
                clearInterval(timer);
                updateButtonText();
            } else {
                updateButtonText();
            }
        }, 1000);
        
        // Store timer reference for cleanup
        button.dataset.timer = timer;
    });
}

function activateMeetingButton(meetingId, buttonClass, buttonText) {
    const button = document.querySelector(`[data-meeting-id="${meetingId}"]`);
    if (button) {
        button.disabled = false;
        button.className = `btn todays-meeting-login-btn ${buttonClass}`;
        button.innerHTML = `<i class="fas fa-sign-in-alt me-1"></i>${buttonText}`;
        
        // Show notification
        Swal.fire({
            title: 'Meeting Starting Soon!',
            text: 'You can now join the meeting',
            icon: 'info',
            timer: 3000,
            showConfirmButton: false
        });
    }
}

function deactivateMeetingButton(meetingId, buttonText) {
    const button = document.querySelector(`[data-meeting-id="${meetingId}"]`);
    if (button) {
        button.disabled = true;
        button.className = 'btn todays-meeting-login-btn btn-secondary';
        button.innerHTML = `<i class="fas fa-clock me-1"></i>${buttonText}`;
        
        // Clear the timer if it exists
        if (button.dataset.timer) {
            clearInterval(parseInt(button.dataset.timer));
            delete button.dataset.timer;
        }
    }
}

function joinMeeting(meetingId, meetingTitle, event) {
    event.preventDefault();
    
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Show confirmation dialog
    Swal.fire({
        title: 'Join Meeting',
        text: `Are you sure you want to join "${meetingTitle}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Join Meeting',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Joining...';
            
            // Generate random meeting ID
            const generateRandomId = () => {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                let result = '';
                for (let i = 0; i < 12; i++) {
                    result += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return result;
            };
            
            const randomId = generateRandomId();
            const meetingUrl = `https://allohub.website/meet?${randomId}`;
            
            // Simulate joining process
            setTimeout(() => {
                // Open meeting in new tab
                window.open(meetingUrl, '_blank');
                
                Swal.fire({
                    title: 'Meeting Joined!',
                    text: `You have successfully joined "${meetingTitle}"`,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Reset button
                    button.disabled = false;
                    button.innerHTML = originalText;
                    
                    console.log(`User joined meeting: ${meetingTitle} (ID: ${meetingId}) at URL: ${meetingUrl}`);
                });
            }, 1500);
        }
    });
}

function updateMeetingsCount(count) {
    const countElement = document.getElementById('meetingsCount');
    if (countElement) {
        countElement.textContent = count;
    }
}

function showTodaysMeetingsError(message) {
    const container = document.getElementById('todaysMeetingsList');
    container.innerHTML = `
        <div class="no-todays-meetings">
            <i class="fas fa-exclamation-triangle text-warning"></i>
            <h6>Error Loading Meetings</h6>
            <p>${message}</p>
        </div>
    `;
}

function loadDashboardStats() {
    console.log('Loading dashboard statistics...');
    
    fetch('get_dashboard_stats.php')
        .then(response => response.json())
        .then(data => {
            console.log('Dashboard stats loaded:', data);
            if (data.success) {
                updateDashboardStats(data.stats);
            } else {
                console.error('Failed to load dashboard stats:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading dashboard stats:', error);
        });
}

function updateDashboardStats(stats) {
    // Animate counter for Total Tasks
    animateCounter('totalTasks', stats.total_tasks);
    
    // Animate counter for Pending Checklists
    animateCounter('pendingChecklists', stats.pending_checklists);
    
    // Animate counter for Completed Checklists
    animateCounter('completedChecklists', stats.completed_checklists);
}

function animateCounter(elementId, targetValue) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const currentValue = parseInt(element.textContent) || 0;
    
    // Only animate if the value has changed
    if (currentValue === targetValue) return;
    
    // Add animation class
    element.classList.add('animating');
    
    // Remove animation class after animation completes
    setTimeout(() => {
        element.classList.remove('animating');
    }, 600);
    
    // Animate the number counting up/down
    const startValue = currentValue;
    const difference = targetValue - startValue;
    const duration = 1000; // 1 second
    const stepTime = 20; // Update every 20ms
    const steps = duration / stepTime;
    const stepValue = difference / steps;
    
    let current = startValue;
    let step = 0;
    
    const timer = setInterval(() => {
        step++;
        current += stepValue;
        
        if (step >= steps) {
            current = targetValue;
            clearInterval(timer);
        }
        
        element.textContent = Math.round(current);
        element.setAttribute('data-target', targetValue);
    }, stepTime);
}

function loadDashboardBroadcasts() {
    console.log('Loading dashboard broadcasts...');
    
    fetch('get_dashboard_broadcasts.php')
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Dashboard broadcasts loaded:', data);
            if (data.success) {
                renderMeetings(data.meetings);
                renderVotingPolls(data.voting_polls);
                
                // Debug: Check if voting polls exist
                if (data.voting_polls && data.voting_polls.length > 0) {
                    console.log('Voting polls found:', data.voting_polls.length);
                    data.voting_polls.forEach((poll, index) => {
                        console.log(`Poll ${index + 1}:`, poll);
                    });
                } else {
                    console.log('No voting polls found in the database');
                }
            } else {
                console.error('Failed to load broadcasts:', data.message);
                console.error('Debug info:', data.debug);
                
                // Show error message to user
                document.getElementById('meetingsList').innerHTML = `
                    <div class="no-broadcasts">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <p>Error loading data</p>
                        <small class="text-muted">${data.message}</small>
                    </div>
                `;
                document.getElementById('votingList').innerHTML = `
                    <div class="no-broadcasts">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <p>Error loading data</p>
                        <small class="text-muted">${data.message}</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading broadcasts:', error);
            
            // Show error message to user
            document.getElementById('meetingsList').innerHTML = `
                <div class="no-broadcasts">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <p>Network error</p>
                    <small class="text-muted">Failed to load data. Please refresh the page.</small>
                </div>
            `;
            document.getElementById('votingList').innerHTML = `
                <div class="no-broadcasts">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <p>Network error</p>
                    <small class="text-muted">Failed to load data. Please refresh the page.</small>
                </div>
            `;
        });
}

function renderMeetings(meetings) {
    const container = document.getElementById('meetingsList');
    
    if (meetings.length === 0) {
        container.innerHTML = `
            <div class="no-broadcasts">
                <i class="fas fa-calendar-check"></i>
                <p>No pending meetings</p>
                <small class="text-muted">You've responded to all available meetings</small>
            </div>
        `;
        return;
    }
    
    container.innerHTML = meetings.map(meeting => `
        <div class="broadcast-item meeting" onclick="showMeetingDetails(${meeting.id})">
            <div class="broadcast-header">
                <h6 class="broadcast-title">${meeting.title}</h6>
                <span class="broadcast-type-badge">${meeting.broadcast_type}</span>
            </div>
            <div class="broadcast-meta">
                <i class="fas fa-calendar me-1"></i>${meeting.date} at ${meeting.time}
                <br>
                <i class="fas fa-user me-1"></i>${meeting.creator}
            </div>
            ${meeting.description ? `<div class="broadcast-description">${meeting.description}</div>` : ''}
            <div class="broadcast-stats">
                <div class="broadcast-stat">
                    <i class="fas fa-users"></i>
                    <span>${meeting.response_count} responses</span>
                </div>
                <div class="broadcast-stat">
                    <i class="fas fa-check text-success"></i>
                    <span>${meeting.accept_count} accepted</span>
                </div>
                <div class="broadcast-stat">
                    <i class="fas fa-times text-danger"></i>
                    <span>${meeting.decline_count} declined</span>
                </div>
            </div>
            <div class="broadcast-actions">
                <button class="btn btn-sm btn-respond btn-accept" onclick="respondToMeeting(${meeting.id}, 'accept', event)">
                    <i class="fas fa-check"></i> Accept
                </button>
                <button class="btn btn-sm btn-respond btn-decline" onclick="respondToMeeting(${meeting.id}, 'decline', event)">
                    <i class="fas fa-times"></i> Decline
                </button>
            </div>
        </div>
    `).join('');
}

function renderVotingPolls(polls) {
    const container = document.getElementById('votingList');
    
    if (polls.length === 0) {
        container.innerHTML = `
            <div class="no-broadcasts">
                <i class="fas fa-poll-h"></i>
                <p>No pending voting polls</p>
                <small class="text-muted">You've voted on all available polls</small>
            </div>
        `;
        return;
    }
    
    container.innerHTML = polls.map(poll => {
        // Calculate total votes
        const totalVotes = poll.options.reduce((sum, option) => sum + (option.vote_count || 0), 0);
        
        // Create options preview (show top 2 options by votes)
        let optionsPreview = '';
        if (poll.options && poll.options.length > 0) {
            // Sort options by vote count (descending)
            const sortedOptions = [...poll.options].sort((a, b) => (b.vote_count || 0) - (a.vote_count || 0));
            const previewOptions = sortedOptions.slice(0, 2);
            
            optionsPreview = previewOptions.map(option => {
                const voteCount = option.vote_count || 0;
                const percentage = totalVotes > 0 ? Math.round((voteCount / totalVotes) * 100) : 0;
                const optionText = option.text || option.option_text;
                
                return `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-truncate me-2" style="max-width: 150px;">${optionText}</span>
                        <span class="badge bg-primary">${voteCount} (${percentage}%)</span>
                    </div>
                `;
            }).join('');
            
            if (poll.options.length > 2) {
                optionsPreview += `<small class="text-muted">+${poll.options.length - 2} more options</small>`;
            }
        }
        
        return `
            <div class="broadcast-item voting" onclick="showVotingOptions(${poll.id}, event)">
                <div class="broadcast-header">
                    <h6 class="broadcast-title">${poll.title}</h6>
                    <span class="broadcast-type-badge">${poll.broadcast_type}</span>
                </div>
                <div class="broadcast-meta">
                    <i class="fas fa-clock me-1"></i>${formatDate(poll.created_at)}
                </div>
                ${poll.description ? `<div class="broadcast-description">${poll.description}</div>` : ''}
                ${optionsPreview ? `
                    <div class="broadcast-options mb-2">
                        <small class="text-muted mb-2 d-block">Top options:</small>
                        ${optionsPreview}
                    </div>
                ` : ''}
                <div class="broadcast-stats">
                    <div class="broadcast-stat">
                        <i class="fas fa-list"></i>
                        <span>${poll.option_count} options</span>
                    </div>
                    <div class="broadcast-stat">
                        <i class="fas fa-vote-yea"></i>
                        <span>${totalVotes} total votes</span>
                    </div>
                </div>
                <div class="broadcast-actions">
                    <button class="btn btn-sm btn-respond btn-vote" onclick="showVotingOptions(${poll.id}, event)">
                        <i class="fas fa-vote-yea"></i> Vote Now
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="showVotingDetails(${poll.id})">
                        <i class="fas fa-chart-bar"></i> View Results
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

function showMeetingDetails(meetingId) {
    fetch(`get_meeting_details.php?id=${meetingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const meeting = data.meeting;
                const responses = data.responses;
                
                let responsesHtml = '';
                if (responses.length > 0) {
                    responsesHtml = `
                        <h6>Responses:</h6>
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
                                    ${responses.map(response => `
                                        <tr>
                                            <td>${response.user_name}</td>
                                            <td>
                                                <span class="badge ${response.response === 'accept' ? 'bg-success' : 'bg-danger'}">
                                                    ${response.response === 'accept' ? 'Accepted' : 'Declined'}
                                                </span>
                                            </td>
                                            <td>${formatDate(response.created_at)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                }
                
                document.getElementById('meetingDetailsContent').innerHTML = `
                    <div class="meeting-details">
                        <h5>${meeting.title}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Date:</strong> ${meeting.meeting_date}</p>
                                <p><strong>Time:</strong> ${meeting.meeting_time}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Creator:</strong> ${meeting.creator_name}</p>
                                <p><strong>Type:</strong> ${meeting.broadcast_type}</p>
                            </div>
                        </div>
                        ${meeting.description ? `<p><strong>Description:</strong><br>${meeting.description}</p>` : ''}
                        ${responsesHtml}
                    </div>
                `;
                
                new bootstrap.Modal(document.getElementById('meetingDetailsModal')).show();
            }
        });
}

function showVotingOptions(pollId, event) {
    event.stopPropagation();
    
    console.log('showVotingOptions called with pollId:', pollId);
    
    // Show loading state
    Swal.fire({
        title: 'Loading...',
        text: 'Getting voting options',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Get voting data
    fetch('get_dashboard_broadcasts.php')
        .then(response => {
            console.log('API Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('API Response data:', data);
            
            if (data.success) {
                console.log('Voting polls from API:', data.voting_polls);
                const poll = data.voting_polls.find(p => p.id == pollId);
                console.log('Found poll:', poll);
                
                if (poll && poll.options && poll.options.length > 0) {
                    console.log('Poll options:', poll.options);
                    showVotingModal(poll);
                } else {
                    console.log('Poll not found or no options, trying fallback API');
                    // Fallback to API call with JSON format
                    fetch(`get_voting_details.php?id=${pollId}&format=json`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Fallback API response:', data);
                            if (data.success) {
                                const poll = data.poll;
                                const options = data.options;
                                console.log('Fallback poll:', poll);
                                console.log('Fallback options:', options);
                                showVotingModal({...poll, options: options});
                            } else {
                                console.error('Fallback API failed:', data.message);
                                Swal.fire('Error', 'Failed to load voting options: ' + (data.message || 'Unknown error'), 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Fallback API error:', error);
                            Swal.fire('Error', 'Failed to load voting options: ' + error.message, 'error');
                        });
                }
            } else {
                console.error('API failed:', data.message);
                Swal.fire('Error', 'Failed to load voting data: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Network error:', error);
            Swal.fire('Error', 'Failed to load voting data: ' + error.message, 'error');
        });
}

function showVotingModal(poll) {
    // Calculate total votes
    const totalVotes = poll.options.reduce((sum, option) => sum + (option.vote_count || 0), 0);
    
    // Create voting options HTML with progress bars
    let optionsHtml = '';
    poll.options.forEach(option => {
        const voteCount = option.vote_count || 0;
        const percentage = totalVotes > 0 ? Math.round((voteCount / totalVotes) * 100) : 0;
        const optionText = option.text || option.option_text;
        
        optionsHtml += `
            <div class="voting-option-card mb-3" data-option-id="${option.id}" data-option-text="${optionText}">
                <div class="voting-option-header d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">${optionText}</h6>
                    <span class="vote-count-badge">${voteCount} votes</span>
                </div>
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar" role="progressbar" style="width: ${percentage}%" 
                         aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="voting-option-footer d-flex justify-content-between align-items-center">
                    <small class="text-muted">${percentage}%</small>
                    <button class="btn btn-primary btn-sm vote-btn" onclick="castVote(${poll.id}, ${option.id}, '${optionText}')">
                        <i class="fas fa-vote-yea me-1"></i>Vote
                    </button>
                </div>
            </div>
        `;
    });
    
    Swal.fire({
        title: `<div class="voting-modal-title">
                    <i class="fas fa-poll text-primary me-2"></i>
                    ${poll.title}
                </div>`,
        html: `
            <div class="voting-modal-content">
                ${poll.description ? `
                    <div class="voting-description mb-3">
                        <p class="text-muted mb-0">${poll.description}</p>
                    </div>
                ` : ''}
                <div class="voting-stats mb-3">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number">${poll.options.length}</div>
                                <div class="stat-label">Options</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number">${totalVotes}</div>
                                <div class="stat-label">Total Votes</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number">${poll.broadcast_type}</div>
                                <div class="stat-label">Type</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="voting-options">
                    ${optionsHtml}
                </div>
            </div>
        `,
        showConfirmButton: false,
        showCloseButton: true,
        width: '600px',
        customClass: {
            container: 'voting-modal-container',
            popup: 'voting-modal-popup',
            content: 'voting-modal-content'
        }
    });
}

function castVote(pollId, optionId, optionText) {
    // Disable all vote buttons
    document.querySelectorAll('.vote-btn').forEach(btn => {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Voting...';
    });
    
    // Call the API
    const formData = new FormData();
    formData.append('poll_id', pollId);
    formData.append('option_id', optionId);
    
    fetch('vote_for_option.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            Swal.fire({
                title: 'Vote Cast Successfully!',
                text: `You voted for: ${optionText}`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Close the voting modal
                Swal.close();
                // Refresh the dashboard data
                loadDashboardBroadcasts();
            });
        } else {
            // Show error message
            Swal.fire({
                title: 'Voting Failed',
                text: data.message || 'Failed to save your vote. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                // Re-enable vote buttons
                document.querySelectorAll('.vote-btn').forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-vote-yea me-1"></i>Vote';
                });
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Show error message
        Swal.fire({
            title: 'Voting Failed',
            text: 'Network error. Please check your connection and try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(() => {
            // Re-enable vote buttons
            document.querySelectorAll('.vote-btn').forEach(btn => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-vote-yea me-1"></i>Vote';
            });
        });
    });
}

function respondToMeeting(meetingId, response, event) {
    event.stopPropagation();
    
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Call the actual API endpoint
    const formData = new FormData();
    formData.append('meeting_id', meetingId);
    formData.append('response', response);
    
    fetch('respond_to_meeting.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = response === 'accept' ? 
                '<i class="fas fa-check"></i> Accepted' : 
                '<i class="fas fa-times"></i> Declined';
            button.classList.remove('btn-accept', 'btn-decline');
            button.classList.add(response === 'accept' ? 'btn-success' : 'btn-danger');
            
            // Show success message
            Swal.fire({
                title: 'Response Sent!',
                text: `You have ${response}d the meeting invitation.`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            
            // Refresh the data to show updated counts
            setTimeout(() => {
                loadDashboardBroadcasts();
            }, 2000);
        } else {
            // Show error message
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to save response',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            
            // Reset button
            button.disabled = false;
            button.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Show error message
        Swal.fire({
            title: 'Error!',
            text: 'Failed to save response. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        
        // Reset button
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

function showVotingDetails(pollId) {
    // Show loading state
    Swal.fire({
        title: 'Loading...',
        text: 'Getting voting details',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(`get_voting_details.php?id=${pollId}&format=html`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('votingDetailsContent').innerHTML = data.html;
                Swal.close();
                new bootstrap.Modal(document.getElementById('votingDetailsModal')).show();
            } else {
                Swal.fire('Error', data.message || 'Failed to load voting details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to load voting details', 'error');
        });
}

// ERP Board 2 - Agile Workflow Functions
function addSprintNote() {
    Swal.fire({
        title: 'Add Sprint Note',
        html: `
            <div class="mb-3">
                <label class="form-label">Select Column:</label>
                <select id="noteColumn" class="form-select">
                    <option value="saturday">Saturday - Sprint Planning & Retrospective</option>
                    <option value="work">Sunday-Friday - Work</option>
                    <option value="friday">Friday - Sprint Review</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Note Title:</label>
                <input type="text" id="noteTitle" class="form-control" placeholder="Enter note title">
            </div>
            <div class="mb-3">
                <label class="form-label">Note Content:</label>
                <textarea id="noteContent" class="form-control" rows="4" placeholder="Enter note content"></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add Note',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const column = document.getElementById('noteColumn').value;
            const title = document.getElementById('noteTitle').value;
            const content = document.getElementById('noteContent').value;
            
            if (!title.trim() || !content.trim()) {
                Swal.showValidationMessage('Please fill in all fields');
                return false;
            }
            
            return { column, title, content };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            addNoteToColumn(result.value.column, result.value.title, result.value.content);
        }
    });
}

function addNote(column) {
    Swal.fire({
        title: 'Add Note',
        html: `
            <div class="mb-3">
                <label class="form-label">Note Title:</label>
                <input type="text" id="noteTitle" class="form-control" placeholder="Enter note title">
            </div>
            <div class="mb-3">
                <label class="form-label">Note Content:</label>
                <textarea id="noteContent" class="form-control" rows="4" placeholder="Enter note content"></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add Note',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const title = document.getElementById('noteTitle').value;
            const content = document.getElementById('noteContent').value;
            
            if (!title.trim() || !content.trim()) {
                Swal.showValidationMessage('Please fill in all fields');
                return false;
            }
            
            return { title, content };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            addNoteToColumn(column, result.value.title, result.value.content);
        }
    });
}

function addNoteToColumn(column, title, content) {
    const columnElement = document.getElementById(`${column}-column`);
    if (!columnElement) return;
    
    // Save to database first
    fetch('save_board_note.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            column: column,
            title: title,
            content: content
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const noteId = data.noteId;
            const noteHtml = `
                <div class="editable-note" id="note_${noteId}">
                    <div class="item-header">
                        <i class="fas fa-sticky-note text-warning"></i>
                        <span class="item-title">${title}</span>
                        <div class="ms-auto">
                            <button class="btn btn-sm btn-outline-primary" onclick="editNote('note_${noteId}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteNote('note_${noteId}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="note-content">
                        <p class="item-description">${content}</p>
                        <small class="text-muted">Added: ${new Date().toLocaleString()}</small>
                    </div>
                </div>
            `;
            
            columnElement.insertAdjacentHTML('beforeend', noteHtml);
            
            // Show success message
            Swal.fire({
                title: 'Note Added!',
                text: 'Your note has been added to the board',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to save note',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to save note. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

function editNote(noteId) {
    const noteElement = document.getElementById(noteId);
    if (!noteElement) return;
    
    const titleElement = noteElement.querySelector('.item-title');
    const contentElement = noteElement.querySelector('.item-description');
    
    const currentTitle = titleElement.textContent;
    const currentContent = contentElement.textContent;
    const dbNoteId = noteId.replace('note_', '');
    
    Swal.fire({
        title: 'Edit Note',
        html: `
            <div class="mb-3">
                <label class="form-label">Note Title:</label>
                <input type="text" id="editNoteTitle" class="form-control" value="${currentTitle}">
            </div>
            <div class="mb-3">
                <label class="form-label">Note Content:</label>
                <textarea id="editNoteContent" class="form-control" rows="4">${currentContent}</textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Save Changes',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const title = document.getElementById('editNoteTitle').value;
            const content = document.getElementById('editNoteContent').value;
            
            if (!title.trim() || !content.trim()) {
                Swal.showValidationMessage('Please fill in all fields');
                return false;
            }
            
            return { title, content };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Update in database
            fetch('save_board_note.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    noteId: dbNoteId,
                    title: result.value.title,
                    content: result.value.content
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    titleElement.textContent = result.value.title;
                    contentElement.textContent = result.value.content;
                    
                    Swal.fire({
                        title: 'Note Updated!',
                        text: 'Your note has been updated',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to update note',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update note. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

function deleteNote(noteId) {
    Swal.fire({
        title: 'Delete Note',
        text: 'Are you sure you want to delete this note?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const dbNoteId = noteId.replace('note_', '');
            
            // Delete from database
            fetch('delete_board_note.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    noteId: dbNoteId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const noteElement = document.getElementById(noteId);
                    if (noteElement) {
                        noteElement.remove();
                    }
                    
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Your note has been deleted',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to delete note',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to delete note. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

function refreshBoard() {
    Swal.fire({
        title: 'Refreshing Board',
        text: 'Loading latest data...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Simulate refresh (in real implementation, this would load from database)
    setTimeout(() => {
        Swal.fire({
            title: 'Board Refreshed!',
            text: 'The board has been updated with latest data',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }, 1500);
}



function loadNotesFromStorage() {
    // Load notes from database
    fetch('get_board_notes.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Object.keys(data.notes).forEach(column => {
                    const columnElement = document.getElementById(`${column}-column`);
                    if (columnElement && data.notes[column]) {
                        data.notes[column].forEach(note => {
                            const noteHtml = `
                                <div class="editable-note" id="note_${note.id}">
                                    <div class="item-header">
                                        <i class="fas fa-sticky-note text-warning"></i>
                                        <span class="item-title">${note.title}</span>
                                        <div class="ms-auto">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editNote('note_${note.id}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteNote('note_${note.id}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="note-content">
                                        <p class="item-description">${note.content}</p>
                                        <small class="text-muted">Added: ${new Date(note.created_at).toLocaleString()}</small>
                                    </div>
                                </div>
                            `;
                            columnElement.insertAdjacentHTML('beforeend', noteHtml);
                        });
                    }
                });
            } else {
                console.error('Failed to load notes:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading notes:', error);
        });
}


</script>

<?php include 'views/footer-dashboard.php' ?>
