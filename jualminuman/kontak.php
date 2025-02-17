<?php
session_start();
include 'config/database.php';

// Proses form kontak
if(isset($_POST['kirim_pesan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subjek = mysqli_real_escape_string($conn, $_POST['subjek']);
    $pesan = mysqli_real_escape_string($conn, $_POST['pesan']);
    
    // Simpan pesan ke database
    $query = "INSERT INTO pesan_kontak (nama, email, subjek, pesan, tanggal) 
              VALUES ('$nama', '$email', '$subjek', '$pesan', NOW())";
    
    if(mysqli_query($conn, $query)) {
        $success = "Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.";
    } else {
        $error = "Terjadi kesalahan! Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami - Toko Minuman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .contact-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        .contact-info {
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 10px;
            height: 100%;
        }
        .map-container {
            position: relative;
            padding-bottom: 75%;
            height: 0;
            overflow: hidden;
        }
        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="contact-info text-center">
                    <i class="fas fa-map-marker-alt contact-icon"></i>
                    <h4>Alamat</h4>
                    <p class="mb-0">Jl. Contoh No. 123</p>
                    <p>Kota, Kode Pos</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="contact-info text-center">
                    <i class="fas fa-phone contact-icon"></i>
                    <h4>Telepon</h4>
                    <p class="mb-0">(021) 1234567</p>
                    <p>Senin - Minggu: 08:00 - 22:00</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="contact-info text-center">
                    <i class="fas fa-envelope contact-icon"></i>
                    <h4>Email</h4>
                    <p class="mb-0">info@tokominuman.com</p>
                    <p>cs@tokominuman.com</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Kirim Pesan</h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Subjek</label>
                                <input type="text" name="subjek" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Pesan</label>
                                <textarea name="pesan" class="form-control" rows="5" required></textarea>
                            </div>

                            <button type="submit" name="kirim_pesan" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lokasi Kami</h5>
                    </div>
                    <div class="card-body">
                        <div class="map-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.6664463317306!2d106.82496851476882!3d-6.175392395527383!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5d2e764b12d%3A0x3d2ad6e1e0e9bcc8!2sMonumen%20Nasional!5e0!3m2!1sid!2sid!4v1647887574811!5m2!1sid!2sid" 
                                    allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Ikuti Kami</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-around">
                            <a href="#" class="text-primary fs-2">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="#" class="text-danger fs-2">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="text-info fs-2">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="text-success fs-2">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 