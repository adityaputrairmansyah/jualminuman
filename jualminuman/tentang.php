<?php
session_start();
include 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Toko Minuman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .about-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/images/about-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-top: -16px;
        }
        
        .feature-box {
            text-align: center;
            padding: 30px;
            margin-bottom: 30px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: transform 0.3s;
        }
        
        .feature-box:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: #0d6efd;
            margin-bottom: 20px;
        }
        
        .team-member {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 15px;
            object-fit: cover;
        }
        
        .social-links a {
            color: #0d6efd;
            margin: 0 10px;
            font-size: 1.2rem;
        }
        
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        
        .timeline-item {
            padding: 20px;
            border-left: 2px solid #0d6efd;
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -9px;
            top: 28px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #0d6efd;
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
                        <a class="nav-link active" href="tentang.php"><i class="fas fa-info-circle me-1"></i> Tentang</a>
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

    <!-- Header -->
    <header class="about-header">
        <div class="container text-center">
            <h1 class="display-4 mb-4">Tentang Kami</h1>
            <p class="lead">Menyajikan berbagai minuman berkualitas untuk menemani hari-hari Anda</p>
        </div>
    </header>

    <!-- Visi Misi -->
    <section class="container my-5">
        <div class="row">
            <div class="col-md-6 mb-4">
                <h2 class="mb-4">Visi</h2>
                <p class="lead">Menjadi toko minuman online terpercaya dan terdepan di Indonesia dengan menyediakan produk berkualitas dan pelayanan terbaik.</p>
            </div>
            <div class="col-md-6 mb-4">
                <h2 class="mb-4">Misi</h2>
                <ul class="lead">
                    <li>Menyediakan berbagai minuman berkualitas</li>
                    <li>Memberikan pelayanan terbaik kepada pelanggan</li>
                    <li>Mengutamakan kepuasan pelanggan</li>
                    <li>Mengembangkan inovasi produk</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Keunggulan -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Keunggulan Kami</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="fas fa-check-circle feature-icon"></i>
                        <h4>Produk Berkualitas</h4>
                        <p>Kami hanya menyediakan produk-produk berkualitas tinggi dari supplier terpercaya.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="fas fa-shipping-fast feature-icon"></i>
                        <h4>Pengiriman Cepat</h4>
                        <p>Layanan pengiriman cepat ke seluruh wilayah Indonesia dengan packaging yang aman.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="fas fa-headset feature-icon"></i>
                        <h4>Layanan 24/7</h4>
                        <p>Tim customer service kami siap melayani Anda 24 jam setiap hari.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sejarah -->
    <section class="container my-5">
        <h2 class="text-center mb-5">Perjalanan Kami</h2>
        <div class="timeline">
            <div class="timeline-item">
                <h4>2020</h4>
                <p>Toko Minuman didirikan sebagai toko offline di kota kami.</p>
            </div>
            <div class="timeline-item">
                <h4>2021</h4>
                <p>Mengembangkan platform online untuk menjangkau lebih banyak pelanggan.</p>
            </div>
            <div class="timeline-item">
                <h4>2022</h4>
                <p>Memperluas jangkauan pengiriman ke seluruh Indonesia.</p>
            </div>
            <div class="timeline-item">
                <h4>2023</h4>
                <p>Mencapai 10.000+ pelanggan setia dan terus berkembang.</p>
            </div>
        </div>
    </section>

    <!-- Tim Kami -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Tim Kami</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="team-member">
                        <img src="assets/images/team1.jpg" alt="CEO">
                        <h4>John Doe</h4>
                        <p class="text-muted">CEO & Founder</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <img src="assets/images/team2.jpg" alt="Manager">
                        <h4>Jane Smith</h4>
                        <p class="text-muted">Operations Manager</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <img src="assets/images/team3.jpg" alt="Marketing">
                        <h4>Mike Johnson</h4>
                        <p class="text-muted">Marketing Director</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
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