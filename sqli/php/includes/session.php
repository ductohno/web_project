<?php
require_once __DIR__ . '/config.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getUserId()
{
    return $_SESSION['user_id'] ?? null;
}

function getUsername()
{
    return $_SESSION['username'] ?? null;
}

function setUserSession($userId, $username)
{
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['login_time'] = time();
}

function destroyUserSession()
{
    session_unset();
    session_destroy();
}
