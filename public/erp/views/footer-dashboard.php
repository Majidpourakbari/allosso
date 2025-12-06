<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');

    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
        }
    });

    // Automatic online status management
    const statusIcon = document.querySelector('#userStatusIcon');
    const statusText = document.querySelector('#userStatusText');
    let lastActivityTime = Date.now();
    let isCurrentlyOnline = true;
    
    // Function to update status display
    function updateStatusDisplay(isOnline) {
        if (statusIcon && statusText) {
            if (isOnline) {
                statusIcon.style.color = '#4CAF50';
                statusText.textContent = 'Online';
                isCurrentlyOnline = true;
            } else {
                statusIcon.style.color = '#ccc';
                statusText.textContent = 'Offline';
                isCurrentlyOnline = false;
            }
        }
    }

    // Function to set user online
    function setUserOnline() {
        if (!isCurrentlyOnline) {
            $.ajax({
                url: 'update_online_status.php',
                method: 'POST',
                data: { status: 1 },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateStatusDisplay(true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error setting user online:', error);
                }
            });
        }
    }

    // Function to set user offline
    function setUserOffline() {
        if (isCurrentlyOnline) {
            $.ajax({
                url: 'update_online_status.php',
                method: 'POST',
                data: { status: 0 },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateStatusDisplay(false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error setting user offline:', error);
                }
            });
        }
    }

    // Function to track user activity
    function trackActivity() {
        lastActivityTime = Date.now();
        setUserOnline(); // Set user online when active
    }

    // Function to check inactivity
    function checkInactivity() {
        const inactiveTime = Date.now() - lastActivityTime;
        const inactiveThreshold = 5 * 60 * 1000; // 5 minutes of inactivity
        
        if (inactiveTime > inactiveThreshold && isCurrentlyOnline) {
            setUserOffline();
        }
    }

    // Track user activity events
    const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
    
    activityEvents.forEach(function(event) {
        document.addEventListener(event, trackActivity, true);
    });

    // Page visibility API to handle tab switching
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // User switched away from tab - start countdown to offline
            setTimeout(function() {
                if (document.hidden) {
                    setUserOffline();
                }
            }, 2 * 60 * 1000); // 2 minutes after hiding tab
        } else {
            // User returned to tab - set online immediately
            trackActivity();
        }
    });

    // Check for inactivity every 30 seconds
    setInterval(checkInactivity, 30000);

    // Set user online on page load
    trackActivity();
});

// Add new function to toggle date/time format
let currentFormat = parseInt(localStorage.getItem('dateTimeFormat')) || 0;
let currentRegion = localStorage.getItem('selectedRegion') || 'IR';

function formatDateTime(format) {
    const now = new Date();
    const dateElement = document.getElementById('current-date');
    const timeElement = document.getElementById('current-time');
    
    // Get base time from PHP (now in Sweden timezone)
    const baseTime = new Date('<?php echo date("Y-m-d H:i:s"); ?>');
    const timeDiff = now.getTime() - new Date().getTime(); // Calculate time difference
    
    // Calculate current time based on base time
    const currentTime = new Date(baseTime.getTime() + timeDiff);
    
    // Now that server is in Sweden timezone, current time is already Sweden time
    const swedenHours = String(currentTime.getHours()).padStart(2, '0');
    const swedenMinutes = String(currentTime.getMinutes()).padStart(2, '0');
    const swedenSeconds = String(currentTime.getSeconds()).padStart(2, '0');
    const swedenTime = `${swedenHours}:${swedenMinutes}:${swedenSeconds}`;
    
    // Calculate Iran time (Sweden time + 2:30 for Iran timezone)
    const iranDate = new Date(currentTime);
    iranDate.setHours(iranDate.getHours() + 2);
    iranDate.setMinutes(iranDate.getMinutes() + 30);
    const iranHours = String(iranDate.getHours()).padStart(2, '0');
    const iranMinutes = String(iranDate.getMinutes()).padStart(2, '0');
    const iranSeconds = String(iranDate.getSeconds()).padStart(2, '0');
    const iranTime = `${iranHours}:${iranMinutes}:${iranSeconds}`;
    
    // Show only selected region
    if (currentRegion === 'IR') {
        // Get Persian date with English numbers
        const persianDate = now.toLocaleDateString('fa-IR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            numberingSystem: 'latn' // Use Latin (English) numbers
        });
        
        switch(format) {
            case 0: // Default format
                dateElement.textContent = `IR - ${persianDate} - ${iranTime}`;
                timeElement.textContent = '';
                break;
            case 1: // Compact format
                dateElement.textContent = `IR - ${persianDate} - ${iranHours}:${iranMinutes}`;
                timeElement.textContent = '';
                break;
            case 2: // Detailed format
                dateElement.textContent = `IR - ${persianDate} - ${iranTime} IRST`;
                timeElement.textContent = '';
                break;
        }
        // Hide the separate Persian date element since we're showing it in the main display
        document.getElementById('persian-date').style.display = 'none';
    } else {
        // Format date for Sweden (now the primary timezone)
        const year = currentTime.getFullYear();
        const month = String(currentTime.getMonth() + 1).padStart(2, '0');
        const day = String(currentTime.getDate()).padStart(2, '0');
        const formattedDate = `${year}/${month}/${day}`;
        
        switch(format) {
            case 0: // Default format
                dateElement.textContent = `SE - ${formattedDate} - ${swedenTime}`;
                timeElement.textContent = '';
                break;
            case 1: // Compact format
                dateElement.textContent = `SE - ${formattedDate} - ${swedenHours}:${swedenMinutes}`;
                timeElement.textContent = '';
                break;
            case 2: // Detailed format
                dateElement.textContent = `SE - ${formattedDate} - ${swedenTime} CET`;
                timeElement.textContent = '';
                break;
        }
        // Hide Persian date for Sweden
        document.getElementById('persian-date').style.display = 'none';
    }
}

function toggleDateTimeFormat() {
    // Toggle between IR and SE
    currentRegion = currentRegion === 'IR' ? 'SE' : 'IR';
    localStorage.setItem('selectedRegion', currentRegion);
    formatDateTime(currentFormat);
}

function updateDateTime() {
    formatDateTime(currentFormat);
}

// Initialize with saved format and region
formatDateTime(currentFormat);
setInterval(updateDateTime, 1000);

// Update the datetime container style
document.querySelector('.datetime-container').style.minWidth = 'auto';

// Update time every second without page reload
setInterval(function() {
    formatDateTime(currentFormat);
}, 1000);
</script> 