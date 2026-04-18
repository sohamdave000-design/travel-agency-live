<?php
require_once 'config/database.php';

$stmt = $pdo->query("SELECT id, name, image FROM packages");
$found = [];
while($row = $stmt->fetch()) {
    // Check for characters outside of standard ASCII range
    if (preg_match('/[^\x20-\x7E]/', $row['name']) || preg_match('/[^\x20-\x7E]/', $row['image'])) {
        $found[] = $row;
    }
}

if (empty($found)) {
    echo "No packages found with special/mangled characters.";
} else {
    echo "<h3>Found Packages with Special Characters</h3><table border='1'>";
    foreach($found as $f) {
        echo "<tr><td>{$f['id']}</td><td>" . htmlspecialchars($f['name']) . "</td><td>" . htmlspecialchars($f['image']) . "</td></tr>";
    }
    echo "</table>";
}
?>
