<?php
require 'templates/header.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<div class="container-fluid py-4 min-vh-100">

    <h2>Riwayat Pesanan Saya</h2>

    <div class="accordion" id="accordionPesanan">
        <?php
        $sql = "SELECT p.*, py.total_bayar, py.metode_pembayaran 
            FROM pemesanan p
            LEFT JOIN pembayaran py ON p.id = py.pemesanan_id
            WHERE p.user_id = ? 
            ORDER BY p.tanggal DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0):
            while ($pesanan = $result->fetch_assoc()):

                $status = $pesanan['status'];
                $badge_class = 'bg-secondary';
                if ($status == 'pending') {
                    $badge_class = 'bg-warning text-dark';
                } elseif ($status == 'selesai') {
                    $badge_class = 'bg-success';
                } elseif ($status == 'batal') {
                    $badge_class = 'bg-danger';
                }

        ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?php echo $pesanan['id']; ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $pesanan['id']; ?>">
                            Kode Transaksi: <?php echo htmlspecialchars($pesanan['kode_transaksi']); ?>
                            <span class="ms-auto me-3">Tanggal: <?php echo date('d M Y, H:i', strtotime($pesanan['tanggal'])); ?></span>

                            <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($pesanan['status']); ?></span>

                        </button>
                    </h2>
                    <div id="collapse<?php echo $pesanan['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionPesanan">
                        <div class="accordion-body">
                            <h5>Detail Pesanan</h5>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $detail_sql = "SELECT d.*, m.name AS menu_name 
                                       FROM detail_pemesanan d
                                       JOIN menu m ON d.menu_id = m.id
                                       WHERE d.pemesanan_id = ?";
                                    $stmt_detail = $conn->prepare($detail_sql);
                                    $stmt_detail->bind_param("i", $pesanan['id']);
                                    $stmt_detail->execute();
                                    $result_detail = $stmt_detail->get_result();
                                    while ($detail = $result_detail->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($detail['menu_name']); ?></td>
                                            <td><?php echo $detail['jumlah']; ?></td>
                                            <td>Rp <?php echo number_format($detail['subtotal'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endwhile;
                                    $stmt_detail->close(); ?>
                                </tbody>
                            </table>
                            <hr>
                            <strong>Total Bayar:</strong> Rp <?php echo number_format($pesanan['total_bayar'], 0, ',', '.'); ?><br>
                            <strong>Metode Pembayaran:</strong> <?php echo strtoupper($pesanan['metode_pembayaran']); ?>
                        </div>
                    </div>
                </div>
        <?php
            endwhile;
        else:
            echo "<div class='alert alert-info'>Anda belum memiliki riwayat pesanan.</div>";
        endif;
        $stmt->close();
        ?>
    </div>

</div>


<?php require 'templates/footer.php'; ?>