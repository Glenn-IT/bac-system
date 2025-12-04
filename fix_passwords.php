<?php
/**
 * FIX LOGIN PASSWORDS
 * Run this file ONCE after importing database.sql to fix the password hashes
 * 
 * How to use:
 * 1. Import database.sql in phpMyAdmin
 * 2. Open browser: http://localhost/bac-system/fix_passwords.php
 * 3. Passwords will be fixed automatically
 * 4. Login with: admin / admin123
 */

// Database configuration
$conn = new mysqli('localhost', 'root', '', 'bac_system');

if ($conn->connect_error) {
    die("Database connection failed. Make sure you imported database.sql first!");
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Fix BAC System Passwords</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: green; padding: 10px; background: #d4edda; border-radius: 5px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0; }
        .info { color: #004085; padding: 10px; background: #d1ecf1; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class='box'>
        <h1>🔧 BAC System - Password Fix Tool</h1>";

// Update passwords with correct hashes
$users = [
    ['admin', 'admin123', 'System Administrator'],
    ['secretariat', 'secret123', 'BAC Secretary'],
    ['member', 'member123', 'BAC Committee Member'],
    ['auditor', 'audit123', 'COA Auditor']
];

echo "<div class='info'><strong>Fixing password hashes...</strong></div>";

$success_count = 0;
foreach ($users as $user) {
    $username = $user[0];
    $password = $user[1];
    $fullname = $user[2];
    
    // Generate correct password hash
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Update the database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $hashed_password, $username);
    
    if ($stmt->execute()) {
        echo "<div class='success'>✓ Fixed password for: <strong>$username</strong> ($fullname)</div>";
        $success_count++;
    } else {
        echo "<div class='error'>✗ Failed to fix password for: $username</div>";
    }
    
    $stmt->close();
}

echo "<hr>";

if ($success_count == 4) {
    echo "<div class='success'><h2>✅ ALL PASSWORDS FIXED SUCCESSFULLY!</h2></div>";
    echo "<div class='info'><strong>You can now login with these credentials:</strong></div>";
    echo "<table>
            <tr>
                <th>Role</th>
                <th>Username</th>
                <th>Password</th>
            </tr>
            <tr>
                <td>Admin</td>
                <td><code>admin</code></td>
                <td><code>admin123</code></td>
            </tr>
            <tr>
                <td>BAC Secretariat Staff</td>
                <td><code>secretariat</code></td>
                <td><code>secret123</code></td>
            </tr>
            <tr>
                <td>BAC Committee Member</td>
                <td><code>member</code></td>
                <td><code>member123</code></td>
            </tr>
            <tr>
                <td>Auditor/COA</td>
                <td><code>auditor</code></td>
                <td><code>audit123</code></td>
            </tr>
          </table>";
    
    echo "<div class='info'>
            <strong>Next Steps:</strong><br>
            1. Go to: <a href='public/index.php'>Login Page</a><br>
            2. Login with: admin / admin123<br>
            3. You can delete this file (fix_passwords.php) after successful login
          </div>";
} else {
    echo "<div class='error'><strong>Some passwords failed to update. Please check your database connection.</strong></div>";
}

$conn->close();

echo "</div></body></html>";
?>
