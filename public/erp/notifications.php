<?php include 'views/headin2.php' ?>

<div class="dashboard-container">
    <?php include 'views/header-sidebar.php' ?>
    
    <div class="content-wrapper">
        <div class="notifications-page">
            <div class="notifications-header">
                <h2>Notifications</h2>
                <button class="btn btn-primary" onclick="markAllAsRead()">
                    <i class="fas fa-check-double"></i> Mark All as Read
                </button>
            </div>

            <div class="notifications-list">
                <?php
                try {
                    $readdb = $conn->query("SELECT n.*, u.avatar, u.name as user_name 
                                         FROM notifications n 
                                         LEFT JOIN users u ON n.user_id = u.id 
                                         WHERE n.receiver_ids LIKE '%{$my_profile_id}%'
                                         ORDER BY n.date DESC, n.time DESC");
                    $read_db = $readdb->fetchAll(PDO::FETCH_OBJ);
                } catch(Exception $e) {
                    echo $e->getMessage();
                }
                ?>

                <?php if(empty($read_db)): ?>
                    <div class="no-notifications">
                        <i class="fas fa-bell-slash"></i>
                        <p>No notifications yet</p>
                    </div>
                <?php else: ?>
                    <?php foreach($read_db as $notification): ?>
                        <?php 
                        $isRead = false;
                        if ($notification->users_read) {
                            $readUsers = explode(',', $notification->users_read);
                            $isRead = in_array($my_profile_id, $readUsers);
                        }
                        ?>
                        <div class="notification-item <?php echo $isRead ? 'read' : 'unread'; ?>" 
                             data-id="<?php echo $notification->id; ?>">
                            <div class="notification-content">
                                <div class="notification-header">
                                    <img src="uploads/profiles/<?php echo $notification->avatar ?: 'default-avatar.png'; ?>" 
                                         alt="<?php echo htmlspecialchars($notification->user_name); ?>" 
                                         class="notification-avatar">
                                    <div class="notification-info">
                                        <div class="notification-message">
                                            <?php echo htmlspecialchars($notification->message); ?>
                                        </div>
                                        <div class="notification-meta">
                                            <span class="notification-time">
                                                <?php echo date('M d, Y H:i', strtotime($notification->date . ' ' . $notification->time)); ?>
                                            </span>
                                            <?php if(!$isRead): ?>
                                                <button class="mark-read-btn" onclick="markAsRead(<?php echo $notification->id; ?>)">
                                                    <i class="fas fa-check"></i> Mark as read
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.notifications-page {
    padding: 20px;
    max-width: 800px;
    margin: 0 auto;
}

.notifications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.notifications-header h2 {
    margin: 0;
    color: var(--primary-dark);
}

.notifications-list {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.notification-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item.unread {
    background-color: #f0f7ff;
}

.notification-header {
    display: flex;
    gap: 15px;
}

.notification-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.notification-info {
    flex: 1;
}

.notification-message {
    margin-bottom: 5px;
    color: #333;
}

.notification-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9em;
}

.notification-time {
    color: #666;
}

.mark-read-btn {
    background: none;
    border: none;
    color: var(--primary-dark);
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.mark-read-btn:hover {
    background-color: rgba(0,0,0,0.05);
}

.no-notifications {
    text-align: center;
    padding: 40px;
    color: #666;
}

.no-notifications i {
    font-size: 48px;
    margin-bottom: 10px;
    color: #ccc;
}

.btn-primary {
    background-color: var(--primary-dark);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.2s;
}

.btn-primary:hover {
    background-color: var(--primary);
}
</style>

<script>
function markAsRead(notificationId) {
    $.ajax({
        url: 'mark_notifications_read.php',
        method: 'POST',
        data: JSON.stringify({ 
            notification_id: notificationId,
            my_profile_id: <?php echo $my_profile_id; ?>
        }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                // Update the notification item UI
                const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.classList.remove('unread');
                    notificationItem.classList.add('read');
                    const markReadBtn = notificationItem.querySelector('.mark-read-btn');
                    if (markReadBtn) {
                        markReadBtn.remove();
                    }
                }
                
                // Update the notification counter in header
                updateNotificationCounter();
            }
        }
    });
}

function markAllAsRead() {
    $.ajax({
        url: 'mark_notifications_read.php',
        method: 'POST',
        data: JSON.stringify({ 
            my_profile_id: <?php echo $my_profile_id; ?>
        }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                // Update all notification items UI
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    item.classList.add('read');
                    const markReadBtn = item.querySelector('.mark-read-btn');
                    if (markReadBtn) {
                        markReadBtn.remove();
                    }
                });
                
                // Update the notification counter in header
                updateNotificationCounter();
            }
        }
    });
}

function updateNotificationCounter() {
    $.ajax({
        url: 'get_notifications.php',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                // Update notification badge
                const badge = $('#notificationBadge');
                if (response.unread_count > 0) {
                    badge.text(response.unread_count > 99 ? '99+' : response.unread_count);
                    badge.show();
                } else {
                    badge.hide();
                }
            }
        }
    });
}

// Update counter when page loads
$(document).ready(function() {
    updateNotificationCounter();
});
</script>

<?php include 'views/footer-dashboard.php' ?>
