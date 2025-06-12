<?php
require_once 'templates/header.php';

// Ambil ID kategori dari URL
$categoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($categoryId === 0) {
    echo "<div class='container mt-4'><p class='text-danger'>Kategori tidak ditemukan.</p></div>";
    require_once 'templates/footer.php';
    exit;
}

// Ambil nama kategori
$stmtCat = $conn->prepare("SELECT name FROM menu_category WHERE id = ?");
$stmtCat->bind_param("i", $categoryId);
$stmtCat->execute();
$resultCat = $stmtCat->get_result();
$category = $resultCat->fetch_assoc();

if (!$category) {
    echo "<div class='container mt-4'><p class='text-danger'>Kategori tidak ditemukan.</p></div>";
    require_once 'templates/footer.php';
    exit;
}

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Hitung total data untuk pagination
$stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM menu WHERE category_id = ?");
$stmtTotal->bind_param("i", $categoryId);
$stmtTotal->execute();
$totalResult = $stmtTotal->get_result()->fetch_assoc();
$totalItems = $totalResult['total'];
$totalPages = ceil($totalItems / $limit);

// Ambil menu berdasarkan kategori & halaman
$stmtMenu = $conn->prepare("SELECT * FROM menu WHERE category_id = ? ORDER BY name ASC LIMIT ?, ?");
$stmtMenu->bind_param("iii", $categoryId, $offset, $limit);
$stmtMenu->execute();
$menus = $stmtMenu->get_result();
?>

<div class="container mt-4">
    <!-- Banner buatan sendiri -->
    <div id="welcome" class="p-5 mb-4 rounded-3 shadow-sm text-white">
        <div class="container-fluid py-3 text-center">
            <h1 class="display-5 fw-bold"><?= htmlspecialchars($category['name']); ?></h1>
            <p class="lead">Lihat daftar menu dari kategori <strong><?= htmlspecialchars($category['name']); ?></strong> di bawah ini.</p>
        </div>
    </div>

    <!-- List menu -->
    <?php if ($menus->num_rows > 0): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php while ($menu = $menus->fetch_assoc()):
                $gambar = 'https://via.placeholder.com/400x300.png?text=Gambar+Segera';
                if (!empty($menu['gambar']) && file_exists('uploads/menu/'.$menu['gambar'])) {
                    $gambar = 'uploads/menu/'.$menu['gambar'];
                }
                $harga = number_format($menu['harga'], 0, ',', '.');
            ?>
                <div class="col">
                    <div class="card h-100">
                        <img src="<?= $gambar; ?>" class="card-img-top" alt="<?= htmlspecialchars($menu['name']); ?>" style="height:200px; object-fit:cover;">
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

        <!-- Pagination -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?id=<?= $categoryId ?>&page=<?= $page - 1 ?>">← Sebelumnya</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?id=<?= $categoryId ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?id=<?= $categoryId ?>&page=<?= $page + 1 ?>">Berikutnya →</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

    <?php else: ?>
        <p class="text-muted text-center">Tidak ada menu dalam kategori ini.</p>
    <?php endif; ?>
</div>

<?php include 'templates/footer.php'; ?>
