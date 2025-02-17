<?php
session_start();
include '../config/database.php';

// Cek apakah user sudah login dan role-nya admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Hapus kategori
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM kategori WHERE id_kategori = '$id'";
    if(mysqli_query($conn, $query)) {
        $success = "Kategori berhasil dihapus!";
    } else {
        $error = "Gagal menghapus kategori!";
    }
}

// Tambah kategori
if(isset($_POST['tambah'])) {
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
    if(mysqli_query($conn, $query)) {
        $success = "Kategori berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan kategori!";
    }
}

// Edit kategori
if(isset($_POST['edit'])) {
    $id_kategori = $_POST['id_kategori'];
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $query = "UPDATE kategori SET nama_kategori = '$nama_kategori' WHERE id_kategori = '$id_kategori'";
    if(mysqli_query($conn, $query)) {
        $success = "Kategori berhasil diupdate!";
    } else {
        $error = "Gagal mengupdate kategori!";
    }
}

// Ambil data kategori
$query = "SELECT * FROM kategori ORDER BY id_kategori DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Admin</title>
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
                        <a class="nav-link" href="produk.php">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="kategori.php">Kategori</a>
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
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tambah Kategori</h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if(isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Nama Kategori</label>
                                <input type="text" name="nama_kategori" class="form-control" required>
                            </div>
                            <button type="submit" name="tambah" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Tambah Kategori
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Daftar Kategori</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Kategori</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['id_kategori']; ?></td>
                                        <td>
                                            <span class="kategori-text"><?php echo $row['nama_kategori']; ?></span>
                                            <form class="edit-form d-none" method="POST" action="">
                                                <div class="input-group">
                                                    <input type="hidden" name="id_kategori" value="<?php echo $row['id_kategori']; ?>">
                                                    <input type="text" name="nama_kategori" class="form-control form-control-sm" 
                                                           value="<?php echo $row['nama_kategori']; ?>" required>
                                                    <button type="submit" name="edit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-secondary cancel-edit">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning edit-btn">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="?delete=<?php echo $row['id_kategori']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit kategori inline
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                row.querySelector('.kategori-text').classList.add('d-none');
                row.querySelector('.edit-form').classList.remove('d-none');
            });
        });

        // Cancel edit
        document.querySelectorAll('.cancel-edit').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                row.querySelector('.kategori-text').classList.remove('d-none');
                row.querySelector('.edit-form').classList.add('d-none');
            });
        });
    </script>
</body>
</html> 