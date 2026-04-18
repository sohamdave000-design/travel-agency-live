<?php
require_once 'config/database.php';

$stmt = $pdo->query("SELECT id, name, image FROM packages");
$mangled = 'ðŸ“‹';
$found = [];
while($row = $stmt->fetch()) {
    if (strpos($row['name'], $mangled) !== false || strpos($row['image'], $mangled) !== false) {
        $found[] = $row;
    }
}

if (empty($found)) {
    echo "No packages found with mangled symbol: $mangled";
} else {
    echo "<h3>Found Mangled Packages</h3><table border='1'>";
    foreach($found as $f) {
        echo "<tr><td>{$f['id']}</td><td>" . htmlspecialchars($f['name']) . "</td><td>" . htmlspecialchars($f['image']) . "</td></tr>";
    }
    echo "</table>";
}
?>
