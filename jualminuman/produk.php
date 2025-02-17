<?php
session_start();
include 'config/database.php';

// Pagination
$limit = 12;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Filter kategori
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$where = $kategori ? "WHERE id_kategori = $kategori" : "";

$query = "SELECT p.*, k.nama_kategori 
          FROM produk p 
          LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
          $where 
          LIMIT $start, $limit";
$result = mysqli_query($conn, $query);

// Get total records for pagination
$total_query = "SELECT COUNT(*) as total FROM produk $where";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_pages = ceil($total_row['total'] / $limit);

// Get categories for filter
$kategori_query = "SELECT * FROM kategori";
$kategori_result = mysqli_query($conn, $kategori_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Toko Minuman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .product-card {
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-img {
            height: 200px;
            object-fit: cover;
        }
        .category-nav {
            background: #f8f9fa;
            padding: 15px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Category Navigation -->
    <div class="category-nav">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Produk Kami</h4>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" 
                            id="categoryDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i>
                        <?php echo $kategori ? 'Kategori: ' . mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = $kategori"))['nama_kategori'] : 'Semua Kategori'; ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                        <li>
                            <a class="dropdown-item <?php echo !$kategori ? 'active' : ''; ?>" href="produk.php">
                                Semua Kategori
                            </a>
                        </li>
                        <?php 
                        mysqli_data_seek($kategori_result, 0);
                        while($kat = mysqli_fetch_assoc($kategori_result)): 
                        ?>
                        <li>
                            <a class="dropdown-item <?php echo $kategori == $kat['id_kategori'] ? 'active' : ''; ?>" 
                               href="?kategori=<?php echo $kat['id_kategori']; ?>">
                                <?php echo $kat['nama_kategori']; ?>
                            </a>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Products Grid -->
        <div class="row">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-3 mb-4">
                <div class="card product-card h-100">
                    <img src="<?php echo $row['gambar']; ?>" class="card-img-top product-img" 
                         alt="<?php echo $row['nama_produk']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['nama_produk']; ?></h5>
                        <p class="card-text text-muted small">
                            <?php echo $row['nama_kategori']; ?>
                        </p>
                        <p class="card-text text-primary fw-bold">
                            Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                        </p>
                        <div class="d-grid">
                            <a href="detail_produk.php?id=<?php echo $row['id_produk']; ?>" 
                               class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i> Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="my-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo $kategori ? '&kategori='.$kategori : ''; ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $kategori ? '&kategori='.$kategori : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
                
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo $kategori ? '&kategori='.$kategori : ''; ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 