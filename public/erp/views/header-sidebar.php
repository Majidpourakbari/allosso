    <!-- Mobile Menu Toggle -->
    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Allohub</h3>
        </div>
        <!-- Profile Info Box -->
        <div class="profile-info-box" onclick="window.location.href='profile'">
            <div class="profile-info-content">
                <img src="uploads/profiles/<?php echo $my_profile_avatar; ?>" alt="Profile" class="profile-info-avatar">
                <div class="profile-info-details">
                    <div class="profile-info-name"><?php echo $my_profile_name; ?></div>
                    <div class="profile-info-email"><?php echo $my_profile_email; ?></div>
                </div>
            </div>
        </div>
        <ul class="nav-menu">
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="dashboard" class="nav-link">
                    <i class="bi bi-house"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Work Section -->
            <li class="nav-item">
                <a href="tasks" class="nav-link">
                    <i class="bi bi-list-check"></i>
                    <span>Tasks</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="broadcast" class="nav-link">
                    <i class="bi bi-broadcast"></i>
                    <span>Broadcast</span>
                </a>
            </li>

            <!-- AI Assistant -->
            <li class="nav-item">
                <a href="ai-assistant" class="nav-link">
                    <i class="bi bi-robot"></i>
                    <span>AI Assistant</span>
                </a>
            </li>

        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="header-left">
                <div class="datetime-container" onclick="toggleDateTimeFormat()" style="cursor: pointer; background: #f8f9fa; padding: 8px 12px; border-radius: 6px; width: fit-content; display: inline-block;">
                    <div id="current-date" style="font-size: 0.9em; margin-bottom: 4px; white-space: nowrap;"></div>
                    <div id="current-time" style="font-size: 0.9em; white-space: nowrap;"></div>
                    <div id="persian-date" style="display: none; margin-top: 4px; font-size: 0.85em; color: #666;"></div>
                </div>
            </div>
            <div class="header-right">
                <div class="search-box">
                    <input type="text" placeholder="Search...">
                    <i class="fas fa-search"></i>
                </div>
                <div class="messages-icon" onclick="toggleMessages()">
                    <i class="fas fa-envelope"></i>
                    <span class="message-badge">0</span>
                    <div class="dropdown-menu messages-dropdown">
                        <div class="messages-header">
                            <h6>Messages</h6>
                        </div>
                        <div class="messages-body" id="usersList">
                            <!-- Users will be loaded here -->
                        </div>
                    </div>
                </div>
                <div class="notification-icon" onclick="toggleNotifications()">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                    <div class="dropdown-menu notifications-dropdown" id="notificationsDropdown">
                        <div class="notifications-header">
                            <h6>Notifications</h6>
                            <button class="btn btn-link btn-sm" onclick="markAllAsRead(event)">Mark all as read</button>
                        </div>
                        <div class="notifications-body" id="notificationsList">
                            <!-- Notifications will be loaded here -->
                        </div>
                        <div class="notifications-footer">
                            <a href="notifications" class="view-all-btn">View All Notifications</a>
                        </div>
                    </div>
                </div>
                <div class="profile-dropdown">
                    <img src="uploads/profiles/<?php echo $my_profile_avatar; ?>" alt="Profile" class="profile-avatar" onclick="toggleProfile()">
                    <div class="dropdown-menu profile-dropdown-menu">
                        <div class="profile-dropdown-header">
                            <div class="profile-dropdown-info">
                                <div class="profile-dropdown-name"><?php echo $my_profile_name; ?></div>
                                <div class="profile-dropdown-email"><?php echo $my_profile_email; ?></div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="profile" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>Profile</span>
                        </a>
                        <div class="dropdown-item">
                            <div class="status-display-container">
                                <div class="status-text">
                                    <i class="fas fa-circle" id="userStatusIcon" style="color: #4CAF50;"></i>
                                    <span id="userStatusText">Online</span>
                                </div>
                                <div class="status-info">
                                    <small style="color: #666; font-size: 0.7em;">Automatic</small>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" onclick="confirmLogout()" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Log out</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

<!-- Chat Box Container -->
<div id="chatBoxContainer" class="chat-box-container">
    <div class="chat-box">
        <div class="chat-header">
            <div class="chat-user-info">
                <img src="" alt="" class="chat-user-avatar">
                <div class="chat-user-details">
                    <div class="chat-user-name"></div>
                    <div class="chat-user-status"></div>
                </div>
            </div>
            <div class="chat-actions">
                <button class="chat-action-btn" onclick="toggleFullScreen()" title="Toggle Full Screen">
                    <i class="fas fa-expand"></i>
                </button>
                <button class="chat-action-btn" onclick="minimizeChat()">
                    <i class="fas fa-minus"></i>
                </button>
                <button class="chat-action-btn" onclick="closeChat()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- Pinned Messages Section -->
        <div class="pinned-messages-section" id="pinnedMessagesSection" style="display: none;">
            <div class="pinned-messages-header" onclick="togglePinnedMessages()">
                <div class="pinned-info">
                    <i class="fas fa-thumbtack"></i>
                    <span class="pinned-count">0</span>
                    <span class="pinned-text">pinned messages</span>
                </div>
                <div class="pinned-actions">
                    <i class="fas fa-chevron-down" id="pinnedToggleIcon"></i>
                </div>
            </div>
            <div class="pinned-messages-list" id="pinnedMessagesList" style="display: none;">
                <!-- Pinned messages will be loaded here -->
            </div>
        </div>
        <div class="chat-content">
            <div class="chat-users-sidebar" id="chatUsersSidebar">
                <div class="chat-users-header">
                    <h6>Users</h6>
                </div>
                <div class="chat-users-list" id="chatUsersList">
                    <!-- Users will be loaded here -->
                </div>
            </div>
            <div class="chat-main-area">
                <div class="chat-messages" id="chatMessages">
                    <!-- Messages will be loaded here -->
                </div>
                <div class="chat-reply-area" id="chatReplyArea" style="display: none;">
                    <div class="reply-preview">
                        <div class="reply-info">
                            <span class="reply-label">Replying to:</span>
                            <span class="reply-sender"></span>
                        </div>
                        <div class="reply-content"></div>
                        <button class="reply-cancel" onclick="cancelReply()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="chat-input">
                    <div class="chat-input-actions">
                        <button class="chat-action-btn" onclick="toggleEmojiPicker()" title="Add Emoji">
                            <i class="fas fa-smile"></i>
                        </button>
                        <button class="chat-action-btn" onclick="toggleVoiceRecorder()" title="Record Voice Message">
                            <i class="fas fa-microphone"></i>
                        </button>
                        <label class="chat-action-btn" title="Attach File">
                            <i class="fas fa-paperclip"></i>sdsd
                            <input type="file" id="fileInput" style="display: none;" onchange="handleFileSelect(event)">
                        </label>
                    </div>
                    
                    <!-- Emoji Picker -->
                    <div class="emoji-picker" id="emojiPicker" style="display: none;">
                        <div class="emoji-grid">
                            <button class="emoji-btn" onclick="addEmoji('😀')">😀</button>
                            <button class="emoji-btn" onclick="addEmoji('😂')">😂</button>
                            <button class="emoji-btn" onclick="addEmoji('😍')">😍</button>
                            <button class="emoji-btn" onclick="addEmoji('🥰')">🥰</button>
                            <button class="emoji-btn" onclick="addEmoji('😎')">😎</button>
                            <button class="emoji-btn" onclick="addEmoji('🤔')">🤔</button>
                            <button class="emoji-btn" onclick="addEmoji('😢')">😢</button>
                            <button class="emoji-btn" onclick="addEmoji('😡')">😡</button>
                            <button class="emoji-btn" onclick="addEmoji('👍')">👍</button>
                            <button class="emoji-btn" onclick="addEmoji('👎')">👎</button>
                            <button class="emoji-btn" onclick="addEmoji('❤️')">❤️</button>
                            <button class="emoji-btn" onclick="addEmoji('💔')">💔</button>
                            <button class="emoji-btn" onclick="addEmoji('🎉')">🎉</button>
                            <button class="emoji-btn" onclick="addEmoji('🔥')">🔥</button>
                            <button class="emoji-btn" onclick="addEmoji('💯')">💯</button>
                            <button class="emoji-btn" onclick="addEmoji('👏')">👏</button>
                            <button class="emoji-btn" onclick="addEmoji('🙏')">🙏</button>
                            <button class="emoji-btn" onclick="addEmoji('🤝')">🤝</button>
                            <button class="emoji-btn" onclick="addEmoji('💪')">💪</button>
                            <button class="emoji-btn" onclick="addEmoji('✨')">✨</button>
                        </div>
                    </div>
                    
                    <textarea placeholder="Type a message..." id="messageInput"></textarea>
                    <button onclick="sendMessage()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Voice Recorder -->
        <div id="voiceRecorder" class="voice-recorder" style="display: none;">
            <div class="recorder-header">
                <span>Recording...</span>
                <span id="recordingTime">00:00</span>
            </div>
            <div class="recorder-waveform" id="recorderWaveform"></div>
            <div class="recorder-actions">
                <button class="recorder-btn cancel" onclick="cancelRecording()">
                    <i class="fas fa-times"></i>
                </button>
                <button class="recorder-btn stop" onclick="stopRecording()">
                    <i class="fas fa-stop"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmLogout() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of your account",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, log out!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'logout.php';
        }
    });
}

// Function to format date and time
function formatDateTime(date, time) {
    const dateObj = new Date(date + 'T' + time);
    return dateObj.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });
}

// Function to check if notification is read
function isNotificationRead(users_read, my_profile_id) {
    if (!users_read) return false;
    return users_read.split(',').includes(my_profile_id.toString());
}

// Function to mark single notification as read
function markNotificationAsRead(notificationId, event) {
    event.stopPropagation();
    
    fetch('mark_notifications_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
            notification_id: notificationId,
            my_profile_id: <?php echo $my_profile_id; ?>
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the notification item styling immediately
            const notificationItem = document.querySelector(`.dropdown-item[data-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('unread');
                notificationItem.classList.add('read');
            }
            
            // Reload notifications to update count and list
            loadNotifications();
        } else {
            console.error('Failed to mark notification as read:', data.message);
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Function to load notifications
function loadNotifications() {
    fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update notification badge
                const badge = document.getElementById('notificationBadge');
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
                
                // Update notifications list
                const notificationsList = document.getElementById('notificationsList');
                notificationsList.innerHTML = '';
                
                if (data.notifications.length === 0) {
                    notificationsList.innerHTML = '<div class="no-notifications">No notifications</div>';
                } else {
                    data.notifications.forEach(notification => {
                        const isRead = isNotificationRead(notification.users_read, <?php echo $my_profile_id; ?>);
                        const notificationHtml = `
                            <div class="dropdown-item ${isRead ? 'read' : 'unread'}" 
                                 data-id="${notification.id}"
                                 onclick="markNotificationAsRead(${notification.id}, event)">
                                <div class="notification-content">
                                    <div class="notification-header">
                                        <img src="uploads/profiles/${notification.avatar || 'default-avatar.png'}" 
                                             alt="${notification.user_name}" 
                                             class="notification-avatar">
                                        <div class="notification-info">
                                            <div class="notification-title">${notification.message}</div>
                                            <div class="notification-time">${notification.date} | ${notification.time}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        notificationsList.innerHTML += notificationHtml;
                    });
                }
            } else {
                console.error('Failed to load notifications:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });
}

// Function to mark all notifications as read
function markAllAsRead(event) {
    event.stopPropagation();
    
    fetch('mark_notifications_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
            my_profile_id: <?php echo $my_profile_id; ?>
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update all notification items styling immediately
            const unreadItems = document.querySelectorAll('.dropdown-item.unread');
            unreadItems.forEach(item => {
                item.classList.remove('unread');
                item.classList.add('read');
            });
            
            // Reload notifications to update count
            loadNotifications();
        } else {
            console.error('Failed to mark all notifications as read:', data.message);
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

// Global function to update notification counter (can be called from other parts of the app)
function updateNotificationCounter() {
    fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error updating notification counter:', error);
        });
}

// Load notifications when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    
    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
});

// Add styles for notifications
const style = document.createElement('style');
style.textContent = `
    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
    }
    
    .notifications-header h6 {
        margin: 0;
        font-weight: 600;
    }
    
    .notifications-body {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .dropdown-item {
        padding: 10px 15px;
        border-bottom: 1px solid #f5f5f5;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .dropdown-item.unread {
        background-color: #f0f7ff;
    }
    
    .notification-content {
        display: flex;
        flex-direction: column;
    }
    
    .notification-header {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    
    .notification-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .notification-info {
        flex: 1;
    }
    
    .notification-title {
        font-size: 0.9em;
        margin-bottom: 2px;
    }
    
    .notification-time {
        font-size: 0.8em;
        color: #666;
    }
    
    .no-notifications {
        padding: 15px;
        text-align: center;
        color: #666;
    }
`;
document.head.appendChild(style);

// Add audio element for message sound
const messageSound = new Audio('uploads/message.mp3');

// Function to load users
function loadUsers() {
    $.ajax({
        url: 'get_users.php',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const usersList = $('#usersList');
                
                if (response.users.length === 0) {
                    if (!usersList.find('.no-users').length) {
                        usersList.html('<div class="no-users">No users found</div>');
                    }
                } else {
                    // Get unread counts
                    $.ajax({
                        url: 'get_unread_count.php',
                        method: 'GET',
                        success: function(unreadResponse) {
                            if (unreadResponse.success) {
                                const unreadCounts = {};
                                let totalUnread = 0;
                                
                                unreadResponse.unread_counts.forEach(count => {
                                    unreadCounts[count.sender_id] = count.unread_count;
                                    totalUnread += count.unread_count;
                                });
                                
                                // Update message badge in header
                                const messageBadge = $('.message-badge');
                                const previousUnread = parseInt(messageBadge.text()) || 0;
                                
                                if (totalUnread > 0) {
                                    messageBadge.text(totalUnread > 99 ? '99+' : totalUnread);
                                    messageBadge.show();
                                    
                                    // Play sound if there are new unread messages
                                    if (totalUnread > previousUnread) {
                                        messageSound.play().catch(error => {
                                            console.log('Error playing sound:', error);
                                        });
                                    }
                                } else {
                                    messageBadge.hide();
                                }
                                
                                // Create a temporary container for new content
                                const tempContainer = $('<div>');
                                
                                response.users.forEach(user => {
                                    const unreadCount = unreadCounts[user.id] || 0;
                                    const userHtml = `
                                        <div class="dropdown-item user-item" data-user-id="${user.id}">
                                            <div class="user-content">
                                                <div class="user-header">
                                                    <img src="uploads/profiles/${user.avatar}" 
                                                         alt="${user.name}" 
                                                         class="user-avatar">
                                                    <div class="user-info">
                                                        <div class="user-name">${user.name}</div>
                                                        <div class="user-status">
                                                            <i class="fas fa-circle ${user.is_online ? 'online' : 'offline'}"></i>
                                                            <span>${user.is_online ? 'Online' : 'Offline'}</span>
                                                        </div>
                                                    </div>
                                                    ${unreadCount > 0 ? `
                                                        <div class="unread-badge">${unreadCount}</div>
                                                    ` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    tempContainer.append(userHtml);
                                });
                                
                                // Smoothly update the list
                                if (usersList.html() !== tempContainer.html()) {
                                    usersList.fadeOut(100, function() {
                                        usersList.html(tempContainer.html());
                                        usersList.fadeIn(100);
                                    });
                                }
                            }
                        }
                    });
                }
            }
        },
        error: function() {
            console.error('Error loading users');
        }
    });
}

// Load users when page loads
$(document).ready(function() {
    loadUsers();
    
    // Refresh users list every 30 seconds
    setInterval(loadUsers, 1000);
    
    // Update last seen every minute
    setInterval(updateLastSeen, 60000);
    
    // Update last seen on user interaction
    $(document).on('mousemove keypress', function() {
        updateLastSeen();
    });
});

// Function to update last seen
function updateLastSeen() {
    $.ajax({
        url: 'update_last_seen.php',
        method: 'POST',
        success: function(response) {
            if (!response.success) {
                console.error('Failed to update last seen:', response.message);
            }
        },
        error: function() {
            console.error('Error updating last seen');
        }
    });
}

// Add styles for messages
const messagesStyle = document.createElement('style');
messagesStyle.textContent = `
    .messages-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
    }
    
    .messages-header h6 {
        margin: 0;
        font-weight: 600;
    }
    
    .messages-body {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .user-item {
        padding: 10px 15px;
        border-bottom: 1px solid #f5f5f5;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .user-item:hover {
        background-color: #f8f9fa;
    }
    
    .user-content {
        display: flex;
        flex-direction: column;
    }
    
    .user-header {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .user-info {
        flex: 1;
    }
    
    .user-name {
        font-size: 0.9em;
        margin-bottom: 2px;
    }
    
    .user-status {
        font-size: 0.8em;
        color: #666;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .user-status i.online {
        color: #4CAF50;
        font-size: 0.7em;
    }
    
    .user-status i.offline {
        color: #ccc;
        font-size: 0.7em;
    }
    
    .no-users {
        padding: 15px;
        text-align: center;
        color: #666;
    }
`;
document.head.appendChild(messagesStyle);

// Add styles for chat box
const chatStyle = document.createElement('style');
chatStyle.textContent = `
    .chat-box-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        display: none;
    }

    .chat-box {
        width: 350px;
        height: 500px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .chat-box.fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
        border-radius: 0;
        z-index: 9999;
    }

    .chat-header {
        padding: 15px;
        background: var(--primary-dark);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .chat-user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chat-user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .chat-user-details {
        display: flex;
        flex-direction: column;
    }

    .chat-user-name {
        font-weight: 600;
        font-size: 1.1em;
    }

    .chat-user-status {
        font-size: 0.8em;
        opacity: 0.8;
    }

    .chat-actions {
        display: flex;
        gap: 10px;
    }

    .chat-action-btn {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 5px;
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    .chat-action-btn:hover {
        opacity: 1;
    }

    .chat-content {
        flex: 1;
        display: flex;
        overflow: hidden;
    }

    .chat-users-sidebar {
        width: 0;
        background: #f8f9fa;
        border-right: 1px solid #eee;
        display: flex;
        flex-direction: column;
        transition: width 0.3s ease;
        overflow: hidden;
    }

    .chat-box.fullscreen .chat-users-sidebar {
        width: 280px;
    }

    .chat-users-header {
        padding: 15px;
        border-bottom: 1px solid #eee;
        background: white;
        flex-shrink: 0;
    }

    .chat-users-header h6 {
        margin: 0;
        font-weight: 600;
        color: #333;
    }

    .chat-users-list {
        flex: 1;
        overflow-y: auto;
        padding: 0;
    }

    .chat-user-item {
        padding: 12px 15px;
        border-bottom: 1px solid #f5f5f5;
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chat-user-item:hover {
        background-color: #e9ecef;
    }

    .chat-user-item.active {
        background-color: var(--primary-dark);
        color: white;
    }

    .chat-user-item.active .chat-user-status {
        color: rgba(255, 255, 255, 0.8);
    }

    .chat-user-item-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .chat-user-item-info {
        flex: 1;
        min-width: 0;
    }

    .chat-user-item-name {
        font-weight: 500;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-user-item-status {
        font-size: 0.8em;
        color: #666;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .chat-user-item-status i.online {
        color: #4CAF50;
        font-size: 0.7em;
    }

    .chat-user-item-status i.offline {
        color: #ccc;
        font-size: 0.7em;
    }

    .chat-user-item.active .chat-user-item-status {
        color: rgba(255, 255, 255, 0.8);
    }

    .chat-user-item.active .chat-user-item-status i.online {
        color: #90EE90;
    }

    .chat-main-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f5f5f5;
    }

    .message {
        position: relative;
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 15px;
        margin: 5px 0;
        transition: background-color 0.2s;
    }

    .message.sent {
        align-self: flex-end;
        background: var(--primary-dark);
        color: white;
        border-bottom-right-radius: 5px;
        margin-left: auto;
    }

    .message.sent:hover {
        background: var(--primary);
    }

    .message.received {
        align-self: flex-start;
        background: white;
        color: var(--primary-dark);
        border-bottom-left-radius: 5px;
        margin-right: auto;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .message.received:hover {
        background: #f8f9fa;
    }

    .message-actions {
        position: absolute;
        top: -8px;
        right: 10px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        padding: 4px;
        display: none;
        gap: 4px;
        z-index: 10;
    }

    .message.sent .message-actions {
        background: var(--primary-dark);
    }

    .message:hover .message-actions {
        display: flex;
    }

    .message-action-btn {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 4px 6px;
        border-radius: 8px;
        font-size: 0.8em;
        transition: all 0.2s;
    }

    .message.sent .message-action-btn {
        color: rgba(255, 255, 255, 0.8);
    }

    .message-action-btn:hover {
        background-color: rgba(0, 0, 0, 0.1);
        color: #333;
    }

    .message.sent .message-action-btn:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .chat-reply-area {
        padding: 10px 15px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
        flex-shrink: 0;
    }

    .reply-preview {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: white;
        padding: 8px 12px;
        border-radius: 8px;
        border-left: 3px solid var(--primary-dark);
        position: relative;
    }

    .reply-info {
        flex: 1;
        min-width: 0;
    }

    .reply-label {
        font-size: 0.75em;
        color: #666;
        font-weight: 500;
    }

    .reply-sender {
        font-size: 0.8em;
        color: var(--primary-dark);
        font-weight: 600;
        margin-left: 5px;
    }

    .reply-content {
        font-size: 0.85em;
        color: #333;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 300px;
    }

    .reply-cancel {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: background-color 0.2s;
        flex-shrink: 0;
    }

    .reply-cancel:hover {
        background-color: #f0f0f0;
        color: #333;
    }

    .message.reply-message {
        border-left: 3px solid var(--primary-dark);
        margin-left: 20px;
        position: relative;
    }

    .message.sent.reply-message {
        border-left: none;
        border-right: 3px solid var(--primary-dark);
        margin-left: auto;
        margin-right: 20px;
    }

    .reply-indicator {
        font-size: 0.75em;
        color: #666;
        margin-bottom: 4px;
        font-weight: 500;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        transition: background-color 0.2s;
        background: rgba(0, 0, 0, 0.05);
    }

    .reply-indicator:hover {
        background: rgba(0, 0, 0, 0.1);
    }

    .message.sent .reply-indicator {
        background: rgba(255, 255, 255, 0.1);
    }

    .message.sent .reply-indicator:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .reply-indicator .reply-sender-name {
        color: var(--primary-dark);
        font-weight: 600;
    }

    .reply-indicator .reply-arrow {
        margin: 0 4px;
        opacity: 0.6;
    }

    .highlight-message {
        animation: highlightPulse 2s ease-in-out;
    }

    @keyframes highlightPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(52, 152, 219, 0.3);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(52, 152, 219, 0);
        }
    }

    .message.sent.highlight-message {
        animation: highlightPulseSent 2s ease-in-out;
    }

    @keyframes highlightPulseSent {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(255, 255, 255, 0.3);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
        }
    }

    .message-sender {
        font-size: 0.8em;
        color: #666;
        margin-bottom: 4px;
    }

    .message-time {
        font-size: 0.7em;
        opacity: 0.7;
        margin-top: 5px;
        text-align: right;
    }

    .chat-input {
        padding: 15px;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
        background: white;
        flex-shrink: 0;
    }

    .chat-input textarea {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 10px 15px;
        resize: none;
        height: 40px;
        outline: none;
    }

    .chat-input button {
        background: var(--primary-dark);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .chat-input button:hover {
        background: var(--primary);
    }

    .chat-box.minimized {
        height: 60px;
    }

    .chat-box.minimized .chat-content,
    .chat-box.minimized .chat-input {
        display: none;
    }

    .chat-box.fullscreen .chat-action-btn[onclick="toggleFullScreen()"] i {
        transform: rotate(180deg);
    }

    .unread-count {
        color: #ff4444;
        font-weight: 600;
        margin-left: 5px;
    }

    .chat-user-item.active .unread-count {
        color: #ffcccc;
    }

    .no-users {
        padding: 20px;
        text-align: center;
        color: #666;
        font-style: italic;
    }

    .chat-users-sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .chat-users-sidebar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .chat-users-sidebar::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .chat-users-sidebar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Pinned message styles */
    .pinned-messages-section {
        background: rgba(255, 255, 255, 0.95);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    .pinned-messages-header {
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .pinned-messages-header:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    .pinned-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9em;
    }

    .pinned-info i {
        color: #ffd700;
        font-size: 0.8em;
    }

    .pinned-count {
        font-weight: 600;
        color: #ffd700;
    }

    .pinned-text {
        color: #333;
    }

    .pinned-actions {
        display: flex;
        align-items: center;
    }

    .pinned-actions i {
        color: #666;
        transition: transform 0.2s;
    }

    .pinned-messages-header.expanded .pinned-actions i {
        transform: rotate(180deg);
    }

    .pinned-messages-list {
        max-height: 200px;
        overflow-y: auto;
        background: rgba(0, 0, 0, 0.02);
    }

    .pinned-message-item {
        padding: 8px 15px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .pinned-message-item:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    .pinned-message-item:last-child {
        border-bottom: none;
    }

    .pinned-message-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .pinned-message-content {
        flex: 1;
        min-width: 0;
    }

    .pinned-message-sender {
        font-size: 0.8em;
        font-weight: 600;
        color: #ffd700;
        margin-bottom: 2px;
    }

    .pinned-message-text {
        font-size: 0.85em;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.3;
    }

    .pinned-message-time {
        font-size: 0.7em;
        color: #666;
        margin-top: 2px;
    }

    .pinned-message-actions {
        display: flex;
        gap: 5px;
        margin-top: 5px;
    }

    .pinned-message-action-btn {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 0.7em;
        transition: all 0.2s;
    }

    .pinned-message-action-btn:hover {
        background: rgba(0, 0, 0, 0.1);
        color: #333;
    }

    .pinned-message-action-btn.unpin-btn {
        color: #ff6b6b;
    }

    .pinned-message-action-btn.unpin-btn:hover {
        background: rgba(255, 107, 107, 0.2);
    }
`;
document.head.appendChild(chatStyle);

// Chat box functionality
let currentChatUser = null;
let isFullScreen = false;
let replyingToMessage = null;

function replyToMessage(messageId, senderName, messageContent) {
    replyingToMessage = {
        id: messageId,
        sender: senderName,
        content: messageContent
    };
    
    // Show reply area
    const replyArea = document.getElementById('chatReplyArea');
    const replySender = replyArea.querySelector('.reply-sender');
    const replyContent = replyArea.querySelector('.reply-content');
    
    replySender.textContent = senderName;
    replyContent.textContent = messageContent;
    replyArea.style.display = 'block';
    
    // Focus on message input
    document.getElementById('messageInput').focus();
}

function cancelReply() {
    replyingToMessage = null;
    document.getElementById('chatReplyArea').style.display = 'none';
    document.getElementById('messageInput').focus();
}

function scrollToOriginalMessage(messageId) {
    const chatMessages = document.getElementById('chatMessages');
    const originalMessage = chatMessages.querySelector(`[data-message-id="${messageId}"]`);
    
    if (originalMessage) {
        // Add highlight effect
        originalMessage.classList.add('highlight-message');
        
        // Scroll to the message
        originalMessage.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        
        // Remove highlight after 2 seconds
        setTimeout(() => {
            originalMessage.classList.remove('highlight-message');
        }, 2000);
    }
}

function loadMessages(userId) {
    const chatMessages = document.getElementById('chatMessages');
    const loadingMessage = '<div class="message received"><div class="message-content">Loading messages...</div></div>';
    
    // Only show loading message on initial load
    if (!chatMessages.dataset.loaded) {
        chatMessages.innerHTML = loadingMessage;
    }
    
    $.ajax({
        url: 'chat_actions.php',
        method: 'POST',
        data: {
            action: 'get_messages',
            user_id: userId
        },
        success: function(response) {
            if (response.success) {
                // Create a temporary container for new messages
                const tempContainer = document.createElement('div');
                
                if (response.messages.length === 0) {
                    tempContainer.innerHTML = '<div class="message received"><div class="message-content">No messages yet. Start the conversation!</div></div>';
                } else {
                    response.messages.forEach(message => {
                        const isSent = message.sender_id == <?php echo $my_profile_id; ?>;
                        const isReply = message.reply_to_id && message.reply_to_message;
                        const messageContent = message.content || '';
                        const messageHtml = `
                            <div class="message ${isSent ? 'sent' : 'received'} ${isReply ? 'reply-message' : ''}" data-message-id="${message.id}">
                                <div class="message-actions">
                                    <button class="message-action-btn" onclick="replyToMessage(${message.id}, '${message.sender_name}', '${messageContent.replace(/'/g, "\\'").replace(/"/g, '\\"')}')" title="Reply">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                    <button class="message-action-btn" onclick="${message.is_pinned ? 'unpinMessage' : 'pinMessage'}(${message.id})" title="${message.is_pinned ? 'Unpin' : 'Pin'} message">
                                        <i class="fas fa-thumbtack ${message.is_pinned ? 'pinned' : ''}"></i>
                                    </button>
                                </div>
                                <div class="message-content">
                                    ${!isSent ? `<div class="message-sender">${message.sender_name}</div>` : ''}
                                    ${isReply ? `
                                        <div class="reply-indicator" onclick="scrollToOriginalMessage(${message.reply_to_id})" title="Click to view original message">
                                            <span class="reply-sender-name">${message.reply_to_message.sender_name || 'Unknown'}</span>
                                            <span class="reply-arrow">→</span>
                                            <span>${message.reply_to_message.content || 'Message not available'}</span>
                                        </div>
                                    ` : ''}
                                    ${formatMessageContent(message)}
                                </div>
                                <div class="message-time">${new Date(message.created_at).toLocaleTimeString()}</div>
                            </div>
                        `;
                        tempContainer.innerHTML += messageHtml;
                    });
                }
                
                // Check if content has changed
                if (chatMessages.innerHTML !== tempContainer.innerHTML) {
                    // Store current scroll position and height
                    const oldScrollHeight = chatMessages.scrollHeight;
                    const oldScrollTop = chatMessages.scrollTop;
                    const wasAtBottom = oldScrollHeight - oldScrollTop === chatMessages.clientHeight;
                    
                    // Update content
                    chatMessages.innerHTML = tempContainer.innerHTML;
                    
                    // If we were at bottom or this is a new message, scroll to bottom
                    if (wasAtBottom || chatMessages.dataset.lastMessageId !== response.messages[response.messages.length - 1]?.id) {
                        scrollToBottom(chatMessages);
                    } else {
                        // Maintain relative scroll position
                        const newScrollHeight = chatMessages.scrollHeight;
                        const scrollDiff = newScrollHeight - oldScrollHeight;
                        chatMessages.scrollTop = oldScrollTop + scrollDiff;
                    }
                    
                    // Update last message ID
                    if (response.messages.length > 0) {
                        chatMessages.dataset.lastMessageId = response.messages[response.messages.length - 1].id;
                    }
                }
                
                // Mark as loaded
                chatMessages.dataset.loaded = 'true';
            } else {
                if (!chatMessages.dataset.loaded) {
                    chatMessages.innerHTML = '<div class="message received"><div class="message-content">Error loading messages</div></div>';
                }
            }
        },
        error: function() {
            if (!chatMessages.dataset.loaded) {
                chatMessages.innerHTML = '<div class="message received"><div class="message-content">Error loading messages</div></div>';
            }
        }
    });
}

function sendMessage() {
    if (!currentChatUser) return;
    
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (message) {
        const messageData = {
            action: 'send',
            receiver_id: currentChatUser,
            message: message
        };
        
        // Add reply data if replying to a message
        if (replyingToMessage) {
            messageData.reply_to_id = replyingToMessage.id;
        }
        
        $.ajax({
            url: 'chat_actions.php',
            method: 'POST',
            data: messageData,
            success: function(response) {
                if (response.success) {
                    messageInput.value = '';
                    cancelReply(); // Clear reply state
                    loadMessages(currentChatUser);
                    // Force scroll to bottom after sending
                    const chatMessages = document.getElementById('chatMessages');
                    scrollToBottom(chatMessages);
                } else {
                    alert('Error sending message: ' + response.message);
                }
            },
            error: function() {
                alert('Error sending message');
            }
        });
    }
}

// Function to pin a message
function pinMessage(messageId) {
    $.ajax({
        url: 'chat_actions.php',
        method: 'POST',
        data: {
            action: 'pin_message',
            message_id: messageId
        },
        success: function(response) {
            if (response.success) {
                // Reload messages to show updated pin status
                loadMessages(currentChatUser);
                // Reload pinned messages section
                loadPinnedMessages(currentChatUser);
                // Show success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Message Pinned',
                    text: 'Message has been pinned successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to pin message'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to pin message'
            });
        }
    });
}

// Function to unpin a message
function unpinMessage(messageId) {
    $.ajax({
        url: 'chat_actions.php',
        method: 'POST',
        data: {
            action: 'unpin_message',
            message_id: messageId
        },
        success: function(response) {
            if (response.success) {
                // Reload messages to show updated pin status
                loadMessages(currentChatUser);
                // Reload pinned messages section
                loadPinnedMessages(currentChatUser);
                // Show success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Message Unpinned',
                    text: 'Message has been unpinned successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to unpin message'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to unpin message'
            });
        }
    });
}

// Function to load pinned messages
function loadPinnedMessages(userId) {
    if (!userId) return;
    
    $.ajax({
        url: 'chat_actions.php',
        method: 'POST',
        data: {
            action: 'get_pinned_messages',
            user_id: userId
        },
        success: function(response) {
            if (response.success) {
                const pinnedSection = document.getElementById('pinnedMessagesSection');
                const pinnedCount = document.querySelector('.pinned-count');
                const pinnedList = document.getElementById('pinnedMessagesList');
                
                if (response.count > 0) {
                    // Show pinned messages section
                    pinnedSection.style.display = 'block';
                    pinnedCount.textContent = response.count;
                    
                    // Update pinned messages list
                    pinnedList.innerHTML = '';
                    response.pinned_messages.forEach(message => {
                        const messageContent = message.content || '';
                        const messageText = message.message_type === 'voice' ? 'Voice message' : 
                                          message.message_type === 'file' ? 'File: ' + (message.file_name || 'Unknown') :
                                          messageContent;
                        
                        const pinnedMessageHtml = `
                            <div class="pinned-message-item" onclick="scrollToMessage(${message.id})">
                                <img src="uploads/profiles/${message.sender_avatar || 'no-profile.jpg'}" 
                                     alt="${message.sender_name}" 
                                     class="pinned-message-avatar">
                                <div class="pinned-message-content">
                                    <div class="pinned-message-sender">${message.sender_name}</div>
                                    <div class="pinned-message-text">${messageText}</div>
                                    <div class="pinned-message-time">${new Date(message.created_at).toLocaleString()}</div>
                                    <div class="pinned-message-actions">
                                        <button class="pinned-message-action-btn unpin-btn" 
                                                onclick="unpinMessage(${message.id}); event.stopPropagation();" 
                                                title="Unpin message">
                                            <i class="fas fa-thumbtack"></i> Unpin
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        pinnedList.innerHTML += pinnedMessageHtml;
                    });
                } else {
                    // Hide pinned messages section if no pinned messages
                    pinnedSection.style.display = 'none';
                }
            }
        },
        error: function() {
            console.error('Error loading pinned messages');
        }
    });
}

// Function to toggle pinned messages section
function togglePinnedMessages() {
    const pinnedList = document.getElementById('pinnedMessagesList');
    const pinnedHeader = document.querySelector('.pinned-messages-header');
    const toggleIcon = document.getElementById('pinnedToggleIcon');
    
    if (pinnedList.style.display === 'none') {
        pinnedList.style.display = 'block';
        pinnedHeader.classList.add('expanded');
    } else {
        pinnedList.style.display = 'none';
        pinnedHeader.classList.remove('expanded');
    }
}

// Function to scroll to a specific message
function scrollToMessage(messageId) {
    const chatMessages = document.getElementById('chatMessages');
    const targetMessage = chatMessages.querySelector(`[data-message-id="${messageId}"]`);
    
    if (targetMessage) {
        // Add highlight effect
        targetMessage.classList.add('highlight-message');
        
        // Scroll to the message
        targetMessage.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        
        // Remove highlight after 2 seconds
        setTimeout(() => {
            targetMessage.classList.remove('highlight-message');
        }, 2000);
    }
}

// Add smooth scroll function
function scrollToBottom(element) {
    element.scrollTo({
        top: element.scrollHeight,
        behavior: 'smooth'
    });
}

// Add scroll to bottom button
const scrollButtonStyle = document.createElement('style');
scrollButtonStyle.textContent = `
    .scroll-to-bottom {
        position: absolute;
        bottom: 80px;
        right: 20px;
        background: var(--primary-dark);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.3s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 1000;
    }

    .scroll-to-bottom.visible {
        opacity: 1;
    }

    .scroll-to-bottom:hover {
        background: var(--primary);
    }

    .chat-messages {
        position: relative;
    }
`;
document.head.appendChild(scrollButtonStyle);

// Add scroll button to chat box
function addScrollButton() {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages.querySelector('.scroll-to-bottom')) {
        const scrollButton = document.createElement('div');
        scrollButton.className = 'scroll-to-bottom';
        scrollButton.innerHTML = '<i class="fas fa-chevron-down"></i>';
        scrollButton.onclick = () => scrollToBottom(chatMessages);
        chatMessages.appendChild(scrollButton);
    }
}

// Add scroll event listener to show/hide scroll button
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.addEventListener('scroll', function() {
            const scrollButton = this.querySelector('.scroll-to-bottom');
            if (scrollButton) {
                const isAtBottom = this.scrollHeight - this.scrollTop === this.clientHeight;
                scrollButton.classList.toggle('visible', !isAtBottom);
            }
        });
    }
});

// Add periodic message refresh
let messageRefreshInterval;
let chatUsersRefreshInterval;

function startMessageRefresh() {
    if (currentChatUser) {
        messageRefreshInterval = setInterval(() => {
            loadMessages(currentChatUser);
        }, 3000); // Reduced from 5000 to 3000 for more responsive updates
    }
}

function stopMessageRefresh() {
    if (messageRefreshInterval) {
        clearInterval(messageRefreshInterval);
    }
}

function startChatUsersRefresh() {
    if (isFullScreen) {
        chatUsersRefreshInterval = setInterval(() => {
            loadChatUsers();
        }, 5000); // Refresh users list every 5 seconds in fullscreen mode
    }
}

function stopChatUsersRefresh() {
    if (chatUsersRefreshInterval) {
        clearInterval(chatUsersRefreshInterval);
    }
}

// Add click handler to user items
document.addEventListener('click', function(e) {
    const userItem = e.target.closest('.user-item');
    if (userItem) {
        const userId = userItem.dataset.userId;
        const userName = userItem.querySelector('.user-name').textContent;
        const userAvatar = userItem.querySelector('.user-avatar').src;
        const isOnline = userItem.querySelector('.user-status i').classList.contains('online');
        
        openChat(userId, userName, userAvatar, isOnline);
    }
});

// Handle enter key in message input
document.getElementById('messageInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// Add styles for unread badge
const unreadStyle = document.createElement('style');
unreadStyle.textContent = `
    .unread-badge {
        background: var(--primary-dark);
        color: white;
        border-radius: 50%;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        font-size: 0.8em;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;
    }

    .user-header {
        position: relative;
    }
`;
document.head.appendChild(unreadStyle);

// Add periodic unread count refresh
let unreadRefreshInterval;

function startUnreadRefresh() {
    unreadRefreshInterval = setInterval(() => {
        loadUsers();
    }, 5000); // Reduced from 10000 to 5000 for more responsive updates
}

function stopUnreadRefresh() {
    if (unreadRefreshInterval) {
        clearInterval(unreadRefreshInterval);
    }
}

// Add styles for message badge
const messageBadgeStyle = document.createElement('style');
messageBadgeStyle.textContent = `
    .message-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #ff4444;
        color: white;
        border-radius: 50%;
        min-width: 20px;
        height: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        line-height: 1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        border: 2px solid white;
    }

    .messages-icon {
        position: relative;
    }
`;
document.head.appendChild(messageBadgeStyle);

// Add specific styles for notification badge
const notificationBadgeStyle = document.createElement('style');
notificationBadgeStyle.textContent = `
    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #ff4444;
        color: white;
        border-radius: 50%;
        min-width: 20px;
        height: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        line-height: 1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        border: 2px solid white;
        z-index: 10;
    }

    .notification-icon {
        position: relative;
    }
`;
document.head.appendChild(notificationBadgeStyle);

// Initialize message badge on page load
$(document).ready(function() {
    // Hide message badge initially
    $('.message-badge').hide();
    
    loadUsers();
    startUnreadRefresh();
    
    // Update last seen every minute
    setInterval(updateLastSeen, 60000);
    
    // Update last seen on user interaction
    $(document).on('mousemove keypress', function() {
        updateLastSeen();
    });
});

// Add styles for smooth transitions
const transitionStyle = document.createElement('style');
transitionStyle.textContent = `
    .chat-messages {
        transition: opacity 0.1s ease-in-out;
    }
    
    .user-item {
        transition: opacity 0.1s ease-in-out;
    }
    
    #usersList {
        transition: opacity 0.1s ease-in-out;
    }
`;
document.head.appendChild(transitionStyle);

// Add styles for voice recorder and file attachments
const voiceRecorderStyle = document.createElement('style');
voiceRecorderStyle.textContent = `
    .chat-input-actions {
        display: flex;
        gap: 5px;
        padding: 0 10px;
    }

    .chat-action-btn {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 5px;
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    .chat-action-btn:hover {
        opacity: 1;
    }

    .voice-recorder {
        position: absolute;
        bottom: 100%;
        left: 0;
        right: 0;
        background: white;
        padding: 15px;
        border-top: 1px solid #eee;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }

    .recorder-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .recorder-waveform {
        height: 60px;
        background: #f5f5f5;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .recorder-actions {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .recorder-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s;
    }

    .recorder-btn.cancel {
        background: #ff4444;
        color: white;
    }

    .recorder-btn.stop {
        background: var(--primary-dark);
        color: white;
    }

    .recorder-btn:hover {
        opacity: 0.9;
    }

    .message.voice-message {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .voice-message-player {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .voice-message-waveform {
        flex: 1;
        height: 30px;
        background: rgba(255,255,255,0.2);
        border-radius: 15px;
        overflow: hidden;
    }

    .voice-message-duration {
        font-size: 0.8em;
        opacity: 0.8;
    }

    .message.file-message {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .file-message-icon {
        font-size: 1.5em;
    }

    .file-message-info {
        flex: 1;
    }

    .file-message-name {
        font-weight: 500;
        margin-bottom: 2px;
    }

    .file-message-size {
        font-size: 0.8em;
        opacity: 0.8;
    }

    /* Emoji Picker Styles */
    .emoji-picker {
        position: absolute;
        bottom: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 10px;
        margin-bottom: 5px;
        z-index: 1000;
    }

    .emoji-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 5px;
    }

    .emoji-btn {
        background: none;
        border: none;
        font-size: 1.5em;
        padding: 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .emoji-btn:hover {
        background-color: #f0f0f0;
    }

    .chat-input {
        position: relative;
    }
`;
document.head.appendChild(voiceRecorderStyle);

// Voice recording functionality
let mediaRecorder = null;
let audioChunks = [];
let recordingStartTime = null;
let recordingTimer = null;

async function toggleVoiceRecorder() {
    const voiceRecorder = document.getElementById('voiceRecorder');
    
    if (voiceRecorder.style.display === 'none') {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];
            
            mediaRecorder.ondataavailable = (event) => {
                audioChunks.push(event.data);
            };
            
            mediaRecorder.start();
            recordingStartTime = Date.now();
            updateRecordingTime();
            recordingTimer = setInterval(updateRecordingTime, 1000);
            
            voiceRecorder.style.display = 'block';
        } catch (error) {
            console.error('Error accessing microphone:', error);
            alert('Could not access microphone. Please check your permissions.');
        }
    } else {
        stopRecording();
    }
}

function updateRecordingTime() {
    const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
    const minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
    const seconds = (elapsed % 60).toString().padStart(2, '0');
    document.getElementById('recordingTime').textContent = `${minutes}:${seconds}`;
}

function stopRecording() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
        clearInterval(recordingTimer);
        
        mediaRecorder.onstop = () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            sendVoiceMessage(audioBlob);
            document.getElementById('voiceRecorder').style.display = 'none';
        };
    }
}

function cancelRecording() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
        clearInterval(recordingTimer);
        document.getElementById('voiceRecorder').style.display = 'none';
    }
}

function sendVoiceMessage(audioBlob) {
    if (!currentChatUser) return;
    
    const formData = new FormData();
    formData.append('action', 'send_voice');
    formData.append('receiver_id', currentChatUser);
    formData.append('voice_message', audioBlob);
    
    $.ajax({
        url: 'chat_actions.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                loadMessages(currentChatUser);
                const chatMessages = document.getElementById('chatMessages');
                scrollToBottom(chatMessages);
            } else {
                alert('Error sending voice message: ' + response.message);
            }
        },
        error: function() {
            alert('Error sending voice message');
        }
    });
}

// File handling functionality
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (!currentChatUser) {
        alert('Please select a chat first');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'send_file');
    formData.append('receiver_id', currentChatUser);
    formData.append('file', file);
    
    $.ajax({
        url: 'chat_actions.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                loadMessages(currentChatUser);
                const chatMessages = document.getElementById('chatMessages');
                scrollToBottom(chatMessages);
            } else {
                alert('Error sending file: ' + response.message);
            }
        },
        error: function() {
            alert('Error sending file');
        }
    });
}

// Update formatMessageContent function to handle voice messages and image previews
function formatMessageContent(message) {
    switch (message.message_type) {
        case 'voice':
            return `
                <div class="voice-message-player">
                    <audio controls class="voice-message-audio">
                        <source src="${message.file_path}" type="audio/webm">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            `;
        case 'file':
            if (message.file_type && message.file_type.startsWith('image/')) {
                return `
                    <div class="file-message image-message">
                        <img src="${message.file_path}" alt="${message.file_name}" class="message-image" onclick="showImagePreview(this.src)">
                        <div class="file-message-info">
                            <div class="file-message-name">${message.file_name}</div>
                            <div class="file-message-size">${formatFileSize(message.file_size)}</div>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div class="file-message">
                        <i class="fas ${getFileIcon(message.file_type)} file-message-icon"></i>
                        <div class="file-message-info">
                            <div class="file-message-name">
                                <a href="${message.file_path}" target="_blank" download="${message.file_name}">
                                    ${message.file_name}
                                </a>
                            </div>
                            <div class="file-message-size">${formatFileSize(message.file_size)}</div>
                        </div>
                    </div>
                `;
            }
        default:
            return message.content || '';
    }
}

function getFileIcon(fileType) {
    if (!fileType) return 'fa-file';
    if (fileType.startsWith('image/')) return 'fa-image';
    if (fileType.startsWith('video/')) return 'fa-video';
    if (fileType.startsWith('audio/')) return 'fa-music';
    if (fileType.includes('pdf')) return 'fa-file-pdf';
    if (fileType.includes('word')) return 'fa-file-word';
    if (fileType.includes('excel') || fileType.includes('sheet')) return 'fa-file-excel';
    if (fileType.includes('powerpoint') || fileType.includes('presentation')) return 'fa-file-powerpoint';
    return 'fa-file';
}

function formatFileSize(bytes) {
    if (!bytes || bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function formatDuration(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.floor(seconds % 60);
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
}

// Add image preview functionality
function showImagePreview(src) {
    const modal = document.createElement('div');
    modal.className = 'image-preview-modal';
    modal.innerHTML = `
        <div class="image-preview-content">
            <img src="${src}" alt="Preview">
            <button class="close-preview" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    document.body.appendChild(modal);
}

// Add styles for image preview and audio player
const mediaStyles = document.createElement('style');
mediaStyles.textContent = `
    .voice-message-player {
        width: 100%;
        max-width: 300px;
    }

    .voice-message-audio {
        width: 100%;
        height: 36px;
        border-radius: 18px;
        background: rgba(255,255,255,0.1);
    }

    .message.sent .voice-message-audio {
        background: rgba(255,255,255,0.2);
    }

    .message.received .voice-message-audio {
        background: rgba(0,0,0,0.05);
    }

    .image-message {
        max-width: 300px;
    }

    .message-image {
        width: 100%;
        border-radius: 10px;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .message-image:hover {
        transform: scale(1.02);
    }

    .image-preview-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .image-preview-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
    }

    .image-preview-content img {
        max-width: 100%;
        max-height: 90vh;
        object-fit: contain;
    }

    .close-preview {
        position: absolute;
        top: -40px;
        right: 0;
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 10px;
    }

    .file-message a {
        color: inherit;
        text-decoration: none;
    }

    .file-message a:hover {
        text-decoration: underline;
    }

    .message.sent .file-message a {
        color: white;
    }
`;
document.head.appendChild(mediaStyles);

// Add styles for notifications footer
const notificationsFooterStyle = document.createElement('style');
notificationsFooterStyle.textContent = `
    .notifications-footer {
        padding: 10px 15px;
        border-top: 1px solid #eee;
        text-align: center;
    }

    .view-all-btn {
        display: block;
        padding: 8px;
        color: var(--primary-dark);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .view-all-btn:hover {
        color: var(--primary);
    }
`;
document.head.appendChild(notificationsFooterStyle);

// Add styles for profile info box
const profileInfoStyle = document.createElement('style');
profileInfoStyle.textContent = `
    .profile-info-box {
        padding: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 10px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .profile-info-box:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .profile-info-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .profile-info-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .profile-info-details {
        flex: 1;
        min-width: 0;
    }

    .profile-info-name {
        font-weight: 600;
        color: #fff;
        font-size: 0.95em;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-info-email {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.8em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
`;
document.head.appendChild(profileInfoStyle);

// Add styles for profile dropdown
const profileDropdownStyle = document.createElement('style');
profileDropdownStyle.textContent = `
    .profile-dropdown-header {
        padding: 15px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
    }

    .profile-dropdown-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .profile-dropdown-name {
        font-weight: 600;
        color: #333;
        font-size: 0.80em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-dropdown-email {
        color: #666;
        font-size: 0.70em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dropdown-divider {
        height: 1px;
        background-color: #eee;
        margin: 0;
    }

    .profile-dropdown-menu {
        min-width: 220px;
        padding: 0;
        margin-top: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .dropdown-item {
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333;
        text-decoration: none;
        transition: background-color 0.2s;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item i {
        width: 20px;
        color: #666;
    }
`;
document.head.appendChild(profileDropdownStyle);

// Add function to close all dropdowns
function closeAllDropdowns() {
    const dropdowns = document.querySelectorAll('.messages-dropdown, .notifications-dropdown, .profile-dropdown-menu');
    dropdowns.forEach(dropdown => {
        dropdown.classList.remove('active');
        dropdown.style.display = 'none';
    });
}

// Function to toggle messages
function toggleMessages(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const dropdown = document.querySelector('.messages-dropdown');
    closeAllDropdowns();
    dropdown.classList.toggle('active');
    dropdown.style.display = dropdown.classList.contains('active') ? 'block' : 'none';
}

// Function to toggle notifications
function toggleNotifications(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const dropdown = document.querySelector('.notifications-dropdown');
    closeAllDropdowns();
    dropdown.classList.toggle('active');
    dropdown.style.display = dropdown.classList.contains('active') ? 'block' : 'none';
}

// Function to toggle profile
function toggleProfile(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const dropdown = document.querySelector('.profile-dropdown-menu');
    closeAllDropdowns();
    dropdown.classList.toggle('active');
    dropdown.style.display = dropdown.classList.contains('active') ? 'block' : 'none';
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const isInsideDropdown = event.target.closest('.messages-icon, .notification-icon, .profile-dropdown, .dropdown-menu');
    if (!isInsideDropdown) {
        closeAllDropdowns();
    }
});

// Add styles for dropdown positioning
const dropdownStyle = document.createElement('style');
dropdownStyle.textContent = `
    .messages-dropdown, .profile-dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        min-width: 200px;
        z-index: 1000;
        margin-top: 10px;
    }

    .notifications-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        min-width: 350px;
        max-width: 400px;
        z-index: 1000;
        margin-top: 10px;
    }

    .messages-icon, .notification-icon, .profile-dropdown {
        position: relative;
    }

    .dropdown-menu.active {
        display: block !important;
    }
`;
document.head.appendChild(dropdownStyle);

function toggleFullScreen() {
    const chatBox = document.querySelector('.chat-box');
    const fullScreenBtn = chatBox.querySelector('.chat-action-btn[onclick="toggleFullScreen()"] i');
    
    isFullScreen = !isFullScreen;
    chatBox.classList.toggle('fullscreen', isFullScreen);
    
    if (isFullScreen) {
        fullScreenBtn.className = 'fas fa-compress';
        loadChatUsers();
        startChatUsersRefresh();
    } else {
        fullScreenBtn.className = 'fas fa-expand';
        stopChatUsersRefresh();
    }
}

function loadChatUsers() {
    $.ajax({
        url: 'get_users.php',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const chatUsersList = $('#chatUsersList');
                
                if (response.users.length === 0) {
                    chatUsersList.html('<div class="no-users">No users found</div>');
                } else {
                    // Get unread counts
                    $.ajax({
                        url: 'get_unread_count.php',
                        method: 'GET',
                        success: function(unreadResponse) {
                            if (unreadResponse.success) {
                                const unreadCounts = {};
                                
                                unreadResponse.unread_counts.forEach(count => {
                                    unreadCounts[count.sender_id] = count.unread_count;
                                });
                                
                                // Create a temporary container for new content
                                const tempContainer = $('<div>');
                                
                                response.users.forEach(user => {
                                    const unreadCount = unreadCounts[user.id] || 0;
                                    const isActive = currentChatUser == user.id;
                                    const userHtml = `
                                        <div class="chat-user-item ${isActive ? 'active' : ''}" 
                                             data-user-id="${user.id}" 
                                             onclick="switchChatUser(${user.id}, '${user.name}', 'uploads/profiles/${user.avatar}', ${user.is_online})">
                                            <img src="uploads/profiles/${user.avatar}" 
                                                 alt="${user.name}" 
                                                 class="chat-user-item-avatar">
                                            <div class="chat-user-item-info">
                                                <div class="chat-user-item-name">${user.name}</div>
                                                <div class="chat-user-item-status">
                                                    <i class="fas fa-circle ${user.is_online ? 'online' : 'offline'}"></i>
                                                    <span>${user.is_online ? 'Online' : 'Offline'}</span>
                                                    ${unreadCount > 0 ? `<span class="unread-count">(${unreadCount})</span>` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    tempContainer.append(userHtml);
                                });
                                
                                // Update the list
                                chatUsersList.html(tempContainer.html());
                            }
                        }
                    });
                }
            }
        },
        error: function() {
            console.error('Error loading chat users');
        }
    });
}

function switchChatUser(userId, userName, userAvatar, isOnline) {
    // Update current chat user
    currentChatUser = userId;
    
    // Update chat header
    const chatBox = document.querySelector('.chat-box');
    chatBox.querySelector('.chat-user-avatar').src = userAvatar;
    chatBox.querySelector('.chat-user-name').textContent = userName;
    chatBox.querySelector('.chat-user-status').textContent = isOnline ? 'Online' : 'Offline';
    chatBox.querySelector('.chat-user-status').style.color = isOnline ? '#4CAF50' : '#666';
    
    // Update active state in sidebar
    $('.chat-user-item').removeClass('active');
    $(`.chat-user-item[data-user-id="${userId}"]`).addClass('active');
    
    // Mark messages as read
    $.ajax({
        url: 'chat_actions.php',
        method: 'POST',
        data: {
            action: 'mark_read',
            sender_id: userId
        }
    });
    
    // Load messages
    loadMessages(userId);
    // Load pinned messages
    loadPinnedMessages(userId);
    
    // Reload users to update unread counts
    if (isFullScreen) {
        loadChatUsers();
    }
}

function openChat(userId, userName, userAvatar, isOnline) {
    stopMessageRefresh();
    currentChatUser = userId;
    const chatBox = document.querySelector('.chat-box');
    const chatContainer = document.getElementById('chatBoxContainer');
    
    // Update chat header
    chatBox.querySelector('.chat-user-avatar').src = userAvatar;
    chatBox.querySelector('.chat-user-name').textContent = userName;
    chatBox.querySelector('.chat-user-status').textContent = isOnline ? 'Online' : 'Offline';
    chatBox.querySelector('.chat-user-status').style.color = isOnline ? '#4CAF50' : '#666';
    
    // Show chat box
    chatContainer.style.display = 'block';
    chatBox.classList.remove('minimized');
    
    // Add scroll button
    addScrollButton();
    
    // Mark messages as read
    $.ajax({
        url: 'chat_actions.php',
        method: 'POST',
        data: {
            action: 'mark_read',
            sender_id: userId
        }
    });
    
    // Load messages and start refresh
    loadMessages(userId);
    // Load pinned messages
    loadPinnedMessages(userId);
    startMessageRefresh();
    
    // If in fullscreen mode, load users and update active state
    if (isFullScreen) {
        loadChatUsers();
    }
}

function minimizeChat() {
    const chatBox = document.querySelector('.chat-box');
    
    // If in fullscreen mode, exit fullscreen first
    if (isFullScreen) {
        toggleFullScreen();
    }
    
    chatBox.classList.toggle('minimized');
}

function closeChat() {
    stopMessageRefresh();
    stopChatUsersRefresh();
    const chatContainer = document.getElementById('chatBoxContainer');
    const chatBox = document.querySelector('.chat-box');
    
    // Reset fullscreen state
    if (isFullScreen) {
        isFullScreen = false;
        chatBox.classList.remove('fullscreen');
        const fullScreenBtn = chatBox.querySelector('.chat-action-btn[onclick="toggleFullScreen()"] i');
        fullScreenBtn.className = 'fas fa-expand';
    }
    
    // Clear reply state
    cancelReply();
    
    chatContainer.style.display = 'none';
    currentChatUser = null;
}

// Add styles for pinned messages section
const pinnedMessagesStyle = document.createElement('style');
pinnedMessagesStyle.textContent = `
    /* Pinned Messages Section Styles */
    .pinned-messages-section {
        background: rgba(255, 255, 255, 0.95);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    .pinned-messages-header {
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .pinned-messages-header:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    .pinned-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9em;
    }

    .pinned-info i {
        color: #ffd700;
        font-size: 0.8em;
    }

    .pinned-count {
        font-weight: 600;
        color: #ffd700;
    }

    .pinned-text {
        color: #333;
    }

    .pinned-actions {
        display: flex;
        align-items: center;
    }

    .pinned-actions i {
        color: #666;
        transition: transform 0.2s;
    }

    .pinned-messages-header.expanded .pinned-actions i {
        transform: rotate(180deg);
    }

    .pinned-messages-list {
        max-height: 200px;
        overflow-y: auto;
        background: rgba(0, 0, 0, 0.02);
    }

    .pinned-message-item {
        padding: 8px 15px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .pinned-message-item:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    .pinned-message-item:last-child {
        border-bottom: none;
    }

    .pinned-message-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .pinned-message-content {
        flex: 1;
        min-width: 0;
    }

    .pinned-message-sender {
        font-size: 0.8em;
        font-weight: 600;
        color: #ffd700;
        margin-bottom: 2px;
    }

    .pinned-message-text {
        font-size: 0.85em;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.3;
    }

    .pinned-message-time {
        font-size: 0.7em;
        color: #666;
        margin-top: 2px;
    }

    .pinned-message-actions {
        display: flex;
        gap: 5px;
        margin-top: 5px;
    }

    .pinned-message-action-btn {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 0.7em;
        transition: all 0.2s;
    }

    .pinned-message-action-btn:hover {
        background: rgba(0, 0, 0, 0.1);
        color: #333;
    }

    .pinned-message-action-btn.unpin-btn {
        color: #ff6b6b;
    }

    .pinned-message-action-btn.unpin-btn:hover {
        background: rgba(255, 107, 107, 0.2);
    }
`;
document.head.appendChild(pinnedMessagesStyle);

// Emoji picker functions
function toggleEmojiPicker() {
    const emojiPicker = document.getElementById('emojiPicker');
    const voiceRecorder = document.getElementById('voiceRecorder');
    
    // Hide voice recorder if it's open
    if (voiceRecorder.style.display !== 'none') {
        voiceRecorder.style.display = 'none';
    }
    
    // Toggle emoji picker
    if (emojiPicker.style.display === 'none') {
        emojiPicker.style.display = 'block';
    } else {
        emojiPicker.style.display = 'none';
    }
}

function addEmoji(emoji) {
    const messageInput = document.getElementById('messageInput');
    const cursorPos = messageInput.selectionStart;
    const textBefore = messageInput.value.substring(0, cursorPos);
    const textAfter = messageInput.value.substring(cursorPos);
    
    // Insert emoji at cursor position
    messageInput.value = textBefore + emoji + textAfter;
    
    // Set cursor position after the emoji
    const newCursorPos = cursorPos + emoji.length;
    messageInput.setSelectionRange(newCursorPos, newCursorPos);
    
    // Hide emoji picker
    document.getElementById('emojiPicker').style.display = 'none';
    
    // Focus back on the input
    messageInput.focus();
}

// Close emoji picker when clicking outside
document.addEventListener('click', function(event) {
    const emojiPicker = document.getElementById('emojiPicker');
    const emojiButton = event.target.closest('.chat-action-btn[onclick="toggleEmojiPicker()"]');
    
    if (emojiPicker && emojiPicker.style.display !== 'none' && !emojiButton && !event.target.closest('.emoji-picker')) {
        emojiPicker.style.display = 'none';
    }
});

// Add styles for automatic status display
const automaticStatusStyle = document.createElement('style');
automaticStatusStyle.textContent = `
    .status-display-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0;
    }

    .status-text {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
    }

    .status-text i {
        font-size: 0.7em;
        transition: color 0.3s ease;
    }

    .status-text span {
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .status-info {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .status-info small {
        opacity: 0.7;
        font-style: italic;
    }

    .dropdown-item .status-display-container {
        width: 100%;
    }

    .dropdown-item .status-display-container:hover .status-text i {
        transform: scale(1.1);
    }
`;
document.head.appendChild(automaticStatusStyle);
</script>
