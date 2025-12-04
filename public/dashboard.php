<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$page_title = "Dashboard";

// Get statistics
$stats = [];

// Total Suppliers
$result = $conn->query("SELECT COUNT(*) as count FROM suppliers WHERE status = 'Active'");
$stats['total_suppliers'] = $result->fetch_assoc()['count'];

// Valid Documents
$result = $conn->query("SELECT COUNT(*) as count FROM eligibility_docs WHERE status = 'Valid'");
$stats['valid_docs'] = $result->fetch_assoc()['count'];

// Expired Documents
$result = $conn->query("SELECT COUNT(*) as count FROM eligibility_docs WHERE status = 'Expired'");
$stats['expired_docs'] = $result->fetch_assoc()['count'];

// For Renewal
$result = $conn->query("SELECT COUNT(*) as count FROM eligibility_docs WHERE status = 'For Renewal'");
$stats['renewal_docs'] = $result->fetch_assoc()['count'];

// Missing Documents
$result = $conn->query("SELECT COUNT(*) as count FROM eligibility_docs WHERE status = 'Missing'");
$stats['missing_docs'] = $result->fetch_assoc()['count'];

// Recent Activity
$recent_activity = [];
if (in_array($_SESSION['user_role'], ['Admin', 'Auditor/COA'])) {
    $stmt = $conn->prepare("SELECT al.*, u.full_name FROM activity_logs al 
                           LEFT JOIN users u ON al.user_id = u.id 
                           ORDER BY al.created_at DESC LIMIT 10");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recent_activity[] = $row;
    }
    $stmt->close();
}

// Upcoming Expirations (next 30 days)
$upcoming_expiry = [];
$stmt = $conn->prepare("SELECT ed.*, s.company_name, dt.document_name 
                       FROM eligibility_docs ed
                       INNER JOIN suppliers s ON ed.supplier_id = s.id
                       INNER JOIN doc_types dt ON ed.doc_type_id = dt.id
                       WHERE ed.status IN ('For Renewal', 'Expired')
                       AND ed.expiration_date IS NOT NULL
                       ORDER BY ed.expiration_date ASC
                       LIMIT 10");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $upcoming_expiry[] = $row;
}
$stmt->close();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Total Suppliers</h6>
                        <h2 class="mb-0"><?php echo $stats['total_suppliers']; ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-building" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="stats-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Valid Documents</h6>
                        <h2 class="mb-0"><?php echo $stats['valid_docs']; ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-check-circle" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="stats-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">For Renewal</h6>
                        <h2 class="mb-0"><?php echo $stats['renewal_docs']; ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-exclamation-triangle" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="stats-card danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Expired</h6>
                        <h2 class="mb-0"><?php echo $stats['expired_docs']; ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-x-circle" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Upcoming Expirations -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-calendar-event"></i> Documents Requiring Attention
                </div>
                <div class="card-body">
                    <?php if (count($upcoming_expiry) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Document</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcoming_expiry as $doc): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($doc['company_name']); ?></td>
                                            <td><?php echo htmlspecialchars($doc['document_name']); ?></td>
                                            <td><?php echo formatDate($doc['expiration_date']); ?></td>
                                            <td>
                                                <span class="badge <?php echo getStatusBadge($doc['status']); ?>">
                                                    <?php echo $doc['status']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No documents requiring immediate attention.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Recent Activity
                </div>
                <div class="card-body">
                    <?php if (count($recent_activity) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_activity as $activity): ?>
                                <div class="list-group-item px-0 border-0">
                                    <div class="d-flex w-100 justify-content-between">
                                        <small class="text-primary"><?php echo htmlspecialchars($activity['full_name']); ?></small>
                                        <small class="text-muted"><?php echo date('M d, h:i A', strtotime($activity['created_at'])); ?></small>
                                    </div>
                                    <small class="text-muted"><?php echo htmlspecialchars($activity['action']); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No recent activity to display.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-lightning"></i> Quick Actions
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (in_array($_SESSION['user_role'], ['Admin', 'BAC Secretariat Staff'])): ?>
                            <div class="col-md-3 mb-2">
                                <a href="../suppliers/add.php" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle"></i> Add Supplier
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="../documents/add.php" class="btn btn-success w-100">
                                    <i class="bi bi-file-earmark-plus"></i> Upload Document
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-3 mb-2">
                            <a href="../suppliers/list.php" class="btn btn-info w-100 text-white">
                                <i class="bi bi-list-ul"></i> View Suppliers
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="../documents/compliance.php" class="btn btn-warning w-100">
                                <i class="bi bi-clipboard-check"></i> Compliance Check
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
