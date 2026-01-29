<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

requireLogin();

$db = Database::getInstance();
$currentDb = $db->getCurrentDbType();
$username = getUsername();

$searchTerm = '';
$results = [];
$searched = false;

if (isset($_GET['q'])) {
    $searchTerm = $_GET['q'];
    $searched = true;

    // VULNERABLE: Using the intentionally vulnerable search function
    $results = searchItems($searchTerm);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search -
        <?php echo APP_NAME; ?>
    </title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <?php echo APP_NAME; ?>
            </div>
            <div class="nav">
                <span style="color: var(--text-muted);">Welcome, <strong>
                        <?php echo e($username); ?>
                    </strong></span>
                <a href="dashboard.php" class="btn btn-secondary btn-sm">← Dashboard</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>

        <div class="card">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Search Items</h1>
                <div class="db-selector">
                    <span style="color: var(--text-muted);">Database:</span>
                    <span class="db-badge <?php echo $currentDb; ?>">
                        <?php echo strtoupper($currentDb); ?>
                    </span>
                </div>
            </div>

            <div class="alert alert-warning">
                <strong>⚠️ SQL Injection Testing Area</strong><br>
                This search is intentionally vulnerable to SQL injection. Try payloads like:<br>
                <code>' OR '1'='1</code> (MySQL/SQLite)<br>
                <code>' OR '1'='1' --</code> (PostgreSQL/Oracle)<br>
                <code>' OR 1=1 --</code> (MSSQL)
            </div>

            <div class="search-container">
                <form method="GET" class="search-form">
                    <input type="text" name="q" class="form-control search-input"
                        placeholder="Search items by title or description..." value="<?php echo e($searchTerm); ?>"
                        autofocus>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>

            <?php if ($searched): ?>
                <div class="mt-4">
                    <h3 style="margin-bottom: 1rem;">
                        Search Results
                        <?php if (!empty($searchTerm)): ?>
                            for "
                            <?php echo e($searchTerm); ?>"
                        <?php endif; ?>
                        <span style="color: var(--text-muted); font-size: 1rem; font-weight: normal;">
                            (
                            <?php echo count($results); ?> found)
                        </span>
                    </h3>

                    <?php if (empty($results)): ?>
                        <div class="text-center" style="padding: 3rem; color: var(--text-muted);">
                            <h3>No results found</h3>
                            <p>Try a different search term or SQL injection payload</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>User ID</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $item): ?>
                                        <tr>
                                            <td>
                                                <?php echo e($item['id'] ?? 'N/A'); ?>
                                            </td>
                                            <td><strong>
                                                    <?php echo e($item['title'] ?? 'N/A'); ?>
                                                </strong></td>
                                            <td>
                                                <?php echo e($item['description'] ?? 'No description'); ?>
                                            </td>
                                            <td>
                                                <?php echo e($item['user_id'] ?? 'N/A'); ?>
                                            </td>
                                            <td>
                                                <?php echo isset($item['created_at']) ? date('M d, Y H:i', strtotime($item['created_at'])) : 'N/A'; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-info mt-4">
                <strong>💡 SQL Injection Examples by Database:</strong><br><br>

                <strong>MySQL:</strong><br>
                <code>' UNION SELECT id, username, password, email, created_at FROM users --</code><br><br>

                <strong>PostgreSQL:</strong><br>
                <code>' UNION SELECT id::text, username, password, email::text, created_at::text FROM users --</code><br><br>

                <strong>MSSQL:</strong><br>
                <code>' UNION SELECT CAST(id AS NVARCHAR), username, password, email, CAST(created_at AS NVARCHAR) FROM users --</code><br><br>

                <strong>Oracle:</strong><br>
                <code>' UNION SELECT TO_CHAR(id), username, password, email, TO_CHAR(created_at) FROM users --</code><br><br>

                <strong>SQLite:</strong><br>
                <code>' UNION SELECT id, username, password, email, created_at FROM users --</code>
            </div>
        </div>
    </div>
</body>

</html>