<?php
require 'partials/header.php';
require 'partials/sidebar.php';

// Aksi untuk UPDATE STATUS PESANAN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $pemesanan_id = $_POST['pemesanan_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE pemesanan SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $pemesanan_id);
    $stmt->execute();
    
    header("Location: pesanan.php");
    exit();
}

// Logika untuk menampilkan DETAIL PESANAN
$detail_pesanan = null;
$item_pesanan = [];
if (isset($_GET['view_id'])) {
    $view_id = $_GET['view_id'];
    
    $sql_detail = "SELECT p.*, u.username, py.total_bayar, py.metode_pembayaran 
                   FROM pemesanan p
                   JOIN users u ON p.user_id = u.id
                   JOIN pembayaran py ON p.id = py.pemesanan_id
                   WHERE p.id = ?";
    $stmt_detail = $conn->prepare($sql_detail);
    $stmt_detail->bind_param("i", $view_id);
    $stmt_detail->execute();
    $detail_pesanan = $stmt_detail->get_result()->fetch_assoc();

    $sql_items = "SELECT dp.*, m.name AS menu_name 
                  FROM detail_pemesanan dp
                  JOIN menu m ON dp.menu_id = m.id
                  WHERE dp.pemesanan_id = ?";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param("i", $view_id);
    $stmt_items->execute();
    $item_pesanan = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<h3>Kelola Pesanan</h3>

<div class="card">
    <div class="card-header">Daftar Semua Pesanan</div>
    <div class="card-body">
        <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th>Kode Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Total Bayar</th>
                    <th style="width: 250px;">Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT p.id, p.kode_transaksi, p.tanggal, p.status, u.username, py.total_bayar 
                        FROM pemesanan p
                        JOIN users u ON p.user_id = u.id
                        JOIN pembayaran py ON p.id = py.pemesanan_id
                        ORDER BY p.tanggal DESC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                        // --- LOGIKA UNTUK WARNA BADGE DIMULAI ---
                        $status = $row['status'];
                        $badge_class = 'bg-secondary'; // Warna default
                        if ($status == 'pending') {
                            $badge_class = 'bg-warning text-dark';
                        } elseif ($status == 'selesai') {
                            $badge_class = 'bg-success';
                        } elseif ($status == 'batal') {
                            $badge_class = 'bg-danger';
                        }
                        // --- LOGIKA UNTUK WARNA BADGE SELESAI ---
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['kode_transaksi']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo date('d M Y, H:i', strtotime($row['tanggal'])); ?></td>
                    <td>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                    <td>
                        <span class="badge <?php echo $badge_class; ?> d-block mb-2"><?php echo ucfirst($row['status']); ?></span>
                        <form action="pesanan.php" method="POST" class="d-flex">
                            <input type="hidden" name="pemesanan_id" value="<?php echo $row['id']; ?>">
                            <select name="status" class="form-select form-select-sm">
                                <option value="pending" <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="selesai" <?php echo ($row['status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                                <option value="batal" <?php echo ($row['status'] == 'batal') ? 'selected' : ''; ?>>Batal</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-sm btn-primary ms-2">Update</button>
                        </form>
                    </td>
                    <td>
                        <a href="pesanan.php?view_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else: ?>
                    <tr><td colspan="6" class="text-center">Belum ada pesanan yang masuk.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($detail_pesanan): ?>
<div class="modal fade" id="detailPesananModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Pesanan: <?php echo htmlspecialchars($detail_pesanan['kode_transaksi']); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Pelanggan:</strong> <?php echo htmlspecialchars($detail_pesanan['username']); ?></p>
        <p><strong>Tanggal:</strong> <?php echo date('d M Y, H:i', strtotime($detail_pesanan['tanggal'])); ?></p>
        <p><strong>Total Bayar:</strong> Rp <?php echo number_format($detail_pesanan['total_bayar'], 0, ',', '.'); ?></p>
        <p><strong>Metode Pembayaran:</strong> <?php echo strtoupper($detail_pesanan['metode_pembayaran']); ?></p>
        <hr>
        <h6>Item yang Dipesan:</h6>
        <table class="table table-sm">
            <thead><tr><th>Menu</th><th>Jumlah</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
                <?php foreach($item_pesanan as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['menu_name']); ?></td>
                    <td><?php echo $item['jumlah']; ?></td>
                    <td class="text-end">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var detailModal = new bootstrap.Modal(document.getElementById('detailPesananModal'));
        detailModal.show();
    });
</script>
<?php endif; ?>

<?php
require 'partials/footer.php';
?>