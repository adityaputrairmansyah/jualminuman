<?php
// Tambahkan di baris paling atas
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'config/database.php';

// Cek koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek session
echo "<!-- Session: ";
print_r($_SESSION);
echo " -->";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Minuman Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Custom CSS */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-top: -16px;
        }
        
        .card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .category-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .category-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 15px;
        }
        
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 60px 0 30px;
        }
        
        .social-icons a {
            color: white;
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .product-card img {
            height: 200px;
            object-fit: cover;
        }
        
        .features-section {
            background: #f8f9fa;
            padding: 60px 0;
        }
        
        .feature-box {
            text-align: center;
            padding: 20px;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-glass-cheers me-2"></i>
                Toko Minuman
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="produk.php"><i class="fas fa-store me-1"></i> Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tentang.php"><i class="fas fa-info-circle me-1"></i> Tentang</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="keranjang.php">
                                <i class="fas fa-shopping-cart me-1"></i> Keranjang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profil.php">
                                <i class="fas fa-user me-1"></i> Profil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i> Keluar
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i> Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1"></i> Daftar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 mb-4">Selamat Datang di Toko Minuman Online</h1>
            <p class="lead mb-4">Temukan berbagai minuman segar dan berkualitas untuk menemani hari-hari Anda.</p>
            <a href="produk.php" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Belanja Sekarang
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="fas fa-truck feature-icon"></i>
                        <h4>Pengiriman Cepat</h4>
                        <p>Layanan pengiriman cepat ke seluruh wilayah</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="fas fa-shield-alt feature-icon"></i>
                        <h4>Produk Berkualitas</h4>
                        <p>Jaminan kualitas produk terbaik</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="fas fa-headset feature-icon"></i>
                        <h4>Layanan 24/7</h4>
                        <p>Dukungan pelanggan setiap saat</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kategori Section -->
    <section class="container my-5">
        <h2 class="text-center mb-4">Kategori Produk</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="category-card">
                    <i class="fas fa-wine-bottle category-icon"></i>
                    <h5>Minuman Botol</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="category-card">
                    <i class="fas fa-coffee category-icon"></i>
                    <h5>Kopi</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="category-card">
                    <i class="fas fa-glass-martini-alt category-icon"></i>
                    <h5>Jus Buah</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="category-card">
                    <i class="fas fa-mug-hot category-icon"></i>
                    <h5>Teh</h5>
                </div>
            </div>
        </div>
    </section>

    <!-- Produk Terbaru -->
    <section class="container my-5">
        <h2 class="text-center mb-4">Produk Terbaru</h2>
        <div class="row">
            <?php
            $query = "SELECT * FROM produk ORDER BY id_produk DESC LIMIT 4";
            $result = mysqli_query($conn, $query);
            while($row = mysqli_fetch_assoc($result)):
            ?>
            <div class="col-md-3 mb-4">
                <div class="card product-card">
                    <img src="<?php echo $row['gambar']; ?>" class="card-img-top" alt="<?php echo $row['nama_produk']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['nama_produk']; ?></h5>
                        <p class="card-text text-primary fw-bold">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                        <div class="d-grid">
                            <a href="detail_produk.php?id=<?php echo $row['id_produk']; ?>" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i> Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>Toko Minuman Online</h5>
                    <p>Menyediakan berbagai minuman berkualitas untuk Anda.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Link Cepat</h5>
                    <ul class="list-unstyled">
                        <li><a href="tentang.php" class="text-white">Tentang Kami</a></li>
                        <li><a href="produk.php" class="text-white">Produk</a></li>
                        <li><a href="kontak.php" class="text-white">Kontak</a></li>
                        <li><a href="kebijakan.php" class="text-white">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Kontak Kami</h5>
                    <p>
                        <i class="fas fa-map-marker-alt me-2"></i> Jl. Contoh No. 123, Kota<br>
                        <i class="fas fa-envelope me-2"></i> info@tokominuman.com<br>
                        <i class="fas fa-phone me-2"></i> (021) 1234567<br>
                        <i class="fas fa-clock me-2"></i> Senin - Minggu: 08:00 - 22:00
                    </p>
                </div>
            </div>
            <hr class="text-white-50">
            <div class="text-center text-white-50">
                <small>&copy; 2024 Toko Minuman Online. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 