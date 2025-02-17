<?php
session_start();
include 'config/database.php';

// Cek login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data keranjang
$user_id = $_SESSION['user_id'];
$query = "SELECT dp.*, p.nama_produk, p.gambar, p.stok 
          FROM detail_pesanan dp 
          JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan 
          JOIN produk p ON dp.id_produk = p.id_produk 
          WHERE ps.id_user = '$user_id' AND ps.status = 'pending'";
$result = mysqli_query($conn, $query);

// Update jumlah
if(isset($_POST['update'])) {
    $id_detail = $_POST['id_detail'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    
    $update_query = "UPDATE detail_pesanan SET jumlah = '$jumlah' WHERE id_detail = '$id_detail'";
    mysqli_query($conn, $update_query);
    
    // Update total harga di tabel pesanan
    $id_pesanan = $_POST['id_pesanan'];
    $update_total = "UPDATE pesanan SET total_harga = (
        SELECT SUM(jumlah * harga) 
        FROM detail_pesanan 
        WHERE id_pesanan = '$id_pesanan'
    ) WHERE id_pesanan = '$id_pesanan'";
    mysqli_query($conn, $update_total);
    
    header("Location: keranjang.php");
    exit();
}

// Hapus item
if(isset($_GET['delete'])) {
    $id_detail = $_GET['delete'];
    $id_pesanan = $_GET['id_pesanan'];
    
    mysqli_query($conn, "DELETE FROM detail_pesanan WHERE id_detail = '$id_detail'");
    
    // Update total harga
    $update_total = "UPDATE pesanan SET total_harga = (
        SELECT SUM(jumlah * harga) 
        FROM detail_pesanan 
        WHERE id_pesanan = '$id_pesanan'
    ) WHERE id_pesanan = '$id_pesanan'";
    mysqli_query($conn, $update_total);
    
    header("Location: keranjang.php");
    exit();
}

// Checkout
if(isset($_POST['checkout'])) {
    $id_pesanan = $_POST['id_pesanan'];
    
    // Update status pesanan
    mysqli_query($conn, "UPDATE pesanan SET status = 'dibayar' WHERE id_pesanan = '$id_pesanan'");
    
    // Update stok produk
    $items = mysqli_query($conn, "SELECT * FROM detail_pesanan WHERE id_pesanan = '$id_pesanan'");
    while($item = mysqli_fetch_assoc($items)) {
        mysqli_query($conn, "UPDATE produk SET stok = stok - {$item['jumlah']} 
                           WHERE id_produk = {$item['id_produk']}");
    }
    
    header("Location: pesanan_saya.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Toko Minuman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <h2 class="mb-4">Keranjang Belanja</h2>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        $id_pesanan = null;
                        while($row = mysqli_fetch_assoc($result)): 
                            $subtotal = $row['jumlah'] * $row['harga'];
                            $total += $subtotal;
                            $id_pesanan = $row['id_pesanan'];
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama_produk']; ?>" 
                                         class="product-img me-3">
                                    <div>
                                        <h6 class="mb-0"><?php echo $row['nama_produk']; ?></h6>
                                        <small class="text-muted">Stok: <?php echo $row['stok']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <form method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="id_detail" value="<?php echo $row['id_detail']; ?>">
                                    <input type="hidden" name="id_pesanan" value="<?php echo $row['id_pesanan']; ?>">
                                    <input type="hidden" name="harga" value="<?php echo $row['harga']; ?>">
                                    <input type="number" name="jumlah" value="<?php echo $row['jumlah']; ?>" 
                                           min="1" max="<?php echo $row['stok']; ?>" 
                                           class="form-control form-control-sm" style="width: 70px;">
                                    <button type="submit" name="update" class="btn btn-sm btn-secondary ms-2">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            </td>
                            <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                            <td>
                                <a href="?delete=<?php echo $row['id_detail']; ?>&id_pesanan=<?php echo $row['id_pesanan']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Yakin ingin menghapus item ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td class="fw-bold">Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <a href="produk.php" class="btn btn-secondary me-2">
                    <i class="fas fa-shopping-bag me-1"></i> Lanjut Belanja
                </a>
                <?php if($total > 0): ?>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="id_pesanan" value="<?php echo $id_pesanan; ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-1"></i> Checkout
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h4>Keranjang Belanja Kosong</h4>
                <p class="text-muted">Belum ada produk yang ditambahkan ke keranjang</p>
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