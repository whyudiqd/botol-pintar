<?php
$servername = "mysql.railway.internal";
$username = "root"; // Default username XAMPP
$password = "gGwsOgvwiYdjJwjkRWTzLKAAYcXgolRh";     // Default password XAMPP
$dbname = "railway";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

<!-- mysql://root:gGwsOgvwiYdjJwjkRWTzLKAAYcXgolRh@switchback.proxy.rlwy.net:22513/railway -->