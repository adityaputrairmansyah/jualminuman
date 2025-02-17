<?php
session_start();
include 'config/database.php';

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: pesanan_saya.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id_pesanan = $_GET['id'];

// Ambil data pesanan
$query = "SELECT p.*, u.nama_lengkap, u.email, u.alamat 
          FROM pesanan p 
          JOIN users u ON p.id_user = u.id_user 
          WHERE p.id_pesanan = '$id_pesanan' AND p.id_user = '$user_id'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 0) {
    header("Location: pesanan_saya.php");
    exit();
}

$pesanan = mysqli_fetch_assoc($result);

// Ambil detail pesanan
$detail_query = "SELECT dp.*, p.nama_produk 
                FROM detail_pesanan dp 
                JOIN produk p ON dp.id_produk = p.id_produk 
                WHERE dp.id_pesanan = '$id_pesanan'";
$detail_result = mysqli_query($conn, $detail_query);

// Data rekening bank
$bank_accounts = [
    'bca' => [
        'name' => 'BANK BCA',
        'account' => '8690333777',
        'holder' => 'PT TOKO MINUMAN'
    ],
    'mandiri' => [
        'name' => 'BANK MANDIRI',
        'account' => '1440055666777',
        'holder' => 'PT TOKO MINUMAN'
    ],
    'bni' => [
        'name' => 'BANK BNI',
        'account' => '0555777888',
        'holder' => 'PT TOKO MINUMAN'
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $id_pesanan; ?> - Toko Minuman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .bank-account {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .copy-button {
            cursor: pointer;
        }
        .copy-button:hover {
            color: #0d6efd;
        }
        @media print {
            .no-print {
                display: none;
            }
            .bank-account {
                border: 2px dashed #000;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Invoice #<?php echo $id_pesanan; ?></h5>
                            <button onclick="window.print()" class="btn btn-sm btn-light no-print">
                                <i class="fas fa-print me-1"></i> Cetak
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h6 class="mb-3">Dari:</h6>
                                <div><strong>Toko Minuman</strong></div>
                                <div>Jl. Contoh No. 123</div>
                                <div>Kota, Kode Pos</div>
                                <div>Email: info@tokominuman.com</div>
                                <div>Phone: (021) 1234567</div>
                            </div>
                            <div class="col-sm-6">
                                <h6 class="mb-3">Kepada:</h6>
                                <div><strong><?php echo $pesanan['nama_lengkap']; ?></strong></div>
                                <div><?php echo $pesanan['alamat_pengiriman']; ?></div>
                                <div>Email: <?php echo $pesanan['email']; ?></div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total = 0;
                                    while($item = mysqli_fetch_assoc($detail_result)): 
                                        $subtotal = $item['jumlah'] * $item['harga'];
                                        $total += $subtotal;
                                    ?>
                                    <tr>
                                        <td><?php echo $item['nama_produk']; ?></td>
                                        <td><?php echo $item['jumlah']; ?></td>
                                        <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                        <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>Rp <?php echo number_format($total, 0, ',', '.'); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <?php if($pesanan['metode_pembayaran'] == 'transfer'): ?>
                            <div class="mt-4">
                                <h5>Informasi Pembayaran</h5>
                                <p>Silakan transfer ke salah satu rekening berikut:</p>
                                
                                <?php foreach($bank_accounts as $bank): ?>
                                <div class="bank-account">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0"><?php echo $bank['name']; ?></h6>
                                        <span class="copy-button" onclick="copyToClipboard('<?php echo $bank['account']; ?>')">
                                            <i class="fas fa-copy"></i>
                                        </span>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">No. Rekening</div>
                                        <div class="col-sm-8"><strong><?php echo $bank['account']; ?></strong></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">Atas Nama</div>
                                        <div class="col-sm-8"><strong><?php echo $bank['holder']; ?></strong></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>

                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Harap transfer sesuai dengan total pembayaran dan simpan bukti transfer.
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <h5>Informasi Tambahan</h5>
                            <div class="row">
                                <div class="col-sm-4">Status</div>
                                <div class="col-sm-8"><?php echo ucfirst($pesanan['status']); ?></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">Metode Pembayaran</div>
                                <div class="col-sm-8"><?php echo ucfirst($pesanan['metode_pembayaran']); ?></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">Tanggal Pesanan</div>
                                <div class="col-sm-8"><?php echo date('d M Y H:i', strtotime($pesanan['tanggal_pesanan'])); ?></div>
                            </div>
                            <?php if($pesanan['catatan']): ?>
                            <div class="row">
                                <div class="col-sm-4">Catatan</div>
                                <div class="col-sm-8"><?php echo $pesanan['catatan']; ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 no-print">
                    <a href="pesanan_saya.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Pesanan Saya
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Nomor rekening berhasil disalin!');
            });
        }
    </script>
</body>
</html> 