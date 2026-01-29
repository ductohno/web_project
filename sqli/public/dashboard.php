<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

requireLogin();

$db = Database::getInstance();
$currentDb = $db->getCurrentDbType();
$userId = getUserId();
$username = getUsername();

// Handle database switch
if (isset($_POST['switch_db'])) {
    $dbType = $_POST['db_type'] ?? DEFAULT_DB_TYPE;
    $db->switchDatabase($dbType);
    redirect('dashboard.php');
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_item'])) {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!empty($title)) {
            if (createItem($userId, $title, $description)) {
                setFlashMessage('Item created successfully!', 'success');
            } else {
                setFlashMessage('Failed to create item', 'danger');
            }
        }
        redirect('dashboard.php');
    }

    if (isset($_POST['update_item'])) {
        $itemId = $_POST['item_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!empty($title)) {
            if (updateItem($itemId, $userId, $title, $description)) {
                setFlashMessage('Item updated successfully!', 'success');
            } else {
                setFlashMessage('Failed to update item', 'danger');
            }
        }
        redirect('dashboard.php');
    }

    if (isset($_POST['delete_item'])) {
        $itemId = $_POST['item_id'] ?? 0;

        if (deleteItem($itemId, $userId)) {
            setFlashMessage('Item deleted successfully!', 'success');
        } else {
            setFlashMessage('Failed to delete item', 'danger');
        }
        redirect('dashboard.php');
    }
}

// Get user's items
$items = getUserItems($userId);
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard -
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
                <a href="search.php" class="btn btn-secondary btn-sm">🔍 Search</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="dashboard-header">
                <h1 class="dashboard-title">My Items</h1>

                <div class="flex gap-2">
                    <div class="db-selector">
                        <span style="color: var(--text-muted);">Database:</span>
                        <span class="db-badge <?php echo $currentDb; ?>">
                            <?php echo strtoupper($currentDb); ?>
                        </span>
                        <form method="POST" style="display: inline;">
                            <select name="db_type" class="form-control" style="width: auto; padding: 0.5rem;"
                                onchange="this.form.submit()">
                                <option value="mysql" <?php echo $currentDb === 'mysql' ? 'selected' : ''; ?>>MySQL
                                </option>
                                <option value="postgresql" <?php echo $currentDb === 'postgresql' ? 'selected' : ''; ?>>
                                    PostgreSQL</option>
                                <option value="mssql" <?php echo $currentDb === 'mssql' ? 'selected' : ''; ?>>MSSQL
                                </option>
                                <option value="sqlite" <?php echo $currentDb === 'sqlite' ? 'selected' : ''; ?>>SQLite
                                </option>
                            </select>
                            <input type="hidden" name="switch_db" value="1">
                        </form>
                    </div>

                    <button class="btn btn-primary btn-sm" onclick="openModal('createModal')">+ New Item</button>
                </div>
            </div>

            <?php if (empty($items)): ?>
                <div class="text-center" style="padding: 3rem; color: var(--text-muted);">
                    <h3>No items yet</h3>
                    <p>Create your first item to get started!</p>
                </div>
            <?php else: ?>
                <div class="items-grid">
                    <?php foreach ($items as $item): ?>
                        <div class="item-card">
                            <h3 class="item-title">
                                <?php echo e($item['title']); ?>
                            </h3>
                            <p class="item-description">
                                <?php echo e($item['description'] ?? 'No description'); ?>
                            </p>
                            <div class="item-meta">
                                Created:
                                <?php echo date('M d, Y', strtotime($item['created_at'])); ?>
                            </div>
                            <div class="item-actions">
                                <button class="btn btn-secondary btn-sm"
                                    onclick="editItem(<?php echo $item['id']; ?>, '<?php echo e($item['title']); ?>', '<?php echo e($item['description'] ?? ''); ?>')">Edit</button>
                                <form method="POST" style="display: inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this item?')">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="delete_item" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Create New Item</h2>
                <button class="close-modal" onclick="closeModal('createModal')">&times;</button>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" placeholder="Enter item title" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" placeholder="Enter item description"></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Cancel</button>
                    <button type="submit" name="create_item" class="btn btn-primary" style="flex: 1;">Create
                        Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Item</h2>
                <button class="close-modal" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="item_id" id="edit_item_id">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" id="edit_title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="edit_description" class="form-control"></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" name="update_item" class="btn btn-primary" style="flex: 1;">Update
                        Item</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function editItem(id, title, description) {
            document.getElementById('edit_item_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            openModal('editModal');
        }

        // Close modal on outside click
        window.onclick = function (event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>

</html>