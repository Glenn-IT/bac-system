<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$bac_record_id = isset($_GET['bac_record_id']) ? (int)$_GET['bac_record_id'] : 0;

if ($bac_record_id <= 0) {
    echo json_encode([]);
    exit();
}

$ids = [];
$stmt = $conn->prepare("SELECT doc_type_id FROM bac_documents WHERE bac_record_id = ?");
$stmt->bind_param("i", $bac_record_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $ids[] = (int)$row['doc_type_id'];
}
$stmt->close();

header('Content-Type: application/json');
echo json_encode($ids);
