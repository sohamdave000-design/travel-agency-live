<?php
require_once '../config/database.php';

if (isset($_SESSION['admin_id'])) {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
}

redirect('login.html');
exit();
?>

