<?php
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    session_destroy();
}

header("Location: login.html");
exit();
?>

