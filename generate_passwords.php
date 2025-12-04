<?php
/**
 * Password Hash Generator
 * Run this file to generate correct password hashes for the database
 */

echo "BAC SYSTEM - Password Hash Generator\n";
echo "=====================================\n\n";

$passwords = [
    'admin123' => 'Admin password',
    'secret123' => 'Secretariat password',
    'member123' => 'Committee Member password',
    'audit123' => 'Auditor password'
];

foreach ($passwords as $password => $label) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "$label ($password):\n";
    echo "$hash\n\n";
}

echo "=====================================\n";
echo "Copy these hashes to database.sql\n";
?>
