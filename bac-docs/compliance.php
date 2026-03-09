<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

$page_title = "BAC Compliance Check";

// Get selected BAC record
$bac_record_id = isset($_GET['bac_record_id']) ? (int)$_GET['bac_record_id'] : 0;

// Get all BAC records
$bac_records = $conn->query("SELECT id, bac_cod FROM bac_records ORDER BY bac_cod");

// Get all document types (only categorized ones: II and III)
$doc_types_query = $conn->query("SELECT * FROM doc_types WHERE category IS NOT NULL AND category != '' ORDER BY category, sort_order, document_name");
$doc_types = [];
while ($dt = $doc_types_query->fetch_assoc()) {
    $doc_types[] = $dt;
}

// Get compliance data if BAC record is selected
$compliance_data = [];
if ($bac_record_id > 0) {
    // Get BAC record info
    $bac_stmt = $conn->prepare("SELECT br.*, u.full_name as created_by_name 
                                 FROM bac_records br 
                                 LEFT JOIN users u ON br.created_by = u.id 
                                 WHERE br.id = ?");
    $bac_stmt->bind_param("i", $bac_record_id);
    $bac_stmt->execute();
    $bac_info = $bac_stmt->get_result()->fetch_assoc();
    $bac_stmt->close();
    
    // Get documents for this BAC record
    foreach ($doc_types as $doc_type) {
        $stmt = $conn->prepare("SELECT * FROM bac_documents WHERE bac_record_id = ? AND doc_type_id = ? ORDER BY uploaded_at DESC LIMIT 1");
        $stmt->bind_param("ii", $bac_record_id, $doc_type['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $doc = $result->fetch_assoc();
            // Update status
            updateBacDocumentStatus($conn, $doc['id']);
            // Refresh data
            $stmt2 = $conn->prepare("SELECT * FROM bac_documents WHERE id = ?");
            $stmt2->bind_param("i", $doc['id']);
            $stmt2->execute();
            $doc = $stmt2->get_result()->fetch_assoc();
            $stmt2->close();
        } else {
            $doc = null;
        }
        
        $compliance_data[] = [
            'doc_type' => $doc_type,
            'document' => $doc
        ];
        
        $stmt->close();
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-clipboard-check"></i> BAC Document Compliance Check
        </div>
        <div class="card-body">
            <!-- BAC Record Selection -->
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <label for="bac_record_id" class="form-label">Select BAC Record to View Compliance</label>
                        <div class="input-group">
                            <select class="form-select" id="bac_record_id" name="bac_record_id" required>
                                <option value="">-- Select BAC Record --</option>
                                <?php while ($rec = $bac_records->fetch_assoc()): ?>
                                    <option value="<?php echo $rec['id']; ?>" 
                                        <?php echo ($bac_record_id == $rec['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($rec['bac_cod']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Check Compliance
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            
            <?php if ($bac_record_id > 0 && isset($bac_info)): ?>
                <!-- BAC Record Information -->
                <div class="alert alert-info">
                    <h5 class="alert-heading">
                        <i class="bi bi-file-earmark-plus"></i> <?php echo htmlspecialchars($bac_info['bac_cod']); ?>
                    </h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Created Date:</strong> <?php echo formatDate($bac_info['created_at']); ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Created By:</strong> <?php echo htmlspecialchars($bac_info['created_by_name']); ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Last Updated:</strong> <?php echo formatDate($bac_info['updated_at']); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Compliance Summary -->
                <?php
                $total_docs = count($compliance_data);
                $valid_count = 0;
                $expired_count = 0;
                $renewal_count = 0;
                $missing_count = 0;
                
                foreach ($compliance_data as $item) {
                    if ($item['document']) {
                        $status = $item['document']['status'];
                        if ($status == 'Valid') $valid_count++;
                        elseif ($status == 'Expired') $expired_count++;
                        elseif ($status == 'For Renewal') $renewal_count++;
                        else $missing_count++;
                    } else {
                        $missing_count++;
                    }
                }
                
                $compliance_percentage = $total_docs > 0 ? ($valid_count / $total_docs) * 100 : 0;
                ?>
                
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $valid_count; ?>/<?php echo $total_docs; ?></h3>
                                <p class="mb-0">Valid Documents</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h3><?php echo $renewal_count; ?></h3>
                                <p class="mb-0">For Renewal</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $expired_count; ?></h3>
                                <p class="mb-0">Expired</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $missing_count; ?></h3>
                                <p class="mb-0">Missing</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Compliance Percentage -->
                <div class="mb-4">
                    <label class="form-label">Compliance Rate: <?php echo number_format($compliance_percentage, 1); ?>%</label>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar <?php echo $compliance_percentage >= 80 ? 'bg-success' : ($compliance_percentage >= 50 ? 'bg-warning' : 'bg-danger'); ?>" 
                             role="progressbar" 
                             style="width: <?php echo $compliance_percentage; ?>%;" 
                             aria-valuenow="<?php echo $compliance_percentage; ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <?php echo number_format($compliance_percentage, 1); ?>%
                        </div>
                    </div>
                </div>
                
                <!-- Checklist Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="40">#</th>
                                <th>Document Type</th>
                                <th>Required</th>
                                <th>Issued Date</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th>File</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            $current_category = null;
                            $letter_map = ['II. Eligibility and Technical Documents' => 'a', 'III. Financial Documents' => 'a'];
                            foreach ($compliance_data as $item): 
                                $item_category = $item['doc_type']['category'] ?? null;
                                // Print category header row when category changes
                                if ($item_category !== $current_category):
                                    $current_category = $item_category;
                            ?>
                                <tr class="table-dark">
                                    <td colspan="8">
                                        <strong><i class="bi bi-folder2-open"></i> <?php echo htmlspecialchars($current_category); ?></strong>
                                    </td>
                                </tr>
                            <?php endif; ?>
                                <tr>
                                    <td class="text-center">
                                        <small class="text-muted"><?php 
                                            // Use letter labeling within each category
                                            static $cat_counters = [];
                                            if (!isset($cat_counters[$item_category])) $cat_counters[$item_category] = 0;
                                            $letter = chr(ord('a') + $cat_counters[$item_category]);
                                            $cat_counters[$item_category]++;
                                            echo $letter . '.';
                                        ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['doc_type']['document_name']); ?></strong>
                                        <?php if ($item['doc_type']['description']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($item['doc_type']['description']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($item['doc_type']['is_required']): ?>
                                            <span class="badge bg-danger">Required</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Optional</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $item['document'] ? formatDate($item['document']['issued_date']) : '-'; ?>
                                    </td>
                                    <td>
                                        <?php echo $item['document'] ? formatDate($item['document']['expiry_date']) : '-'; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['document']): ?>
                                            <span class="badge <?php echo getStatusBadge($item['document']['status']); ?>">
                                                <?php echo $item['document']['status']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Missing</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['document'] && $item['document']['file_path'] && file_exists('../' . $item['document']['file_path'])): ?>
                                            <a href="../<?php echo $item['document']['file_path']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="bi bi-download"></i> View
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No file</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (in_array($_SESSION['user_role'], ['Admin', 'BAC Secretariat Staff'])): ?>
                                            <?php if ($item['document']): ?>
                                                <a href="edit.php?id=<?php echo $item['document']['id']; ?>" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            <?php else: ?>
                                                <a href="add.php?bac_record_id=<?php echo $bac_record_id; ?>" class="btn btn-success btn-sm">
                                                    <i class="bi bi-plus"></i> Add
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Print Button -->
                <div class="mt-3">
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="bi bi-printer"></i> Print Compliance Report
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .top-navbar, .btn, form { display: none !important; }
    .main-content { margin-left: 0 !important; }
}
</style>

<?php include '../includes/footer.php'; ?>
