<?php
require_once 'config/database.php';

function check_recent($pdo, $table, $columns) {
    echo "<h3>Checking Recent $table</h3>";
    $cols = implode(", ", $columns);
    $stmt = $pdo->query("SELECT id, $cols FROM $table ORDER BY id DESC LIMIT 10");
    echo "<table border='1'><tr><th>ID</th>";
    foreach($columns as $c) echo "<th>$c</th>";
    echo "</tr>";
    while($row = $stmt->fetch()) {
        echo "<tr><td>{$row['id']}</td>";
        foreach($columns as $c) echo "<td>" . htmlspecialchars($row[$c]) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

check_recent($pdo, 'packages', ['name', 'image']);
?>
