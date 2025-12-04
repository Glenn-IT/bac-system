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
$page_title = "View Supplier";

// Get supplier data
$stmt = $conn->prepare("SELECT s.*, u.full_name as created_by_name FROM suppliers s 
                       LEFT JOIN users u ON s.created_by = u.id 
                       WHERE s.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: list.php?error=not_found");
    exit();
}

$supplier = $result->fetch_assoc();
$stmt->close();

// Get all documents for this supplier
$docs_stmt = $conn->prepare("SELECT ed.*, dt.document_name, u.full_name as uploaded_by_name 
                             FROM eligibility_docs ed
                             INNER JOIN doc_types dt ON ed.doc_type_id = dt.id
                             LEFT JOIN users u ON ed.uploaded_by = u.id
                             WHERE ed.supplier_id = ?
                             ORDER BY dt.document_name");
$docs_stmt->bind_param("i", $id);
$docs_stmt->execute();
$docs_result = $docs_stmt->get_result();
$documents = [];
while ($row = $docs_result->fetch_assoc()) {
    $documents[] = $row;
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
                    <i class="bi bi-building"></i> Supplier Information
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Company Name</th>
                            <td><?php echo htmlspecialchars($supplier['company_name']); ?></td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td><?php echo htmlspecialchars($supplier['address']); ?></td>
                        </tr>
                        <tr>
                            <th>TIN</th>
                            <td><?php echo htmlspecialchars($supplier['tin']); ?></td>
                        </tr>
                        <tr>
                            <th>PhilGEPS Number</th>
                            <td><?php echo htmlspecialchars($supplier['philgeps_number']); ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo htmlspecialchars($supplier['email']); ?></td>
                        </tr>
                        <tr>
                            <th>Contact Person</th>
                            <td><?php echo htmlspecialchars($supplier['contact_person']); ?></td>
                        </tr>
                        <tr>
                            <th>Contact Number</th>
                            <td><?php echo htmlspecialchars($supplier['contact_no']); ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge <?php echo getStatusBadge($supplier['status']); ?>">
                                    <?php echo $supplier['status']; ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <td><?php echo htmlspecialchars($supplier['created_by_name']); ?></td>
                        </tr>
                        <tr>
                            <th>Created Date</th>
                            <td><?php echo formatDate($supplier['created_at']); ?></td>
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
                            <a href="../documents/add.php?supplier_id=<?php echo $id; ?>" class="btn btn-success">
                                <i class="bi bi-file-earmark-plus"></i> Add Document
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
                    $status_counts = ['Valid' => 0, 'Expired' => 0, 'For Renewal' => 0, 'Missing' => 0];
                    foreach ($documents as $doc) {
                        $status_counts[$doc['status']]++;
                    }
                    ?>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Valid
                            <span class="badge bg-success rounded-pill"><?php echo $status_counts['Valid']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            For Renewal
                            <span class="badge bg-warning text-dark rounded-pill"><?php echo $status_counts['For Renewal']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Expired
                            <span class="badge bg-danger rounded-pill"><?php echo $status_counts['Expired']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Missing
                            <span class="badge bg-secondary rounded-pill"><?php echo $status_counts['Missing']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-file-earmark-text"></i> Eligibility Documents
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
                                            <td><?php echo formatDate($doc['expiration_date']); ?></td>
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
                                                    <a href="../documents/edit.php?id=<?php echo $doc['id']; ?>" class="btn btn-warning btn-sm">
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
