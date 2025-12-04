<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

$page_title = "Suppliers List";

// Get all suppliers
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$query = "SELECT s.*, u.full_name as created_by_name FROM suppliers s 
          LEFT JOIN users u ON s.created_by = u.id 
          WHERE 1=1";

if ($search) {
    $query .= " AND (s.company_name LIKE '%$search%' OR s.tin LIKE '%$search%' OR s.philgeps_number LIKE '%$search%')";
}

$query .= " ORDER BY s.created_at DESC";
$result = $conn->query($query);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-building"></i> Suppliers List</span>
            <?php if (in_array($_SESSION['user_role'], ['Admin', 'BAC Secretariat Staff'])): ?>
                <a href="add.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Add New Supplier
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <!-- Search Form -->
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search by company name, TIN, or PhilGEPS number..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                            <?php if ($search): ?>
                                <a href="list.php" class="btn btn-secondary">
                                    <i class="bi bi-x"></i> Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['success']) {
                        case 'added':
                            echo "Supplier added successfully!";
                            break;
                        case 'updated':
                            echo "Supplier updated successfully!";
                            break;
                        case 'deleted':
                            echo "Supplier deleted successfully!";
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
                            <th>Company Name</th>
                            <th>TIN</th>
                            <th>PhilGEPS No.</th>
                            <th>Contact Person</th>
                            <th>Contact No.</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tin']); ?></td>
                                    <td><?php echo htmlspecialchars($row['philgeps_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['contact_person']); ?></td>
                                    <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                                    <td>
                                        <span class="badge <?php echo getStatusBadge($row['status']); ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm btn-action">
                                            <i class="bi bi-eye"></i>
                                        </a>
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
                                    No suppliers found. 
                                    <?php if (in_array($_SESSION['user_role'], ['Admin', 'BAC Secretariat Staff'])): ?>
                                        <a href="add.php">Add your first supplier</a>
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

<?php include '../includes/footer.php'; ?>
