<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

// Check permission
checkPermission(['Admin', 'BAC Secretariat Staff']);

$page_title = "Upload Document";
$error = '';
$supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;

// Get suppliers
$suppliers = $conn->query("SELECT id, company_name FROM suppliers WHERE status = 'Active' ORDER BY company_name");

// Get document types
$doc_types = $conn->query("SELECT id, document_name FROM doc_types ORDER BY document_name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $supplier_id = (int)$_POST['supplier_id'];
    $doc_type_id = (int)$_POST['doc_type_id'];
    $issued_date = sanitize($_POST['issued_date']);
    $expiration_date = sanitize($_POST['expiration_date']);
    $remarks = sanitize($_POST['remarks']);
    
    // Validate
    if ($supplier_id == 0 || $doc_type_id == 0) {
        $error = "Please select supplier and document type.";
    } else {
        $file_path = '';
        
        // Handle file upload
        if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
            $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $file_name = $_FILES['document_file']['name'];
            $file_tmp = $_FILES['document_file']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed_extensions)) {
                $error = "Invalid file format. Only PDF, JPG, and PNG are allowed.";
            } else {
                // Create upload directory if not exists
                $upload_dir = "../uploads/";
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Generate unique filename
                $new_filename = 'doc_' . $supplier_id . '_' . $doc_type_id . '_' . time() . '.' . $file_ext;
                $file_path = 'uploads/' . $new_filename;
                
                if (!move_uploaded_file($file_tmp, '../' . $file_path)) {
                    $error = "Error uploading file.";
                    $file_path = '';
                }
            }
        }
        
        if (!$error) {
            // Insert document
            $stmt = $conn->prepare("INSERT INTO eligibility_docs (supplier_id, doc_type_id, issued_date, expiration_date, file_path, remarks, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissssi", $supplier_id, $doc_type_id, $issued_date, $expiration_date, $file_path, $remarks, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                $doc_id = $conn->insert_id;
                
                // Update status
                updateDocumentStatus($conn, $doc_id);
                
                // Get supplier name for logging
                $sup_stmt = $conn->prepare("SELECT company_name FROM suppliers WHERE id = ?");
                $sup_stmt->bind_param("i", $supplier_id);
                $sup_stmt->execute();
                $sup_result = $sup_stmt->get_result();
                $supplier = $sup_result->fetch_assoc();
                $sup_stmt->close();
                
                logActivity($conn, $_SESSION['user_id'], "Uploaded document for " . $supplier['company_name'], 'Documents');
                header("Location: list.php?success=added");
                exit();
            } else {
                $error = "Error adding document: " . $conn->error;
            }
            $stmt->close();
        }
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
                    <i class="bi bi-file-earmark-plus"></i> Upload Document
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select class="form-select" id="supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    <?php while ($sup = $suppliers->fetch_assoc()): ?>
                                        <option value="<?php echo $sup['id']; ?>" 
                                            <?php echo ($supplier_id == $sup['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($sup['company_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="doc_type_id" class="form-label">Document Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="doc_type_id" name="doc_type_id" required>
                                    <option value="">Select Document Type</option>
                                    <?php while ($dt = $doc_types->fetch_assoc()): ?>
                                        <option value="<?php echo $dt['id']; ?>">
                                            <?php echo htmlspecialchars($dt['document_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="issued_date" class="form-label">Issued Date</label>
                                <input type="date" class="form-control" id="issued_date" name="issued_date">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="expiration_date" class="form-label">Expiration Date</label>
                                <input type="date" class="form-control" id="expiration_date" name="expiration_date">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="document_file" class="form-label">Upload File (PDF, JPG, PNG)</label>
                                <input type="file" class="form-control" id="document_file" name="document_file" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Maximum file size: 10MB</small>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="list.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-upload"></i> Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
