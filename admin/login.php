<?php
require_once '../config/database.php';

if (isAdmin()) {
    redirect('index.html');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT id, password FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $username;
            redirect('index.html');
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Travel Agency</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #1e293b; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .admin-login { background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 400px; text-align: center; }
        .admin-login h2 { color: #0f172a; margin-bottom: 2rem; }
        .admin-login .form-group { text-align: left; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="admin-login">
        <h2>Admin Portal</h2>
        
        <?php if($error): ?>
            <div class="alert alert-danger" style="text-align: left;"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%; padding: 0.75rem;">Login as Admin</button>
        </form>
        <p style="margin-top: 1.5rem; font-size: 0.9rem;"><a href="../index.html" style="color: #64748b; text-decoration: none;">&larr; Back to Website</a></p>
    </div>
</body>
</html>

