<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

// Handle database switch
if (isset($_POST['switch_db'])) {
    $dbType = $_POST['db_type'] ?? DEFAULT_DB_TYPE;
    $db = Database::getInstance();
    $db->switchDatabase($dbType);
    header('Location: index.php');
    exit;
}

$db = Database::getInstance();
$currentDb = $db->getCurrentDbType();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo APP_NAME; ?>
    </title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="auth-container">
        <div class="auth-card card">
            <div class="logo text-center mb-4">
                <?php echo APP_NAME; ?>
            </div>

            <h1 class="auth-title">Welcome to SQL Injection Lab</h1>
            <p class="auth-subtitle">Multi-Database Security Testing Environment</p>

            <div class="db-selector mb-4" style="justify-content: center;">
                <span style="color: var(--text-muted);">Current Database:</span>
                <span class="db-badge <?php echo $currentDb; ?>">
                    <?php echo strtoupper($currentDb); ?>
                </span>
            </div>

            <form method="POST" class="mb-4">
                <div class="form-group">
                    <label class="form-label">Switch Database</label>
                    <select name="db_type" class="form-control" onchange="this.form.submit()">
                        <option value="mysql" <?php echo $currentDb === 'mysql' ? 'selected' : ''; ?>>MySQL</option>
                        <option value="postgresql" <?php echo $currentDb === 'postgresql' ? 'selected' : ''; ?>>PostgreSQL
                        </option>
                        <option value="mssql" <?php echo $currentDb === 'mssql' ? 'selected' : ''; ?>>Microsoft SQL Server
                        </option>
                        <option value="sqlite" <?php echo $currentDb === 'sqlite' ? 'selected' : ''; ?>>SQLite</option>
                        <option value="oracle" <?php echo $currentDb === 'oracle' ? 'selected' : ''; ?>>Oracle</option>
                    </select>
                </div>
                <input type="hidden" name="switch_db" value="1">
            </form>

            <div class="flex gap-2">
                <a href="login.php" class="btn btn-primary" style="flex: 1;">Login</a>
                <a href="register.php" class="btn btn-secondary" style="flex: 1;">Register</a>
            </div>

            <div class="alert alert-warning mt-4">
                <strong>⚠️ Warning:</strong> This is a security testing lab. The search functionality contains
                intentional SQL injection vulnerabilities for educational purposes.
            </div>
        </div>
    </div>
</body>

</html>