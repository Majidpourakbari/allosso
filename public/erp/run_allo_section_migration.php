<?php
require_once 'controlls/db/functions.php';

echo "Running Allo Section migration...\n";

try {
    // Check if column already exists
    $checkStmt = $conn->prepare("SHOW COLUMNS FROM tasks LIKE 'allo_section'");
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        echo "✓ allo_section column already exists\n";
    } else {
        // Add allo_section column
        $stmt = $conn->prepare("ALTER TABLE tasks ADD COLUMN allo_section VARCHAR(50) NULL DEFAULT NULL AFTER category");
        $stmt->execute();
        echo "✓ Added allo_section column\n";
        
        // Add index for better performance on allo_section queries
        $stmt = $conn->prepare("CREATE INDEX idx_tasks_allo_section ON tasks(allo_section)");
        $stmt->execute();
        echo "✓ Added index for allo_section\n";
    }
    
    echo "\nMigration completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // Check if column already exists
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists, skipping...\n";
    }
}
?> 