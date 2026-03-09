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
$page_title = "Edit BAC Document";
$error = '';

// Get document data
$stmt = $conn->prepare("SELECT bd.*, br.bac_cod FROM bac_documents bd
                       INNER JOIN bac_records br ON bd.bac_record_id = br.id
                       WHERE bd.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: list.php?error=not_found");
    exit();
}

$document = $result->fetch_assoc();
$stmt->close();

// Get BAC records
$bac_records = $conn->query("SELECT id, bac_cod FROM bac_records ORDER BY bac_cod DESC");

// Get document types
$doc_types = $conn->query("SELECT id, document_name FROM doc_types ORDER BY document_name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bac_record_id = (int)$_POST['bac_record_id'];
    $doc_type_id = (int)$_POST['doc_type_id'];
    $issued_date = sanitize($_POST['issued_date']);
    $expiry_date = sanitize($_POST['expiry_date']);
    $file_path = $document['file_path'];
    $file_name = $document['file_name'];
    
    // Validate
    if ($bac_record_id == 0 || $doc_type_id == 0) {
        $error = "Please select BAC COD and document type.";
    } else {
        // Handle file upload
        if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
            $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $new_file_name = $_FILES['document_file']['name'];
            $file_tmp = $_FILES['document_file']['tmp_name'];
            $file_ext = strtolower(pathinfo($new_file_name, PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed_extensions)) {
                $error = "Invalid file format. Only PDF, JPG, and PNG are allowed.";
            } else {
                // Delete old file if exists
                if ($file_path && file_exists('../' . $file_path)) {
                    unlink('../' . $file_path);
                }
                
                // Upload new file
                $upload_dir = "../uploads/";
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $new_filename = 'bac_doc_' . $bac_record_id . '_' . $doc_type_id . '_' . time() . '.' . $file_ext;
                $file_path = 'uploads/' . $new_filename;
                $file_name = $new_file_name;
                
                if (!move_uploaded_file($file_tmp, '../' . $file_path)) {
                    $error = "Error uploading file.";
                    $file_path = $document['file_path'];
                    $file_name = $document['file_name'];
                }
            }
        }
        
        if (!$error) {
            // Update BAC document
            $stmt = $conn->prepare("UPDATE bac_documents SET bac_record_id = ?, doc_type_id = ?, file_name = ?, file_path = ?, issued_date = ?, expiry_date = ? WHERE id = ?");
            $stmt->bind_param("iissssi", $bac_record_id, $doc_type_id, $file_name, $file_path, $issued_date, $expiry_date, $id);
            
            if ($stmt->execute()) {
                // Update status based on expiry date
                updateBacDocumentStatus($conn, $id);
                
                logActivity($conn, $_SESSION['user_id'], "Updated BAC document for " . $document['bac_cod'], 'BAC Documents');
                header("Location: ../records/view.php?id=" . $bac_record_id . "&success=doc_updated");
                exit();
            } else {
                $error = "Error updating document: " . $conn->error;
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
                    <i class="bi bi-pencil-square"></i> Edit BAC Document
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
                                            <?php echo ($document['bac_record_id'] == $bac['id']) ? 'selected' : ''; ?>>
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
                                        <option value="<?php echo $dt['id']; ?>" 
                                            <?php echo ($document['doc_type_id'] == $dt['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dt['document_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="issued_date" class="form-label">Issued Date</label>
                                <input type="date" class="form-control" id="issued_date" name="issued_date" 
                                       value="<?php echo htmlspecialchars($document['issued_date']); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" id="expiry_date" name="expiry_date" 
                                       value="<?php echo htmlspecialchars($document['expiry_date']); ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Current File</label>
                                <div>
                                    <?php if ($document['file_path'] && file_exists('../' . $document['file_path'])): ?>
                                        <a href="../<?php echo $document['file_path']; ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="bi bi-download"></i> View Current File
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No file uploaded</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="document_file" class="form-label">Upload New File (PDF, JPG, PNG)</label>
                                <input type="file" class="form-control" id="document_file" name="document_file" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Leave empty to keep current file. Maximum file size: 10MB</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="../records/view.php?id=<?php echo $document['bac_record_id']; ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Record
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
