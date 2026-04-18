<?php
require_once 'config/database.php';
$q = $pdo->query("SELECT name, image FROM rentals WHERE name LIKE '%Fortuner%'");
while($r = $q->fetch()) {
    echo $r['name'] . " : " . $r['image'] . "\n";
}
?>
