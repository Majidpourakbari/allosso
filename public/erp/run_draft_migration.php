<?php
require_once 'controlls/db/functions.php';

echo "Starting draft status migration...\n";

try {
    // Add draft status to meetings table
    echo "Adding is_draft column to meetings table...\n";
    $conn->exec("ALTER TABLE `meetings` ADD COLUMN `is_draft` BOOLEAN DEFAULT FALSE AFTER `created_at`");
    $conn->exec("ALTER TABLE `meetings` ADD INDEX `idx_is_draft` (`is_draft`)");
    echo "✅ is_draft column added to meetings table successfully\n";

    // Add draft status to voting_polls table
    echo "Adding is_draft column to voting_polls table...\n";
    $conn->exec("ALTER TABLE `voting_polls` ADD COLUMN `is_draft` BOOLEAN DEFAULT FALSE AFTER `created_at`");
    $conn->exec("ALTER TABLE `voting_polls` ADD INDEX `idx_is_draft` (`is_draft`)");
    echo "✅ is_draft column added to voting_polls table successfully\n";

    // Update existing records to be non-draft
    echo "Updating existing records to be non-draft...\n";
    $conn->exec("UPDATE `meetings` SET `is_draft` = FALSE WHERE `is_draft` IS NULL");
    $conn->exec("UPDATE `voting_polls` SET `is_draft` = FALSE WHERE `is_draft` IS NULL");
    echo "✅ Existing records updated successfully\n";

    echo "\n🎉 Draft status migration completed successfully!\n";
    echo "The following changes have been made:\n";
    echo "- Added is_draft column to meetings table\n";
    echo "- Added is_draft column to voting_polls table\n";
    echo "- Added indexes for better performance\n";
    echo "- Updated existing records to be non-draft (sent)\n";

} catch (Exception $e) {
    echo "❌ Error during migration: " . $e->getMessage() . "\n";
}
?> 