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

// Get document info
$stmt = $conn->prepare("SELECT bd.*, br.bac_cod FROM bac_documents bd
                       INNER JOIN bac_records br ON bd.bac_record_id = br.id
                       WHERE bd.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: list.php?error=not_found");
    exit();
}

$document = $result->fetch_assoc();
$stmt->close();

// Delete file if exists
if ($document['file_path'] && file_exists('../' . $document['file_path'])) {
    unlink('../' . $document['file_path']);
}

// Delete from database
$delete_stmt = $conn->prepare("DELETE FROM bac_documents WHERE id = ?");
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    logActivity($conn, $_SESSION['user_id'], "Deleted BAC document for " . $document['bac_cod'], 'BAC Documents');
    header("Location: list.php?success=deleted");
} else {
    header("Location: list.php?error=delete_failed");
}

$delete_stmt->close();
exit();
?>
