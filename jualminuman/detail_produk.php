<?php
session_start();
include 'config/database.php';

if(!isset($_GET['id'])) {
    header("Location: produk.php");
    exit();
}

$id_produk = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT p.*, k.nama_kategori 
          FROM produk p 
          LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
          WHERE p.id_produk = '$id_produk'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 0) {
    header("Location: produk.php");
    exit();
}

$produk = mysqli_fetch_assoc($result);

// Handle add to cart
if(isset($_POST['add_to_cart']) && isset($_SESSION['user_id'])) {
    $jumlah = $_POST['jumlah'];
    $user_id = $_SESSION['user_id'];
    
    // Check if there's an active cart
    $cart_query = "SELECT id_pesanan FROM pesanan 
                   WHERE id_user = '$user_id' AND status = 'pending'";
    $cart_result = mysqli_query($conn, $cart_query);
    
    if(mysqli_num_rows($cart_result) > 0) {
        $cart = mysqli_fetch_assoc($cart_result);
        $id_pesanan = $cart['id_pesanan'];
    } else {
        // Create new cart
        mysqli_query($conn, "INSERT INTO pesanan (id_user, status) VALUES ('$user_id', 'pending')");
        $id_pesanan = mysqli_insert_id($conn);
    }
    
    // Check if product already in cart
    $check_query = "SELECT * FROM detail_pesanan dp 
                    JOIN pesanan p ON dp.id_pesanan = p.id_pesanan 
                    WHERE p.id_user = '$user_id' AND p.status = 'pending' 
                    AND dp.id_produk = '$id_produk'";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result) > 0) {
        // Update quantity
        $item = mysqli_fetch_assoc($check_result);
        $new_jumlah = $item['jumlah'] + $jumlah;
        mysqli_query($conn, "UPDATE detail_pesanan 
                           SET jumlah = '$new_jumlah' 
                           WHERE id_detail = '{$item['id_detail']}'");
    } else {
        // Add new item
        $harga = $produk['harga'];
        mysqli_query($conn, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga) 
                           VALUES ('$id_pesanan', '$id_produk', '$jumlah', '$harga')");
    }
    
    // Update total
    mysqli_query($conn, "UPDATE pesanan SET total_harga = (
        SELECT SUM(jumlah * harga) 
        FROM detail_pesanan 
        WHERE id_pesanan = '$id_pesanan'
    ) WHERE id_pesanan = '$id_pesanan'");
    
    // Redirect ke keranjang setelah menambahkan produk
    header("Location: keranjang.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $produk['nama_produk']; ?> - Toko Minuman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .product-image {
            max-height: 400px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Include your navbar here -->
    
    <div class="container my-5">
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo $produk['gambar']; ?>" class="img-fluid rounded product-image" alt="<?php echo $produk['nama_produk']; ?>">
            </div>
            
            <div class="col-md-6">
                <h1><?php echo $produk['nama_produk']; ?></h1>
                <p class="text-muted"><?php echo $produk['nama_kategori']; ?></p>
                
                <h3 class="text-primary">
                    Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                </h3>
                
                <div class="my-4">
                    <h5>Deskripsi</h5>
                    <p><?php echo nl2br($produk['deskripsi']); ?></p>
                </div>
                
                <div class="mb-4">
                    <h5>Stok</h5>
                    <p><?php echo $produk['stok']; ?> unit</p>
                </div>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <form method="POST" class="mb-3">
                        <div class="input-group mb-3">
                            <input type="number" name="jumlah" class="form-control" value="1" min="1" max="<?php echo $produk['stok']; ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">
                                <i class="fas fa-cart-plus me-1"></i> Tambah ke Keranjang
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        Silakan <a href="login.php">login</a> untuk membeli produk ini.
                    </div>
                <?php endif; ?>
                
                <a href="produk.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Produk
                </a>
            </div>
        </div>
    </div>
    
    <!-- Include your footer here -->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 