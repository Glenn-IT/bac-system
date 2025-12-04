<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

$page_title = "Documents List";

// Get filter parameters
$supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$doc_type = isset($_GET['doc_type']) ? (int)$_GET['doc_type'] : 0;

// Build query
$query = "SELECT ed.*, s.company_name, dt.document_name, u.full_name as uploaded_by_name 
          FROM eligibility_docs ed
          INNER JOIN suppliers s ON ed.supplier_id = s.id
          INNER JOIN doc_types dt ON ed.doc_type_id = dt.id
          LEFT JOIN users u ON ed.uploaded_by = u.id
          WHERE 1=1";

if ($supplier_id > 0) {
    $query .= " AND ed.supplier_id = $supplier_id";
}

if ($status) {
    $query .= " AND ed.status = '$status'";
}

if ($doc_type > 0) {
    $query .= " AND ed.doc_type_id = $doc_type";
}

$query .= " ORDER BY ed.created_at DESC";
$result = $conn->query($query);

// Get suppliers for filter
$suppliers = $conn->query("SELECT id, company_name FROM suppliers WHERE status = 'Active' ORDER BY company_name");

// Get document types for filter
$doc_types = $conn->query("SELECT id, document_name FROM doc_types ORDER BY document_name");

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-file-earmark-text"></i> Documents List</span>
            <?php if (in_array($_SESSION['user_role'], ['Admin', 'BAC Secretariat Staff'])): ?>
                <a href="add.php" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle"></i> Upload Document
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <select name="supplier_id" class="form-select">
                            <option value="">All Suppliers</option>
                            <?php while ($sup = $suppliers->fetch_assoc()): ?>
                                <option value="<?php echo $sup['id']; ?>" <?php echo $supplier_id == $sup['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($sup['company_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="doc_type" class="form-select">
                            <option value="">All Document Types</option>
                            <?php while ($dt = $doc_types->fetch_assoc()): ?>
                                <option value="<?php echo $dt['id']; ?>" <?php echo $doc_type == $dt['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dt['document_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="Valid" <?php echo $status == 'Valid' ? 'selected' : ''; ?>>Valid</option>
                            <option value="For Renewal" <?php echo $status == 'For Renewal' ? 'selected' : ''; ?>>For Renewal</option>
                            <option value="Expired" <?php echo $status == 'Expired' ? 'selected' : ''; ?>>Expired</option>
                            <option value="Missing" <?php echo $status == 'Missing' ? 'selected' : ''; ?>>Missing</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                        <a href="list.php" class="btn btn-secondary">
                            <i class="bi bi-x"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['success']) {
                        case 'added':
                            echo "Document uploaded successfully!";
                            break;
                        case 'updated':
                            echo "Document updated successfully!";
                            break;
                        case 'deleted':
                            echo "Document deleted successfully!";
                            break;
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Supplier</th>
                            <th>Document Type</th>
                            <th>Issued Date</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['document_name']); ?></td>
                                    <td><?php echo formatDate($row['issued_date']); ?></td>
                                    <td><?php echo formatDate($row['expiration_date']); ?></td>
                                    <td>
                                        <span class="badge <?php echo getStatusBadge($row['status']); ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['file_path'] && file_exists('../' . $row['file_path'])): ?>
                                            <a href="../<?php echo $row['file_path']; ?>" target="_blank" class="btn btn-sm btn-info btn-action">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No file</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (in_array($_SESSION['user_role'], ['Admin', 'BAC Secretariat Staff'])): ?>
                                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm btn-action">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if ($_SESSION['user_role'] == 'Admin'): ?>
                                                <a href="delete.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-danger btn-sm btn-action btn-delete">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    No documents found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
