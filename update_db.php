<?php
require_once 'config.php';

$conn = getDBConnection();

// Read the SQL file
$sql = file_get_contents('db_setup.sql');

// Split into individual statements, but be careful with semicolons in strings
$statements = [];
$currentStatement = '';
$inString = false;
$stringChar = '';

for ($i = 0; $i < strlen($sql); $i++) {
    $char = $sql[$i];

    if (!$inString && ($char === '"' || $char === "'")) {
        $inString = true;
        $stringChar = $char;
    } elseif ($inString && $char === $stringChar && $sql[$i-1] !== '\\') {
        $inString = false;
        $stringChar = '';
    }

    if (!$inString && $char === ';') {
        $statement = trim($currentStatement);
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            $statements[] = $statement;
        }
        $currentStatement = '';
    } else {
        $currentStatement .= $char;
    }
}

// Execute each statement
foreach ($statements as $statement) {
    echo "Executing: " . substr($statement, 0, 50) . "...\n";
    if ($conn->query($statement) === TRUE) {
        echo "✓ Success\n";
    } else {
        echo "✗ Error: " . $conn->error . "\n";
    }
}

$conn->close();
echo "\nDatabase setup completed!\n";
?>