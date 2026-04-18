<?php
require_once 'config/database.php';

if (isLoggedIn()) {
    redirect('dashboard.html');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $email;
            redirect('dashboard.html');
        } else {
            $error = "Invalid email or password.";
        }
    }
}

include 'includes/header.php';
?>

<div class="form-container">
    <h2>Login to Account</h2>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">Login</button>
    </form>
    
    <div class="text-center" style="margin-top: 1.5rem;">
        <p>Don't have an account? <a href="register.html" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">Register here</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

