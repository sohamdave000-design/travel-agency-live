<?php
require_once 'config/database.php';
$user = $pdo->query("SELECT * FROM users LIMIT 1")->fetch();
if ($user) {
    echo "User: " . $user['email'] . "<br>";
    // We'll reset the password for testing (password is 'password')
    $pwd = password_hash('password', PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$pwd, $user['id']]);
    echo "Password reset to 'password' for testing.";
} else {
    // Create one
    $pwd = password_hash('password', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (name, email, password) VALUES ('Test User', 'test@example.com', ?)")->execute([$pwd]);
    echo "Created user 'test@example.com' with password 'password'.";
}
?>
