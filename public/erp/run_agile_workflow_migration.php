<?php
require_once 'controlls/db/functions.php';

try {
    // Read and execute the SQL file
    $sql = file_get_contents('sql/create_agile_workflow_tables.sql');
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $conn->exec($statement);
            echo "Executed: " . substr($statement, 0, 50) . "...\n";
        }
    }
    
    echo "\n✅ Agile Workflow tables created successfully!\n";
    echo "Tables created:\n";
    echo "- sprints\n";
    echo "- sprint_phases\n";
    echo "- sprint_tasks\n";
    echo "- sprint_meetings\n";
    echo "- sprint_metrics\n";
    
} catch (PDOException $e) {
    echo "❌ Error creating Agile Workflow tables: " . $e->getMessage() . "\n";
}
?> 