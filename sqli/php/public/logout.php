<?php
require_once __DIR__ . '/../includes/session.php';

destroyUserSession();
header('Location: index.php');
exit;
