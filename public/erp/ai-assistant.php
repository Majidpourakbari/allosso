<?php include 'views/headin2.php' ?>


<div class="dashboard-container">
    <?php include 'views/header-sidebar.php' ?>
        <!-- Dashboard Cards -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">AI Assistant</h4>
            </div>
            <div class="card-body">
                <!-- Task Search Form -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" id="taskSearchInput" class="form-control" placeholder="Search for tasks..." aria-label="Search for tasks">
                            <button class="btn btn-primary" type="button" id="searchTasksBtn">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Results -->
                <div id="searchResults" style="display: none;">
                    <h5 class="mb-3">Search Results</h5>
                    <div id="searchResultsTable"></div>
                </div>

                <!-- Loading State -->
                <div id="searchLoading" style="display: none;" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Searching tasks...</p>
                </div>

                <!-- No Results Message -->
                <div id="noResults" style="display: none;" class="text-center text-muted">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h5>No tasks found</h5>
                    <p>Try different search terms or check your spelling.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('taskSearchInput');
    const searchBtn = document.getElementById('searchTasksBtn');
    const searchResults = document.getElementById('searchResults');
    const searchResultsTable = document.getElementById('searchResultsTable');
    const searchLoading = document.getElementById('searchLoading');
    const noResults = document.getElementById('noResults');

    // Search button click handler
    searchBtn.addEventListener('click', function() {
        performSearch();
    });

    // Enter key handler
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    function performSearch() {
        const searchTerm = searchInput.value.trim();
        
        if (!searchTerm) {
            alert('Please enter a search term');
            return;
        }

        // Show loading state
        searchLoading.style.display = 'block';
        searchResults.style.display = 'none';
        noResults.style.display = 'none';

        // Perform search
        fetch('filter_tasks.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `search=${encodeURIComponent(searchTerm)}&search_type=simple`
        })
        .then(response => response.json())
        .then(data => {
            searchLoading.style.display = 'none';
            
            if (data.success && data.tasks && data.tasks.length > 0) {
                displaySearchResults(data.tasks);
            } else {
                showNoResults();
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            searchLoading.style.display = 'none';
            alert('Error performing search. Please try again.');
        });
    }

    function displaySearchResults(tasks) {
        let tableHTML = `
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Task ID</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Allo Section</th>
                            <th>Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        tasks.forEach(task => {
            const statusClass = getStatusClass(task.status);
            const statusText = getStatusText(task.status);
            const priorityClass = getPriorityClass(task.priority);
            const priorityText = getPriorityText(task.priority);
            
            tableHTML += `
                <tr>
                    <td><span class="badge bg-secondary">#${task.id}</span></td>
                    <td><strong>${escapeHtml(task.title)}</strong></td>
                    <td><span class="badge ${statusClass}">${statusText}</span></td>
                    <td><span class="badge ${priorityClass}">${priorityText}</span></td>
                    <td>${escapeHtml(task.allo_section || 'N/A')}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="progress me-2" style="width: 60px; height: 8px;">
                                <div class="progress-bar" style="width: ${task.progress || 0}%"></div>
                            </div>
                            <span class="small">${task.progress || 0}%</span>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewTaskDetails(${task.id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
            `;
        });

        tableHTML += `
                    </tbody>
                </table>
            </div>
        `;

        searchResultsTable.innerHTML = tableHTML;
        searchResults.style.display = 'block';
    }

    function showNoResults() {
        noResults.style.display = 'block';
    }

    function getStatusClass(status) {
        const statusMap = {
            '0': 'bg-secondary',
            '1': 'bg-primary',
            '2': 'bg-success',
            '3': 'bg-warning',
            '4': 'bg-dark',
            '5': 'bg-danger'
        };
        return statusMap[status] || 'bg-secondary';
    }

    function getStatusText(status) {
        const statusMap = {
            '0': 'Created',
            '1': 'In Progress',
            '2': 'Completed',
            '3': 'On Hold',
            '4': 'Archived',
            '5': 'To Debug'
        };
        return statusMap[status] || 'Unknown';
    }

    function getPriorityClass(priority) {
        const priorityMap = {
            '1': 'bg-success',
            '2': 'bg-warning',
            '3': 'bg-danger'
        };
        return priorityMap[priority] || 'bg-secondary';
    }

    function getPriorityText(priority) {
        const priorityMap = {
            '1': 'Low',
            '2': 'Medium',
            '3': 'High'
        };
        return priorityMap[priority] || 'Unknown';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});

// Global function to view task details (can be called from search results)
function viewTaskDetails(taskId) {
    // Open task details in a new window or redirect
    window.open(`tasks.php?task=${taskId}`, '_blank');
}
</script>

<?php include 'views/footer-dashboard.php' ?>
