<div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark" style="width: 280px; min-height: 100vh;">
    <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <span class="fs-4">Admin Warung</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php" class="nav-link text-white">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="menu.php" class="nav-link text-white">
                <i class="bi bi-card-checklist me-2"></i> Kelola Menu
            </a>
        </li>
        <li>
            <a href="kategori.php" class="nav-link text-white">
                <i class="bi bi-tags me-2"></i> Kelola Kategori
            </a>
        </li>
        <li>
            <a href="pesanan.php" class="nav-link text-white">
                <i class="bi bi-receipt me-2"></i> Kelola Pesanan
            </a>
        </li>
        <li>
            <a href="users.php" class="nav-link text-white">
                <i class="bi bi-people me-2"></i> Kelola Pengguna
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-2"></i>
            <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="../index.php" target="_blank">Lihat Situs</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="../logout.php">Sign out</a></li>
        </ul>
    </div>
</div>
<div class="flex-grow-1 p-4">