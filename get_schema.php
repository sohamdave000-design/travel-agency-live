<?php
require_once 'config/database.php';

function print_schema($pdo, $table) {
    echo "<h3>Schema for $table</h3>";
    try {
        $stmt = $pdo->query("DESC $table");
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while($row = $stmt->fetch()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td><td>{$row['Extra']}</td></tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "Error getting schema for $table: " . $e->getMessage();
    }
}

print_schema($pdo, 'bookings');
print_schema($pdo, 'payments');
?>
