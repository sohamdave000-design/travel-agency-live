<?php
require_once 'config/database.php';

if (isLoggedIn()) {
    redirect('dashboard.html');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $phone, $hashed_password])) {
                $success = "Registration successful! You can now login.";
                // Clear post variables
                $name = $email = $phone = '';
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="form-container">
    <h2>Create Account</h2>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <div class="text-center mt-4">
            <a href="login.html" class="btn-primary">Go to Login</a>
        </div>
    <?php else: ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo isset($name)?$name:''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" value="<?php echo isset($email)?$email:''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?php echo isset($phone)?$phone:''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">Register</button>
    </form>
    
    <div class="text-center" style="margin-top: 1.5rem;">
        <p>Already have an account? <a href="login.html" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">Login here</a></p>
    </div>
    
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

