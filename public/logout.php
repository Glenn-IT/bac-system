<?php
session_start();

// Log activity before destroying session
if (isset($_SESSION['user_id'])) {
    require_once '../config/db.php';
    logActivity($conn, $_SESSION['user_id'], 'User logged out', 'Authentication');
}

// Destroy session
session_destroy();

// Redirect to login
header("Location: index.php");
exit();
?>
