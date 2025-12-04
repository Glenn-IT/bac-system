<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

// Check permission - only Admin can delete
checkPermission(['Admin']);

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = (int)$_GET['id'];

// Get supplier name for logging
$stmt = $conn->prepare("SELECT company_name FROM suppliers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: list.php?error=not_found");
    exit();
}

$supplier = $result->fetch_assoc();
$stmt->close();

// Delete associated documents and files
$docs_stmt = $conn->prepare("SELECT file_path FROM eligibility_docs WHERE supplier_id = ?");
$docs_stmt->bind_param("i", $id);
$docs_stmt->execute();
$docs_result = $docs_stmt->get_result();

while ($doc = $docs_result->fetch_assoc()) {
    if ($doc['file_path'] && file_exists('../' . $doc['file_path'])) {
        unlink('../' . $doc['file_path']);
    }
}
$docs_stmt->close();

// Delete supplier (cascade will delete documents)
$delete_stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    logActivity($conn, $_SESSION['user_id'], "Deleted supplier: " . $supplier['company_name'], 'Suppliers');
    header("Location: list.php?success=deleted");
} else {
    header("Location: list.php?error=delete_failed");
}

$delete_stmt->close();
exit();
?>
