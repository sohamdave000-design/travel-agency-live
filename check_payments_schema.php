<?php
require_once 'config/database.php';
$q = $pdo->query("DESCRIBE payments");
while($row = $q->fetch()) {
    echo $row['Field'] . "\n";
}
?>
