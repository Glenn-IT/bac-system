<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

// Check permission
checkPermission(['Admin', 'BAC Secretariat Staff']);

$page_title = "Add New Record";
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bac_cod_number = sanitize($_POST['bac_cod_number']);
    
    // Build full BAC COD
    $bac_cod = "PE " . $bac_cod_number;
    
    // Validate required fields
    if (empty($bac_cod_number)) {
        $error = "BAC COD Number is required.";
    } else {
        // Check if BAC COD already exists
        $check_stmt = $conn->prepare("SELECT id FROM bac_records WHERE bac_cod = ?");
        $check_stmt->bind_param("s", $bac_cod);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "A record with BAC COD '$bac_cod' already exists.";
        } else {
            // Insert into bac_records table
            $stmt = $conn->prepare("INSERT INTO bac_records (bac_cod, created_by) VALUES (?, ?)");
            $stmt->bind_param("si", $bac_cod, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                logActivity($conn, $_SESSION['user_id'], "Added new record: $bac_cod", 'Records');
                header("Location: list.php?success=added");
                exit();
            } else {
                $error = "Error adding record: " . $conn->error;
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
                    <i class="bi bi-plus-circle"></i> Add New Record
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
                                <label for="bac_cod_number" class="form-label">BAC COD <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">PE</span>
                                    <input type="text" class="form-control" id="bac_cod_number" name="bac_cod_number" 
                                           placeholder="25-001" 
                                           pattern="[0-9]{2}-[0-9]{3}"
                                           title="Format: 25-001 (YY-XXX)"
                                           value="<?php echo isset($_POST['bac_cod_number']) ? htmlspecialchars($_POST['bac_cod_number']) : ''; ?>" 
                                           required autofocus>
                                </div>
                                <small class="form-text text-muted">Format: YY-XXX (e.g., 25-001, 25-002, 25-003)</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="list.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
