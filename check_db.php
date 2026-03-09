<?php
require_once 'config/db.php';
$result = $conn->query('SELECT id, document_name FROM doc_types ORDER BY id');
while($row = $result->fetch_assoc()) {
    echo $row['id'] . ' | ' . $row['document_name'] . "\n";
}
echo "Total: " . $result->num_rows . "\n";

// Also check if category column exists
$cols = $conn->query("SHOW COLUMNS FROM doc_types");
echo "\n--- doc_types columns ---\n";
while($col = $cols->fetch_assoc()) {
    echo $col['Field'] . ' | ' . $col['Type'] . "\n";
}
