<?php
require_once 'config/database.php';

function check_table($pdo, $table, $columns) {
    echo "<h3>Checking $table</h3>";
    $cols = implode(", ", $columns);
    try {
        $stmt = $pdo->query("SELECT id, $cols FROM $table LIMIT 10");
        echo "<table border='1'><tr><th>ID</th>";
        foreach($columns as $c) echo "<th>$c</th>";
        echo "</tr>";
        while($row = $stmt->fetch()) {
            echo "<tr><td>{$row['id']}</td>";
            foreach($columns as $c) echo "<td>" . htmlspecialchars($row[$c]) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "Error checking table $table: " . $e->getMessage();
    }
}

// Check with correct columns
check_table($pdo, 'packages', ['name', 'image']);
check_table($pdo, 'hotels', ['name', 'image']);
check_table($pdo, 'buses', ['bus_name', 'image']);
?>