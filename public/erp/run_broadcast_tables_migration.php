<?php
require_once 'controlls/db/functions.php';

echo "Starting broadcast tables migration...\n";

try {
    // Create meetings table
    echo "Creating meetings table...\n";
    $meetings_sql = file_get_contents('sql/create_meetings_table.sql');
    $conn->exec($meetings_sql);
    echo "✅ meetings table created successfully\n";

    // Create voting_polls table
    echo "Creating voting_polls table...\n";
    $voting_polls_sql = file_get_contents('sql/create_voting_polls_table.sql');
    $conn->exec($voting_polls_sql);
    echo "✅ voting_polls table created successfully\n";

    // Create voting_options table
    echo "Creating voting_options table...\n";
    $voting_options_sql = file_get_contents('sql/create_voting_options_table.sql');
    $conn->exec($voting_options_sql);
    echo "✅ voting_options table created successfully\n";

    // Create meeting_responses table
    echo "Creating meeting_responses table...\n";
    $meeting_responses_sql = file_get_contents('sql/create_meeting_responses_table.sql');
    $conn->exec($meeting_responses_sql);
    echo "✅ meeting_responses table created successfully\n";

    // Create voting_responses table
    echo "Creating voting_responses table...\n";
    $voting_responses_sql = file_get_contents('sql/create_voting_responses_table.sql');
    $conn->exec($voting_responses_sql);
    echo "✅ voting_responses table created successfully\n";

    echo "\n🎉 Migration completed successfully!\n";
    echo "The following tables have been created:\n";
    echo "- meetings (for storing meeting information)\n";
    echo "- voting_polls (for storing voting poll information)\n";
    echo "- voting_options (for storing voting poll options)\n";
    echo "- meeting_responses (for storing meeting accept/decline responses)\n";
    echo "- voting_responses (for storing voting poll responses)\n";

} catch (Exception $e) {
    echo "❌ Error during migration: " . $e->getMessage() . "\n";
}
?> 