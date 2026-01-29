<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';

// User registration
function registerUser($username, $password, $email)
{
    $db = Database::getInstance();

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        $db->query($sql, [$username, $hashedPassword, $email]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// User login
function loginUser($username, $password)
{
    $db = Database::getInstance();

    try {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = $db->query($sql, [$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            setUserSession($user['id'], $user['username']);
            return true;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

// Get all items for current user
function getUserItems($userId)
{
    $db = Database::getInstance();

    try {
        $sql = "SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $db->query($sql, [$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Create new item
function createItem($userId, $title, $description)
{
    $db = Database::getInstance();

    try {
        $sql = "INSERT INTO items (user_id, title, description) VALUES (?, ?, ?)";
        $db->query($sql, [$userId, $title, $description]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Update item
function updateItem($itemId, $userId, $title, $description)
{
    $db = Database::getInstance();

    try {
        $sql = "UPDATE items SET title = ?, description = ? WHERE id = ? AND user_id = ?";
        $db->query($sql, [$title, $description, $itemId, $userId]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Delete item
function deleteItem($itemId, $userId)
{
    $db = Database::getInstance();

    try {
        $sql = "DELETE FROM items WHERE id = ? AND user_id = ?";
        $db->query($sql, [$itemId, $userId]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Get single item
function getItem($itemId, $userId)
{
    $db = Database::getInstance();

    try {
        $sql = "SELECT * FROM items WHERE id = ? AND user_id = ?";
        $stmt = $db->query($sql, [$itemId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

// VULNERABLE: Search items with SQL injection
function searchItems($searchTerm)
{
    $db = Database::getInstance();

    // INTENTIONALLY VULNERABLE - Direct string concatenation
    $sql = "SELECT * FROM items WHERE title LIKE '%" . $searchTerm . "%' OR description LIKE '%" . $searchTerm . "%'";

    try {
        $result = $db->vulnerableQuery($sql);
        if ($result) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    } catch (Exception $e) {
        return [];
    }
}

// Escape HTML output
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Redirect helper
function redirect($url)
{
    header("Location: $url");
    exit;
}

// Flash message helpers
function setFlashMessage($message, $type = 'info')
{
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage()
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}
