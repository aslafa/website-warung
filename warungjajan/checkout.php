<?php
require 'templates/header.php';

// 1. Validasi: Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['info_message'] = 'Anda harus login untuk melanjutkan ke checkout.';
    header('Location: login.php');
    exit();
}

// 2. Validasi: Pastikan keranjang tidak kosong
if (empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit();
}

// 3. Logika untuk memproses pesanan saat form disubmit
$checkout_success = false;
$kode_transaksi_berhasil = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $kode_transaksi = 'WJ-' . date('Ymd') . strtoupper(uniqid());

    $conn->begin_transaction();
    try {
        // a. Insert ke tabel `pemesanan`
        $tanggal_pemesanan = date('Y-m-d');
        $stmt_pemesanan = $conn->prepare("INSERT INTO pemesanan (kode_transaksi, user_id, status, tanggal) VALUES (?, ?, 'pending', ?)");
        $stmt_pemesanan->bind_param("sis", $kode_transaksi, $user_id, $tanggal_pemesanan);
        $stmt_pemesanan->execute();
        $pemesanan_id = $conn->insert_id;

        // b. Loop melalui keranjang dan insert ke `detail_pemesanan`
        $total_bayar = 0;
        $menu_ids_str = implode(',', array_keys($_SESSION['cart']));
        $menu_result = $conn->query("SELECT id, harga FROM menu WHERE id IN ($menu_ids_str)");
        $menus_data = [];
        while ($row = $menu_result->fetch_assoc()) {
            $menus_data[$row['id']] = $row['harga'];
        }

        $stmt_detail = $conn->prepare("INSERT INTO detail_pemesanan (pemesanan_id, menu_id, jumlah, subtotal) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $menu_id => $jumlah) {
            $harga = $menus_data[$menu_id];
            $subtotal = $harga * $jumlah;
            $total_bayar += $subtotal;
            $stmt_detail->bind_param("iiid", $pemesanan_id, $menu_id, $jumlah, $subtotal);
            $stmt_detail->execute();
        }

        // c. Insert ke tabel `pembayaran`
        $tanggal_pembayaran = date('Y-m-d');
        $stmt_pembayaran = $conn->prepare("INSERT INTO pembayaran (pemesanan_id, total_bayar, metode_pembayaran, tanggal_bayar) VALUES (?, ?, ?, ?)");
        $stmt_pembayaran->bind_param("idss", $pemesanan_id, $total_bayar, $metode_pembayaran, $tanggal_pembayaran);
        $stmt_pembayaran->execute();

        // Jika semua query berhasil, commit transaksi
        $conn->commit();

        // Kosongkan keranjang dan tandai checkout berhasil
        unset($_SESSION['cart']);
        $checkout_success = true;
        $kode_transaksi_berhasil = $kode_transaksi;
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        // Simpan pesan error untuk ditampilkan
        var_dump($exception);
        $db_error_message = "Checkout gagal. Terjadi kesalahan pada database. Silakan coba lagi.";
        // Untuk debugging, Anda bisa uncomment baris di bawah ini untuk melihat error asli
        // $db_error_message = "Error: " . $exception->getMessage();
    }
}
?>

<div class="container-fluid min-vh-100 py-4">
    <h2>Checkout</h2>

    <?php if (isset($db_error_message)): ?>
        <div class="alert alert-danger"><?php echo $db_error_message; ?></div>
    <?php endif; ?>

    <?php if ($checkout_success): ?>
        <div class="alert alert-success text-center">
            <h4>Pesanan Berhasil Dibuat!</h4>
            <p>Terima kasih telah berbelanja. Kode Transaksi Anda adalah:</p>
            <h3><strong><?php echo $kode_transaksi_berhasil; ?></strong></h3>
            <p>Anda dapat melihat riwayat pesanan Anda di halaman <a href="pesanan_saya.php" class="alert-link">Pesanan Saya</a>.</p>
            <a href="index.php" class="btn btn-primary mt-3">Kembali ke Halaman Utama</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-7">
                <h4>Ringkasan Pesanan</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_harga = 0;
                        $menu_ids = implode(',', array_keys($_SESSION['cart']));
                        $sql = "SELECT id, name, harga FROM menu WHERE id IN ($menu_ids)";
                        $result = $conn->query($sql);
                        $menus_data = [];
                        while ($row = $result->fetch_assoc()) {
                            $menus_data[$row['id']] = $row;
                        }

                        foreach ($_SESSION['cart'] as $menu_id => $quantity) {
                            $menu_item = $menus_data[$menu_id];
                            $subtotal = $menu_item['harga'] * $quantity;
                            $total_harga += $subtotal;
                            echo "<tr><td>" . htmlspecialchars($menu_item['name']) . "</td><td>$quantity</td><td class='text-end'>Rp " . number_format($subtotal, 0, ',', '.') . "</td></tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Total Belanja</th>
                            <th class="text-end">Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Konfirmasi Pesanan</h4>
                        <form action="checkout.php" method="post">
                            <div class="mb-3">
                                <label for="metode_pembayaran" class="form-label">Pilih Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                                    <option value="cash">Cash</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>
                            <p>Dengan menekan tombol di bawah, Anda setuju untuk membuat pesanan ini.</p>
                            <div class="d-grid">
                                <button type="submit" name="place_order" class="btn btn-lg btn-success">Buat Pesanan Sekarang</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>


<?php require 'templates/footer.php'; ?>