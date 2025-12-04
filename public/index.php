<?php
session_start();
require_once '../config/db.php';

// AUTO-LOGIN AS ADMIN (Login disabled)
if (!isset($_SESSION['user_id'])) {
    // Automatically log in as admin
    $stmt = $conn->prepare("SELECT id, username, full_name, role FROM users WHERE username = 'admin' AND status = 'Active'");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        
        // Log activity
        logActivity($conn, $user['id'], 'Auto-login as admin', 'Authentication');
    }
}

// Redirect to dashboard
header("Location: dashboard.php");
exit();
?>
