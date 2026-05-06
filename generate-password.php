<?php
// File: generate-password.php
// Akses di browser: http://localhost/perpustakaan-sdn259/generate-password.php

$password = 'password123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<p><strong>Password:</strong> " . $password . "</p>";
echo "<p><strong>Hash:</strong> " . $hash . "</p>";
echo "<hr>";
echo "<h3>Copy SQL berikut:</h3>";
echo "<textarea style='width:100%; height:300px; font-family:monospace;'>";
echo "-- Update password untuk semua user\n\n";
echo "UPDATE users SET password = '$hash' WHERE username = 'admin';\n";
echo "UPDATE users SET password = '$hash' WHERE username = 'pustakawan';\n";
echo "UPDATE users SET password = '$hash' WHERE username = 'anggota';\n";
echo "</textarea>";
?>
