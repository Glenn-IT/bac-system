<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

// Check permission
checkPermission(['Admin', 'BAC Secretariat Staff']);

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = (int)$_GET['id'];
$page_title = "Edit Supplier";
$error = '';

// Get supplier data
$stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: list.php?error=not_found");
    exit();
}

$supplier = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_name = sanitize($_POST['company_name']);
    $address = sanitize($_POST['address']);
    $tin = sanitize($_POST['tin']);
    $philgeps_number = sanitize($_POST['philgeps_number']);
    $email = sanitize($_POST['email']);
    $contact_person = sanitize($_POST['contact_person']);
    $contact_no = sanitize($_POST['contact_no']);
    $status = sanitize($_POST['status']);
    
    // Validate required fields
    if (empty($company_name) || empty($address) || empty($tin)) {
        $error = "Company name, address, and TIN are required.";
    } else {
        // Check if TIN already exists for other suppliers
        $check_stmt = $conn->prepare("SELECT id FROM suppliers WHERE tin = ? AND id != ?");
        $check_stmt->bind_param("si", $tin, $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "A supplier with this TIN already exists.";
        } else {
            // Update supplier
            $stmt = $conn->prepare("UPDATE suppliers SET company_name = ?, address = ?, tin = ?, philgeps_number = ?, email = ?, contact_person = ?, contact_no = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssssssssi", $company_name, $address, $tin, $philgeps_number, $email, $contact_person, $contact_no, $status, $id);
            
            if ($stmt->execute()) {
                logActivity($conn, $_SESSION['user_id'], "Updated supplier: $company_name", 'Suppliers');
                header("Location: list.php?success=updated");
                exit();
            } else {
                $error = "Error updating supplier: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
} else {
    // Pre-fill form with existing data
    $_POST = $supplier;
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Edit Supplier
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="<?php echo htmlspecialchars($_POST['company_name']); ?>" required>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="2" required><?php echo htmlspecialchars($_POST['address']); ?></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tin" class="form-label">TIN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tin" name="tin" 
                                       value="<?php echo htmlspecialchars($_POST['tin']); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="philgeps_number" class="form-label">PhilGEPS Number</label>
                                <input type="text" class="form-control" id="philgeps_number" name="philgeps_number" 
                                       value="<?php echo htmlspecialchars($_POST['philgeps_number']); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email']); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                       value="<?php echo htmlspecialchars($_POST['contact_person']); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="contact_no" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_no" name="contact_no" 
                                       value="<?php echo htmlspecialchars($_POST['contact_no']); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="Active" <?php echo $_POST['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo $_POST['status'] == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="list.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
