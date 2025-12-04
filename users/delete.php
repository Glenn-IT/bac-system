<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

// Check permission
checkPermission(['Admin']);

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = (int)$_GET['id'];

// Prevent deleting own account
if ($id == $_SESSION['user_id']) {
    header("Location: list.php?error=cannot_delete_self");
    exit();
}

// Get username for logging
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: list.php?error=not_found");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

// Delete user
$delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    logActivity($conn, $_SESSION['user_id'], "Deleted user: " . $user['username'], 'Users');
    header("Location: list.php?success=deleted");
} else {
    header("Location: list.php?error=delete_failed");
}

$delete_stmt->close();
exit();
?>
