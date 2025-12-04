<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

$page_title = "Records List";
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$query = "SELECT br.*, u.full_name as created_by_name 
          FROM bac_records br 
          LEFT JOIN users u ON br.created_by = u.id 
          WHERE 1=1";

if (!empty($search)) {
    $query .= " AND br.bac_cod LIKE '%$search%'";
}

$query .= " ORDER BY br.bac_cod DESC";

$result = $conn->query($query);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container-fluid">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> 
            <?php 
                if ($_GET['success'] == 'added') echo 'Record added successfully!';
                elseif ($_GET['success'] == 'updated') echo 'Record updated successfully!';
                elseif ($_GET['success'] == 'deleted') echo 'Record deleted successfully!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-ul"></i> Records List</span>
            <?php if (in_array($_SESSION['user_role'], ['Admin', 'BAC Secretariat Staff'])): ?>
                <a href="add.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Add New Record
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <!-- Search -->
            <form method="GET" class="row g-3 mb-3">
                <div class="col-md-8">
                    <input type="text" class="form-control" name="search" placeholder="Search by BAC COD (e.g., PE 25-001)" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <a href="list.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
            
            <!-- Records Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>BAC COD</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['bac_cod']); ?></strong></td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <a href="view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (in_array($_SESSION['user_role'], ['Admin', 'BAC Secretariat Staff'])): ?>
                                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['user_role'] == 'Admin'): ?>
                                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this record?');" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <small class="text-muted">Total Records: <?php echo $result->num_rows; ?></small>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
