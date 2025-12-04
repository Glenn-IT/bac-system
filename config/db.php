<?php
/**
 * Database Configuration
 * BAC Eligibilities Record Keeping System
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bac_system');

// Create connection using MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

/**
 * Function to log activities
 */
function logActivity($conn, $user_id, $action, $module) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, module, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $action, $module, $ip_address);
    $stmt->execute();
    $stmt->close();
}

/**
 * Function to check user permission
 */
function checkPermission($allowed_roles) {
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowed_roles)) {
        header("Location: ../public/dashboard.php?error=access_denied");
        exit();
    }
}

/**
 * Function to sanitize input
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Function to update document status based on expiration date
 */
function updateDocumentStatus($conn, $doc_id) {
    $stmt = $conn->prepare("SELECT file_path, expiration_date FROM eligibility_docs WHERE id = ?");
    $stmt->bind_param("i", $doc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $doc = $result->fetch_assoc();
    $stmt->close();
    
    $status = 'Missing';
    
    if ($doc['file_path'] && file_exists('../' . $doc['file_path'])) {
        if ($doc['expiration_date']) {
            $expiry = new DateTime($doc['expiration_date']);
            $today = new DateTime();
            $thirty_days = new DateTime('+30 days');
            
            if ($expiry < $today) {
                $status = 'Expired';
            } elseif ($expiry <= $thirty_days) {
                $status = 'For Renewal';
            } else {
                $status = 'Valid';
            }
        } else {
            $status = 'Valid';
        }
    }
    
    $update_stmt = $conn->prepare("UPDATE eligibility_docs SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $status, $doc_id);
    $update_stmt->execute();
    $update_stmt->close();
    
    return $status;
}

/**
 * Function to update BAC document status based on expiration date
 */
function updateBacDocumentStatus($conn, $doc_id) {
    $stmt = $conn->prepare("SELECT file_path, expiry_date FROM bac_documents WHERE id = ?");
    $stmt->bind_param("i", $doc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $doc = $result->fetch_assoc();
    $stmt->close();
    
    $status = 'Missing';
    
    if ($doc['file_path'] && file_exists('../' . $doc['file_path'])) {
        if ($doc['expiry_date']) {
            $expiry = new DateTime($doc['expiry_date']);
            $today = new DateTime();
            $thirty_days = new DateTime('+30 days');
            
            if ($expiry < $today) {
                $status = 'Expired';
            } elseif ($expiry <= $thirty_days) {
                $status = 'For Renewal';
            } else {
                $status = 'Valid';
            }
        } else {
            $status = 'Valid';
        }
    }
    
    $update_stmt = $conn->prepare("UPDATE bac_documents SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $status, $doc_id);
    $update_stmt->execute();
    $update_stmt->close();
    
    return $status;
}

/**
 * Function to format date for display
 */
function formatDate($date) {
    if ($date) {
        return date('M d, Y', strtotime($date));
    }
    return 'N/A';
}

/**
 * Function to get status badge class
 */
function getStatusBadge($status) {
    switch ($status) {
        case 'Valid':
            return 'bg-success';
        case 'Expired':
            return 'bg-danger';
        case 'For Renewal':
            return 'bg-warning text-dark';
        case 'Missing':
            return 'bg-secondary';
        case 'Active':
            return 'bg-success';
        case 'Inactive':
            return 'bg-secondary';
        default:
            return 'bg-secondary';
    }
}
?>
