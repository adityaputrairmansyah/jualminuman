<?php
session_start();
include '../config/database.php';

// Cek apakah user sudah login dan role-nya admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Ambil data kategori
$kategori_query = "SELECT * FROM kategori";
$kategori_result = mysqli_query($conn, $kategori_query);

if(isset($_POST['submit'])) {
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $id_kategori = $_POST['id_kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    // Upload gambar
    $target_dir = "../assets/images/products/";
    if(!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $gambar = $target_dir . basename($_FILES["gambar"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($gambar,PATHINFO_EXTENSION));
    
    // Cek apakah file adalah gambar
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $error = "File bukan gambar.";
            $uploadOk = 0;
        }
    }
    
    // Cek ukuran file
    if ($_FILES["gambar"]["size"] > 500000) {
        $error = "Ukuran file terlalu besar.";
        $uploadOk = 0;
    }
    
    // Format yang diizinkan
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $error = "Hanya file JPG, JPEG, PNG yang diizinkan.";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 0) {
        $error = "File gagal diupload.";
    } else {
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $gambar)) {
            $gambar_path = "assets/images/products/" . basename($_FILES["gambar"]["name"]);
            
            $query = "INSERT INTO produk (nama_produk, id_kategori, harga, stok, deskripsi, gambar) 
                     VALUES ('$nama_produk', '$id_kategori', '$harga', '$stok', '$deskripsi', '$gambar_path')";
            
            if(mysqli_query($conn, $query)) {
                header("Location: produk.php");
                exit();
            } else {
                $error = "Gagal menambahkan produk!";
            }
        } else {
            $error = "Gagal mengupload file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Navbar Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="produk.php">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kategori.php">Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pesanan.php">Pesanan</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Keluar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tambah Produk Baru</h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" name="nama_produk" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="id_kategori" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php while($kategori = mysqli_fetch_assoc($kategori_result)): ?>
                                        <option value="<?php echo $kategori['id_kategori']; ?>">
                                            <?php echo $kategori['nama_kategori']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Harga</label>
                                <input type="number" name="harga" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Stok</label>
                                <input type="number" name="stok" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Gambar Produk</label>
                                <input type="file" name="gambar" class="form-control" required>
                                <small class="text-muted">Format: JPG, JPEG, PNG. Max: 500KB</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Simpan Produk
                                </button>
                                <a href="produk.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 