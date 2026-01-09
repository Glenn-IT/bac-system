<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = (int)$_GET['id'];
$page_title = "View Record";

// Get record details
$stmt = $conn->prepare("SELECT br.*, u.full_name as created_by_name 
                       FROM bac_records br 
                       LEFT JOIN users u ON br.created_by = u.id 
                       WHERE br.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: list.php?error=not_found");
    exit();
}

$record = $result->fetch_assoc();
$stmt->close();

// Get all document types
$all_doc_types = [];
$dt_query = $conn->query("SELECT id, document_name FROM doc_types ORDER BY document_name");
while ($dt = $dt_query->fetch_assoc()) {
    $all_doc_types[] = $dt;
}

// Get all BAC documents for this record
$docs_stmt = $conn->prepare("SELECT bd.*, dt.document_name, u.full_name as uploaded_by_name 
                             FROM bac_documents bd
                             INNER JOIN doc_types dt ON bd.doc_type_id = dt.id
                             LEFT JOIN users u ON bd.uploaded_by = u.id
                             WHERE bd.bac_record_id = ?
                             ORDER BY dt.document_name");
$docs_stmt->bind_param("i", $id);
$docs_stmt->execute();
$docs_result = $docs_stmt->get_result();
$documents = [];
$uploaded_doc_types = [];
while ($row = $docs_result->fetch_assoc()) {
    // Update document status
    updateBacDocumentStatus($conn, $row['id']);
    
    // Re-fetch updated status WITH document_name
    $update_stmt = $conn->prepare("SELECT bd.*, dt.document_name, u.full_name as uploaded_by_name 
                                   FROM bac_documents bd
                                   INNER JOIN doc_types dt ON bd.doc_type_id = dt.id
                                   LEFT JOIN users u ON bd.uploaded_by = u.id
                                   WHERE bd.id = ?");
    $update_stmt->bind_param("i", $row['id']);
    $update_stmt->execute();
    $row = $update_stmt->get_result()->fetch_assoc();
    $update_stmt->close();
    
    $documents[] = $row;
    $uploaded_doc_types[] = $row['doc_type_id'];
}
$docs_stmt->close();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-file-earmark-text"></i> Record Information
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">BAC COD</th>
                            <td><h4><span class="badge bg-primary"><?php echo htmlspecialchars($record['bac_cod']); ?></span></h4></td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <td><?php echo htmlspecialchars($record['created_by_name']); ?></td>
                        </tr>
                        <tr>
                            <th>Created Date</th>
                            <td><?php echo date('F d, Y h:i A', strtotime($record['created_at'])); ?></td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td><?php echo date('F d, Y h:i A', strtotime($record['updated_at'])); ?></td>
                        </tr>
                    </table>
                    
                    <div class="mt-3">
                        <a href="list.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                        <?php if (in_array($_SESSION['user_role'], ['Admin', 'BAC Secretariat Staff'])): ?>
                            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="../bac-docs/add.php?bac_record_id=<?php echo $id; ?>" class="btn btn-success">
                                <i class="bi bi-file-earmark-plus"></i> Add Document
                            </a>
                        <?php endif; ?>
                        <?php if ($_SESSION['user_role'] == 'Admin'): ?>
                            <a href="delete.php?id=<?php echo $record['id']; ?>" class="btn btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this record?');">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pie-chart"></i> Document Summary
                </div>
                <div class="card-body">
                    <?php
                    // Calculate based on ALL document types vs uploaded documents
                    $total_docs = count($all_doc_types);
                    $uploaded_docs = count($documents);
                    $valid_docs = 0;
                    $expired_docs = 0;
                    $renewal_docs = 0;
                    
                    // Count status types
                    foreach ($documents as $doc) {
                        if ($doc['status'] == 'Valid') $valid_docs++;
                        elseif ($doc['status'] == 'Expired') $expired_docs++;
                        elseif ($doc['status'] == 'For Renewal') $renewal_docs++;
                    }
                    
                    $missing_docs = $total_docs - $uploaded_docs;
                    ?>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Document Types
                            <span class="badge bg-primary rounded-pill"><?php echo $total_docs; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Uploaded
                            <span class="badge bg-success rounded-pill"><?php echo $uploaded_docs; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Valid
                            <span class="badge bg-success rounded-pill"><?php echo $valid_docs; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            For Renewal
                            <span class="badge bg-warning rounded-pill"><?php echo $renewal_docs; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Expired
                            <span class="badge bg-danger rounded-pill"><?php echo $expired_docs; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Missing
                            <span class="badge bg-secondary rounded-pill"><?php echo $missing_docs; ?></span>
                        </li>
                    </ul>
                    
                    <?php if ($total_docs > 0): ?>
                        <div class="mt-3">
                            <div class="progress" style="height: 25px;">
                                <?php $percentage = ($uploaded_docs / $total_docs) * 100; ?>
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?php echo $percentage; ?>%;" 
                                     aria-valuenow="<?php echo $percentage; ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <?php echo round($percentage, 1); ?>%
                                </div>
                            </div>
                            <small class="text-muted">Upload Completion Rate (<?php echo $uploaded_docs; ?> of <?php echo $total_docs; ?> document types)</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-folder-fill"></i> BAC Documents
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Document Type</th>
                                    <th>Issued Date</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                    <th>File</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($documents) > 0): ?>
                                    <?php $no = 1; foreach ($documents as $doc): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($doc['document_name']); ?></td>
                                            <td><?php echo formatDate($doc['issued_date']); ?></td>
                                            <td><?php echo formatDate($doc['expiry_date']); ?></td>
                                            <td>
                                                <span class="badge <?php echo getStatusBadge($doc['status']); ?>">
                                                    <?php echo $doc['status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($doc['file_path'] && file_exists('../' . $doc['file_path'])): ?>
                                                    <a href="../<?php echo $doc['file_path']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="bi bi-download"></i> View
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">No file</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (in_array($_SESSION['user_role'], ['Admin', 'BAC Secretariat Staff'])): ?>
                                                    <a href="../bac-docs/edit.php?id=<?php echo $doc['id']; ?>" class="btn btn-warning btn-sm">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            No documents uploaded yet.
                                            <?php if (in_array($_SESSION['user_role'], ['Admin', 'BAC Secretariat Staff'])): ?>
                                                <br><br>
                                                <a href="../bac-docs/add.php?bac_record_id=<?php echo $id; ?>" class="btn btn-success btn-sm">
                                                    <i class="bi bi-file-earmark-plus"></i> Upload First Document
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
