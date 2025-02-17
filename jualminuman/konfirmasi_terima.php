<?php
session_start();
include 'config/database.php';

if(!isset($_SESSION['user_id']) || !isset($_POST['konfirmasi_terima'])) {
    header("Location: pesanan_saya.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id_pesanan = $_POST['id_pesanan'];

// Verifikasi pesanan milik user yang login
$query = "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan' AND id_user = '$user_id' AND status = 'dikirim'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0) {
    // Update status pesanan menjadi selesai
    mysqli_query($conn, "UPDATE pesanan SET status = 'selesai' WHERE id_pesanan = '$id_pesanan'");
    $_SESSION['success'] = "Pesanan berhasil dikonfirmasi!";
} else {
    $_SESSION['error'] = "Pesanan tidak valid!";
}

header("Location: pesanan_saya.php");
exit(); 