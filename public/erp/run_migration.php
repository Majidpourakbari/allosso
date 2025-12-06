<?php
require_once 'controlls/db/functions.php';

echo "Running database migration...\n";

try {
    // Add parent_id column
    $stmt = $conn->prepare("ALTER TABLE tasks_checklists ADD COLUMN parent_id INT NULL DEFAULT NULL AFTER id");
    $stmt->execute();
    echo "✓ Added parent_id column\n";
    
    // Add foreign key constraint
    $stmt = $conn->prepare("ALTER TABLE tasks_checklists ADD CONSTRAINT fk_checklist_parent 
                           FOREIGN KEY (parent_id) REFERENCES tasks_checklists(id) ON DELETE CASCADE");
    $stmt->execute();
    echo "✓ Added foreign key constraint\n";
    
    // Add index
    $stmt = $conn->prepare("CREATE INDEX idx_checklist_parent_id ON tasks_checklists(parent_id)");
    $stmt->execute();
    echo "✓ Added index for parent_id\n";
    
    echo "\nMigration completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // Check if column already exists
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists, skipping...\n";
    }
}
?> 