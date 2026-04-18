<?php
require_once dirname(__DIR__) . '/config/database.php';
$packages = $pdo->query("SELECT id, name, destination, latitude, longitude FROM packages")->fetchAll();
foreach ($packages as $pkg) {
    if (empty($pkg['latitude']) || empty($pkg['longitude'])) {
        echo "MISSING: ID " . $pkg['id'] . " - " . $pkg['name'] . " (" . $pkg['destination'] . ")\n";
    } else {
        echo "OK: ID " . $pkg['id'] . " - " . $pkg['name'] . " (" . $pkg['latitude'] . ", " . $pkg['longitude'] . ")\n";
    }
}
?>
