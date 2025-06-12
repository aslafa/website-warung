<?php require 'templates/header.php'; ?>

<div class="container mt-4">
    <?php require_once('./banner.php') ?>

    <?php
    // Ambil semua kategori
    $cats = $conn->query("SELECT * FROM menu_category ORDER BY name ASC");

    while ($cat = $cats->fetch_assoc()):
        $catId = intval($cat['id']);
        $catName = htmlspecialchars($cat['name']);

        // Cek apakah kategori ini punya minimal 1 menu
        $stmt = $conn->prepare("SELECT * FROM menu WHERE category_id = ? ORDER BY name ASC LIMIT 4");
        $stmt->bind_param("i", $catId);
        $stmt->execute();
        $menus = $stmt->get_result();

        // Skip kategori jika tidak punya menu
        if ($menus->num_rows === 0) {
            continue;
        }
    ?>
        <div class="category-section mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4">
                    <i class="bi bi-tags text-primary me-2"></i><?= $catName; ?>
                </h2>
                <a href="category.php?id=<?= $catId; ?>" class="btn btn-link text-decoration-none">Lihat Semua →</a>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                <?php
                while ($menu = $menus->fetch_assoc()):
                    $gambar = 'https://via.placeholder.com/400x300.png?text=Gambar+Segera';
                    if (!empty($menu['gambar']) && file_exists('uploads/menu/'.$menu['gambar'])) {
                        $gambar = 'uploads/menu/'.$menu['gambar'];
                    }
                    $harga = number_format($menu['harga'], 0, ',', '.');
                ?>
                    <div class="col">
                        <div class="card h-100">
                            <img src="<?= $gambar; ?>" class="card-img-top" alt="<?= htmlspecialchars($menu['name']); ?>"
                                 style="height:200px; object-fit:cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($menu['name']); ?></h5>
                                <p class="text-primary fw-bold mb-3">Rp <?= $harga; ?></p>
                                <form action="cart_handler.php" method="post" class="mt-auto">
                                    <input type="hidden" name="menu_id" value="<?= $menu['id']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <div class="input-group">
                                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="99">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-cart-plus me-1"></i> Add
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php require 'templates/footer.php'; ?>
