<?php
require_once 'controlls/db/functions.php';

try {
    // Read and execute the SQL migration
    $sql = file_get_contents('sql/add_label_to_tasks.sql');
    
    // Split the SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $conn->exec($statement);
            echo "Executed: " . substr($statement, 0, 50) . "...\n";
        }
    }
    
    echo "\n✅ Label column migration completed successfully!\n";
    echo "The 'label' column has been added to the tasks table.\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
}
?> 