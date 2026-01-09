<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

// Check permission
checkPermission(['Admin', 'BAC Secretariat Staff']);

$page_title = "Upload BAC Document";
$error = '';
$bac_record_id = isset($_GET['bac_record_id']) ? (int)$_GET['bac_record_id'] : 0;

// Get BAC records
$bac_records = $conn->query("SELECT id, bac_cod FROM bac_records ORDER BY bac_cod DESC");

// Get document types
$doc_types = $conn->query("SELECT id, document_name FROM doc_types ORDER BY document_name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bac_record_id = (int)$_POST['bac_record_id'];
    $doc_type_id = (int)$_POST['doc_type_id'];
    $issued_date = sanitize($_POST['issued_date']);
    $expiry_date = sanitize($_POST['expiry_date']);
    
    // Validate
    if ($bac_record_id == 0 || $doc_type_id == 0) {
        $error = "Please select BAC COD and document type.";
    } else {
        // Check if this document type already exists for this BAC record
        $check_stmt = $conn->prepare("SELECT id FROM bac_documents WHERE bac_record_id = ? AND doc_type_id = ?");
        $check_stmt->bind_param("ii", $bac_record_id, $doc_type_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Get document type name for error message
            $dt_stmt = $conn->prepare("SELECT document_name FROM doc_types WHERE id = ?");
            $dt_stmt->bind_param("i", $doc_type_id);
            $dt_stmt->execute();
            $dt_result = $dt_stmt->get_result();
            $doc_type_name = $dt_result->fetch_assoc()['document_name'];
            $dt_stmt->close();
            
            $error = "This BAC record already has a document of type '<strong>" . htmlspecialchars($doc_type_name) . "</strong>'. Please edit the existing document instead of uploading a duplicate.";
        }
        $check_stmt->close();
        
        if (!$error) {
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
                $new_filename = 'bac_doc_' . $bac_record_id . '_' . $doc_type_id . '_' . time() . '.' . $file_ext;
                $file_path = 'uploads/' . $new_filename;
                
                if (!move_uploaded_file($file_tmp, '../' . $file_path)) {
                    $error = "Error uploading file.";
                    $file_path = '';
                }
            }
        }
        
        if (!$error) {
            // Insert BAC document
            $stmt = $conn->prepare("INSERT INTO bac_documents (bac_record_id, doc_type_id, file_name, file_path, issued_date, expiry_date, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissssi", $bac_record_id, $doc_type_id, $file_name, $file_path, $issued_date, $expiry_date, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                $doc_id = $conn->insert_id;
                
                // Update status based on expiry date
                updateBacDocumentStatus($conn, $doc_id);
                
                // Get BAC COD for logging
                $bac_stmt = $conn->prepare("SELECT bac_cod FROM bac_records WHERE id = ?");
                $bac_stmt->bind_param("i", $bac_record_id);
                $bac_stmt->execute();
                $bac_result = $bac_stmt->get_result();
                $bac_record = $bac_result->fetch_assoc();
                $bac_stmt->close();
                
                logActivity($conn, $_SESSION['user_id'], "Uploaded BAC document for " . $bac_record['bac_cod'], 'BAC Documents');
                header("Location: list.php?success=added");
                exit();
            } else {
                $error = "Error adding document: " . $conn->error;
            }
            $stmt->close();
        }
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
                    <i class="bi bi-file-earmark-plus"></i> Upload BAC Document
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
                                <label for="bac_record_id" class="form-label">BAC COD <span class="text-danger">*</span></label>
                                <select class="form-select" id="bac_record_id" name="bac_record_id" required>
                                    <option value="">Select BAC COD</option>
                                    <?php while ($bac = $bac_records->fetch_assoc()): ?>
                                        <option value="<?php echo $bac['id']; ?>" 
                                            <?php echo ($bac_record_id == $bac['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($bac['bac_cod']); ?>
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
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="document_file" class="form-label">Upload File (PDF, JPG, PNG)</label>
                                <input type="file" class="form-control" id="document_file" name="document_file" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Maximum file size: 10MB</small>
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
