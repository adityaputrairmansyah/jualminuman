<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'jualminuman';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Tambahkan ini untuk debugging
mysqli_set_charset($conn, "utf8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
?> 