<?php
/**
 * Auto Offline Users Script
 * This script should be run periodically (e.g., via cron job) to automatically
 * set users offline who have been inactive for more than the threshold time
 */

require_once 'controlls/db/functions.php';

try {
    // Set users offline who haven't been seen for more than 10 minutes
    $offline_threshold = 1; // minutes
    
    $stmt = $conn->prepare("
        UPDATE users 
        SET online_status = 0 
        WHERE online_status = 1 
        AND last_seen < DATE_SUB(NOW(), INTERVAL :threshold MINUTE)
    ");
    
    $result = $stmt->execute(['threshold' => $offline_threshold]);
    
    if ($result) {
        $affected_rows = $stmt->rowCount();
        echo "Successfully set $affected_rows users offline due to inactivity\n";
        
        // Log the action
        error_log("Auto-offline script: Set $affected_rows users offline at " . date('Y-m-d H:i:s'));
    } else {
        throw new Exception('Failed to update user statuses');
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    error_log("Auto-offline script error: " . $e->getMessage());
}
?> 