<?php require 'templates/header.php'; ?>

<div class="container py-4" style="min-height: 100dvh;">

    <h2>Keranjang Belanja Anda</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-info">Keranjang belanja Anda kosong. Yuk, mulai <a href="index.php">belanja</a>!</div>
    <?php else: ?>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga Satuan</th>
                    <th style="width: 150px;">Jumlah</th>
                    <th>Subtotal</th>
                    <th style="width: 80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_harga = 0;
                // Ambil semua ID menu dari session untuk di-query sekaligus
                $menu_ids = implode(',', array_keys($_SESSION['cart']));
                $sql = "SELECT * FROM menu WHERE id IN ($menu_ids)";
                $result = $conn->query($sql);

                // Buat array asosiatif dari hasil query agar mudah diakses
                $menus = [];
                while ($row = $result->fetch_assoc()) {
                    $menus[$row['id']] = $row;
                }

                foreach ($_SESSION['cart'] as $menu_id => $quantity):
                    // Jika data menu ada
                    if (isset($menus[$menu_id])):
                        $menu_item = $menus[$menu_id];
                        $subtotal = $menu_item['harga'] * $quantity;
                        $total_harga += $subtotal;
                ?>
                        <tr>
                            <td><?php echo htmlspecialchars($menu_item['name']); ?></td>
                            <td>Rp <?php echo number_format($menu_item['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <form action="cart_handler.php" method="post" class="d-flex">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="menu_id" value="<?php echo $menu_id; ?>">
                                    <input type="number" name="quantity" value="<?php echo $quantity; ?>" min="1" class="form-control form-control-sm">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary ms-2">Update</button>
                                </form>
                            </td>
                            <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                            <td>
                                <form action="cart_handler.php" method="post">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="menu_id" value="<?php echo $menu_id; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                <?php
                    endif;
                endforeach;
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">
                        <h4>Total</h4>
                    </th>
                    <th colspan="2">
                        <h4>Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></h4>
                    </th>
                </tr>
            </tfoot>
        </table>
        <div class="d-flex justify-content-between">
            <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Lanjut Belanja</a>
            <a href="checkout.php" class="btn btn-success">Lanjut ke Checkout <i class="bi bi-arrow-right"></i></a>
        </div>
    <?php endif; ?>
</div>


<?php require 'templates/footer.php'; ?>