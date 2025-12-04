<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

// Check permission - Only Admin can delete
checkPermission(['Admin']);

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = (int)$_GET['id'];

// Get record info
$stmt = $conn->prepare("SELECT bac_cod FROM bac_records WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: list.php?error=not_found");
    exit();
}

$record = $result->fetch_assoc();
$stmt->close();

// Delete from database
$delete_stmt = $conn->prepare("DELETE FROM bac_records WHERE id = ?");
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    logActivity($conn, $_SESSION['user_id'], "Deleted record: " . $record['bac_cod'], 'Records');
    header("Location: list.php?success=deleted");
} else {
    header("Location: list.php?error=delete_failed");
}

$delete_stmt->close();
exit();
?>
