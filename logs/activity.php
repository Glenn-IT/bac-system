<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

// Check permission
checkPermission(['Admin', 'Auditor/COA']);

$page_title = "Activity Logs";

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 50;
$offset = ($page - 1) * $records_per_page;

// Filters
$user_filter = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$module_filter = isset($_GET['module']) ? sanitize($_GET['module']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';

// Build query
$query = "SELECT al.*, u.full_name, u.username FROM activity_logs al 
          LEFT JOIN users u ON al.user_id = u.id 
          WHERE 1=1";

if ($user_filter > 0) {
    $query .= " AND al.user_id = $user_filter";
}

if ($module_filter) {
    $query .= " AND al.module = '$module_filter'";
}

if ($date_from) {
    $query .= " AND DATE(al.created_at) >= '$date_from'";
}

if ($date_to) {
    $query .= " AND DATE(al.created_at) <= '$date_to'";
}

// Count total records
$count_query = str_replace("SELECT al.*, u.full_name, u.username", "SELECT COUNT(*) as total", $query);
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

$query .= " ORDER BY al.created_at DESC LIMIT $records_per_page OFFSET $offset";
$result = $conn->query($query);

// Get users for filter
$users = $conn->query("SELECT id, username, full_name FROM users ORDER BY full_name");

// Get modules for filter
$modules = $conn->query("SELECT DISTINCT module FROM activity_logs ORDER BY module");

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-clock-history"></i> Activity Logs
            <span class="badge bg-secondary ms-2"><?php echo number_format($total_records); ?> records</span>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select form-select-sm">
                            <option value="">All Users</option>
                            <?php while ($u = $users->fetch_assoc()): ?>
                                <option value="<?php echo $u['id']; ?>" <?php echo $user_filter == $u['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($u['full_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Module</label>
                        <select name="module" class="form-select form-select-sm">
                            <option value="">All Modules</option>
                            <?php while ($m = $modules->fetch_assoc()): ?>
                                <option value="<?php echo $m['module']; ?>" <?php echo $module_filter == $m['module'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($m['module']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" 
                               value="<?php echo $date_from; ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" 
                               value="<?php echo $date_to; ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-filter"></i> Filter
                            </button>
                            <a href="activity.php" class="btn btn-secondary btn-sm">
                                <i class="bi bi-x"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- Logs Table -->
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th width="150">Date & Time</th>
                            <th width="150">User</th>
                            <th width="100">Module</th>
                            <th>Action</th>
                            <th width="120">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <small>
                                            <?php echo date('M d, Y', strtotime($row['created_at'])); ?><br>
                                            <?php echo date('h:i:s A', strtotime($row['created_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['full_name'] ?? 'Unknown'); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['username'] ?? '-'); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($row['module']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['action']); ?></td>
                                    <td><small><?php echo htmlspecialchars($row['ip_address']); ?></small></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No activity logs found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&user_id=<?php echo $user_filter; ?>&module=<?php echo $module_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>">
                                    Previous
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&user_id=<?php echo $user_filter; ?>&module=<?php echo $module_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&user_id=<?php echo $user_filter; ?>&module=<?php echo $module_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>">
                                    Next
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <div class="text-center">
                    <small class="text-muted">
                        Page <?php echo $page; ?> of <?php echo $total_pages; ?> 
                        (Showing <?php echo $offset + 1; ?> - <?php echo min($offset + $records_per_page, $total_records); ?> of <?php echo number_format($total_records); ?> records)
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
