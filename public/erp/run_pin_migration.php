<?php
require_once 'controlls/db/functions.php';

echo "Starting pin functionality migration...\n";

try {
    // Read the SQL file
    $sql = file_get_contents('sql/add_pin_to_messages.sql');
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            echo "Executing: " . substr($statement, 0, 50) . "...\n";
            $result = $conn->exec($statement);
            if ($result !== false) {
                echo "✓ Success\n";
            } else {
                echo "✗ Failed\n";
            }
        }
    }
    
    echo "\nMigration completed successfully!\n";
    echo "Pin functionality has been added to the messages table.\n";
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?> 