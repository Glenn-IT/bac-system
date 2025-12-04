<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

// Check permission
checkPermission(['Admin', 'BAC Secretariat Staff']);

$page_title = "Add Supplier";
$error = '';
$success = '';

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
        // Check if TIN already exists
        $check_stmt = $conn->prepare("SELECT id FROM suppliers WHERE tin = ?");
        $check_stmt->bind_param("s", $tin);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "A supplier with this TIN already exists.";
        } else {
            // Insert supplier
            $stmt = $conn->prepare("INSERT INTO suppliers (company_name, address, tin, philgeps_number, email, contact_person, contact_no, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssi", $company_name, $address, $tin, $philgeps_number, $email, $contact_person, $contact_no, $status, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                logActivity($conn, $_SESSION['user_id'], "Added supplier: $company_name", 'Suppliers');
                header("Location: list.php?success=added");
                exit();
            } else {
                $error = "Error adding supplier: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-plus-circle"></i> Add New Supplier
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
                                       value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>" required>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="2" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tin" class="form-label">TIN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tin" name="tin" 
                                       value="<?php echo isset($_POST['tin']) ? htmlspecialchars($_POST['tin']) : ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="philgeps_number" class="form-label">PhilGEPS Number</label>
                                <input type="text" class="form-control" id="philgeps_number" name="philgeps_number" 
                                       value="<?php echo isset($_POST['philgeps_number']) ? htmlspecialchars($_POST['philgeps_number']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                       value="<?php echo isset($_POST['contact_person']) ? htmlspecialchars($_POST['contact_person']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="contact_no" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_no" name="contact_no" 
                                       value="<?php echo isset($_POST['contact_no']) ? htmlspecialchars($_POST['contact_no']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="Active" selected>Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="list.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
