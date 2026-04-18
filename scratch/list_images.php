<?php
require_once 'config/database.php';
$q = $pdo->query("SELECT image FROM rentals");
while($r = $q->fetch()) {
    echo $r['image'] . "\n";
}
?>
