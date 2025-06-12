<?php
require 'partials/header.php';
require 'partials/sidebar.php';

// GANTI NAMA TABEL
$total_menu = $conn->query("SELECT COUNT(*) as total FROM menu")->fetch_assoc()['total'];
$total_pesanan = $conn->query("SELECT COUNT(*) as total FROM pemesanan")->fetch_assoc()['total'];
$total_pendapatan_result = $conn->query("SELECT SUM(py.total_bayar) as total FROM pembayaran py JOIN pemesanan p ON py.pemesanan_id = p.id WHERE p.status = 'selesai'");
$total_pendapatan = $total_pendapatan_result->fetch_assoc()['total'] ?? 0;

?>
<h1>Dashboard Admin</h1>
<hr>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Total Menu</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $total_menu; ?></h5>
                <p class="card-text">Jumlah varian menu yang tersedia.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Total Pesanan</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $total_pesanan; ?></h5>
                <p class="card-text">Jumlah semua pesanan yang masuk.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">Total Pendapatan (Selesai)</div>
            <div class="card-body">
                <h5 class="card-title">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h5>
                <p class="card-text">Total pendapatan dari pesanan yang selesai.</p>
            </div>
        </div>
    </div>
</div>

<?php
require 'partials/footer.php';
?>