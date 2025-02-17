<?php
session_start();
include 'config/database.php';

// Cek login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil semua pesanan user
$query = "SELECT p.*, COUNT(dp.id_detail) as total_items 
          FROM pesanan p 
          LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan 
          WHERE p.id_user = '$user_id' 
          GROUP BY p.id_pesanan 
          ORDER BY p.tanggal_pesanan DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Toko Minuman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .order-status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-dibayar { background: #cce5ff; color: #004085; }
        .status-dikirim { background: #d4edda; color: #155724; }
        .status-selesai { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <h2 class="mb-4">Pesanan Saya</h2>

        <?php if(mysqli_num_rows($result) > 0): ?>
            <div class="row">
                <?php while($pesanan = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-shopping-bag me-2"></i>
                                    Pesanan #<?php echo $pesanan['id_pesanan']; ?>
                                </span>
                                <span class="order-status status-<?php echo strtolower($pesanan['status']); ?>">
                                    <?php echo ucfirst($pesanan['status']); ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <?php
                                // Ambil detail pesanan
                                $id_pesanan = $pesanan['id_pesanan'];
                                $detail_query = "SELECT dp.*, p.nama_produk, p.gambar 
                                               FROM detail_pesanan dp 
                                               JOIN produk p ON dp.id_produk = p.id_produk 
                                               WHERE dp.id_pesanan = '$id_pesanan'";
                                $detail_result = mysqli_query($conn, $detail_query);
                                ?>

                                <div class="mb-3">
                                    <small class="text-muted">
                                        Tanggal Pesanan: <?php echo date('d M Y H:i', strtotime($pesanan['tanggal_pesanan'])); ?>
                                    </small>
                                </div>

                                <?php while($item = mysqli_fetch_assoc($detail_result)): ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="<?php echo $item['gambar']; ?>" 
                                             alt="<?php echo $item['nama_produk']; ?>"
                                             style="width: 50px; height: 50px; object-fit: cover;"
                                             class="me-3">
                                        <div>
                                            <h6 class="mb-0"><?php echo $item['nama_produk']; ?></h6>
                                            <small class="text-muted">
                                                <?php echo $item['jumlah']; ?> x 
                                                Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endwhile; ?>

                                <hr>

                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1">
                                            <strong>Total Items:</strong> <?php echo $pesanan['total_items']; ?>
                                        </p>
                                        <p class="mb-1">
                                            <strong>Total Harga:</strong> 
                                            Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?>
                                        </p>
                                        <p class="mb-1">
                                            <strong>Metode Pembayaran:</strong><br>
                                            <?php echo $pesanan['metode_pembayaran'] ?? '-'; ?>
                                        </p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1">
                                            <strong>Alamat Pengiriman:</strong><br>
                                            <small><?php echo $pesanan['alamat_pengiriman'] ?? '-'; ?></small>
                                        </p>
                                    </div>
                                </div>

                                <?php if($pesanan['status'] == 'dikirim'): ?>
                                    <div class="mt-3">
                                        <form method="POST" action="konfirmasi_terima.php">
                                            <input type="hidden" name="id_pesanan" value="<?php echo $pesanan['id_pesanan']; ?>">
                                            <button type="submit" name="konfirmasi_terima" class="btn btn-success btn-sm">
                                                <i class="fas fa-check me-1"></i> Konfirmasi Penerimaan
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                <h4>Belum ada pesanan</h4>
                <p class="text-muted">Anda belum memiliki riwayat pesanan</p>
                <a href="produk.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-1"></i> Mulai Belanja
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 