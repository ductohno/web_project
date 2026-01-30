<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        if (loginUser($username, $password)) {
            redirect('dashboard.php');
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login -
        <?php echo APP_NAME; ?>
    </title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="auth-container">
        <div class="auth-card card">
            <div class="logo text-center mb-4">
                <?php echo APP_NAME; ?>
            </div>

            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Login to your account</p>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo e($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter your username" required
                        autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password"
                        required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>

            <p class="text-center mt-3" style="color: var(--text-muted);">
                Don't have an account? <a href="register.php" class="auth-link">Register here</a>
            </p>

            <p class="text-center mt-2" style="color: var(--text-muted);">
                <a href="index.php" class="auth-link">← Back to home</a>
            </p>

            <div class="alert alert-info mt-4">
                <strong>🔑 Default credentials:</strong><br>
                Username: <code>admin</code> or <code>testuser</code><br>
                Password: <code>password</code>
            </div>
        </div>
    </div>
</body>

</html>