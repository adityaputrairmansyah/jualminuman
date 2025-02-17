<?php
session_start();
include 'config/database.php';

// Cek login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$query = "SELECT * FROM users WHERE id_user = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Update profil
if(isset($_POST['update_profile'])) {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    
    $update_query = "UPDATE users SET 
                    nama_lengkap = '$nama_lengkap',
                    email = '$email',
                    alamat = '$alamat'
                    WHERE id_user = '$user_id'";
    
    if(mysqli_query($conn, $update_query)) {
        $success = "Profil berhasil diperbarui!";
        // Refresh data user
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
    } else {
        $error = "Gagal memperbarui profil!";
    }
}

// Update password
if(isset($_POST['update_password'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    
    // Verifikasi password lama
    if(password_verify($password_lama, $user['password'])) {
        if($password_baru == $konfirmasi_password) {
            $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = '$hashed_password' WHERE id_user = '$user_id'";
            
            if(mysqli_query($conn, $update_query)) {
                $success_password = "Password berhasil diperbarui!";
            } else {
                $error_password = "Gagal memperbarui password!";
            }
        } else {
            $error_password = "Konfirmasi password tidak sesuai!";
        }
    } else {
        $error_password = "Password lama tidak sesuai!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Toko Minuman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-user-circle fa-5x text-primary mb-3"></i>
                        <h5 class="card-title"><?php echo $user['username']; ?></h5>
                        <p class="card-text text-muted">
                            <?php echo $user['role']; ?>
                        </p>
                        <p class="card-text">
                            <small class="text-muted">
                                Bergabung sejak: <?php echo date('d M Y', strtotime($user['created_at'])); ?>
                            </small>
                        </p>
                    </div>
                </div>

                <!-- Menu -->
                <div class="list-group">
                    <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="fas fa-user me-2"></i> Profil
                    </a>
                    <a href="#password" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-key me-2"></i> Ubah Password
                    </a>
                    <a href="pesanan_saya.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-bag me-2"></i> Pesanan Saya
                    </a>
                </div>
            </div>

            <div class="col-md-8">
                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Edit Profil</h5>
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
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" value="<?php echo $user['username']; ?>" disabled>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="nama_lengkap" class="form-control" 
                                               value="<?php echo $user['nama_lengkap']; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" 
                                               value="<?php echo $user['email']; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Alamat</label>
                                        <textarea name="alamat" class="form-control" rows="3"><?php echo $user['alamat']; ?></textarea>
                                    </div>

                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Password Tab -->
                    <div class="tab-pane fade" id="password">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Ubah Password</h5>
                            </div>
                            <div class="card-body">
                                <?php if(isset($success_password)): ?>
                                    <div class="alert alert-success"><?php echo $success_password; ?></div>
                                <?php endif; ?>

                                <?php if(isset($error_password)): ?>
                                    <div class="alert alert-danger"><?php echo $error_password; ?></div>
                                <?php endif; ?>

                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Password Lama</label>
                                        <input type="password" name="password_lama" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password Baru</label>
                                        <input type="password" name="password_baru" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" name="konfirmasi_password" class="form-control" required>
                                    </div>

                                    <button type="submit" name="update_password" class="btn btn-primary">
                                        <i class="fas fa-key me-1"></i> Ubah Password
                                    </button>
                                </form>
                            </div>
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