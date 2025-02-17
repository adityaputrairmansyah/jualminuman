<?php
session_start();
include 'config/database.php';

// Cek login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah ada id_pesanan yang dikirim
if(!isset($_POST['id_pesanan']) && !isset($_GET['id'])) {
    header("Location: keranjang.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id_pesanan = isset($_POST['id_pesanan']) ? $_POST['id_pesanan'] : $_GET['id'];

// Ambil data pesanan
$query = "SELECT p.*, u.nama_lengkap, u.email, u.alamat 
          FROM pesanan p 
          JOIN users u ON p.id_user = u.id_user 
          WHERE p.id_pesanan = '$id_pesanan' AND p.id_user = '$user_id' AND p.status = 'pending'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "Pesanan tidak ditemukan atau sudah diproses!";
    header("Location: keranjang.php");
    exit();
}

$pesanan = mysqli_fetch_assoc($result);

// Ambil detail pesanan
$detail_query = "SELECT dp.*, p.nama_produk, p.gambar, p.stok 
                FROM detail_pesanan dp 
                JOIN produk p ON dp.id_produk = p.id_produk 
                WHERE dp.id_pesanan = '$id_pesanan'";
$detail_result = mysqli_query($conn, $detail_query);

// Proses checkout
if(isset($_POST['konfirmasi_pembayaran'])) {
    $metode_pembayaran = mysqli_real_escape_string($conn, $_POST['metode_pembayaran']);
    $alamat_pengiriman = mysqli_real_escape_string($conn, $_POST['alamat_pengiriman']);
    $catatan = isset($_POST['catatan']) ? mysqli_real_escape_string($conn, $_POST['catatan']) : '';
    
    // Cek stok sebelum proses
    $stok_cukup = true;
    $error_stok = '';
    
    $items = mysqli_query($conn, "SELECT dp.*, p.stok, p.nama_produk 
                                 FROM detail_pesanan dp 
                                 JOIN produk p ON dp.id_produk = p.id_produk 
                                 WHERE dp.id_pesanan = '$id_pesanan'");
    
    while($item = mysqli_fetch_assoc($items)) {
        if($item['jumlah'] > $item['stok']) {
            $stok_cukup = false;
            $error_stok = "Stok {$item['nama_produk']} tidak mencukupi!";
            break;
        }
    }
    
    if($stok_cukup) {
        try {
            // Mulai transaksi
            mysqli_begin_transaction($conn);
            
            // Update pesanan
            $update_query = "UPDATE pesanan SET 
                            status = 'dibayar',
                            metode_pembayaran = '$metode_pembayaran',
                            alamat_pengiriman = '$alamat_pengiriman',
                            catatan = '$catatan',
                            tanggal_pembayaran = NOW()
                            WHERE id_pesanan = '$id_pesanan'";
            
            if(!mysqli_query($conn, $update_query)) {
                throw new Exception("Gagal mengupdate pesanan");
            }
            
            // Update stok produk
            $items = mysqli_query($conn, "SELECT dp.*, p.stok 
                                        FROM detail_pesanan dp 
                                        JOIN produk p ON dp.id_produk = p.id_produk 
                                        WHERE dp.id_pesanan = '$id_pesanan'");
            
            while($item = mysqli_fetch_assoc($items)) {
                $update_stok = "UPDATE produk 
                               SET stok = stok - {$item['jumlah']} 
                               WHERE id_produk = {$item['id_produk']}";
                               
                if(!mysqli_query($conn, $update_stok)) {
                    throw new Exception("Gagal mengupdate stok");
                }
            }
            
            // Commit transaksi
            mysqli_commit($conn);
            
            $_SESSION['success'] = "Pesanan berhasil dikonfirmasi!";
            header("Location: invoice.php?id=" . $id_pesanan);
            exit();
            
        } catch (Exception $e) {
            // Rollback jika terjadi error
            mysqli_rollback($conn);
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    } else {
        $error = $error_stok;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Toko Minuman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }
        .payment-method {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover {
            border-color: #0d6efd;
        }
        .payment-method.selected {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-8">
                <!-- Detail Pesanan -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Detail Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total = 0;
                                    while($item = mysqli_fetch_assoc($detail_result)): 
                                        $subtotal = $item['jumlah'] * $item['harga'];
                                        $total += $subtotal;
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $item['gambar']; ?>" 
                                                     alt="<?php echo $item['nama_produk']; ?>" 
                                                     class="product-img me-3">
                                                <div>
                                                    <h6 class="mb-0"><?php echo $item['nama_produk']; ?></h6>
                                                    <small class="text-muted">Stok: <?php echo $item['stok']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                        <td><?php echo $item['jumlah']; ?></td>
                                        <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <tr class="table-light">
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td class="fw-bold">Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Form Checkout -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="id_pesanan" value="<?php echo $id_pesanan; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <div class="payment-method" onclick="selectPayment('transfer')">
                                    <input type="radio" name="metode_pembayaran" value="transfer" required>
                                    <i class="fas fa-university me-2"></i> Transfer Bank
                                </div>
                                <div class="payment-method" onclick="selectPayment('cod')">
                                    <input type="radio" name="metode_pembayaran" value="cod" required>
                                    <i class="fas fa-money-bill-wave me-2"></i> Cash on Delivery (COD)
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat Pengiriman</label>
                                <textarea name="alamat_pengiriman" class="form-control" rows="3" required><?php echo $pesanan['alamat']; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="catatan" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="konfirmasi_pembayaran" class="btn btn-primary">
                                    <i class="fas fa-check me-1"></i> Konfirmasi Pembayaran
                                </button>
                                <a href="keranjang.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Keranjang
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPayment(method) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selected class to clicked payment method
            const selectedMethod = document.querySelector(`.payment-method input[value="${method}"]`);
            selectedMethod.checked = true;
            selectedMethod.closest('.payment-method').classList.add('selected');
        }
    </script>
</body>
</html> 