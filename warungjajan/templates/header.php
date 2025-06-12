<?php
// Mulai session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Warung Jajan</title>
  <!-- Font Awesome -->
  <link
    rel="stylesheet"
    href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css">

  <!-- Boostrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Custom CSS untuk memastikan kontras yang baik -->

  <link rel="stylesheet" href="./dist/css/app.css">

  <?php if (isset($css)) echo $css; ?>
</head>

<body>

  <nav class="navbar navbar-expand-lg bg-white shadow-sm border-bottom">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <i class="bi bi-shop text-primary me-2 fs-4"></i>
        <span class="text-white">Berkah Lestari</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="index.php">
              <i class="bi bi-house me-1 text-white"></i>
              <span class="text-white">Home</span>
            </a>
          </li>
          <li class="nav-item position-relative">
            <a class="nav-link d-flex align-items-center" href="cart.php">
              <i class="bi bi-cart me-1 text-white"></i>
              <span class="text-white">Barang</span>
              <?php
              $cart_item_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
              if ($cart_item_count > 0) {
                echo "<span class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger'>$cart_item_count</span>";
              }
              ?>
            </a>
          </li>
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center" role="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="bi bi-person-circle me-1 text-white"></i>
                <span class="text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item d-flex align-items-center" href="pesanan_saya.php">
                    <i class="bi bi-bag-check me-2"></i>Pesanan Saya
                  </a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                  <li><a class="dropdown-item d-flex align-items-center" href="admin/index.php">
                      <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
                    </a></li>
                <?php endif; ?>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger d-flex align-items-center" href="logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                  </a></li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center" href="login.php">
                <i class="bi bi-box-arrow-in-right me-1 text-white"></i>
                <span class="text-white">Login</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center" href="register.php">
                <i class="bi bi-person-plus me-1 text-white"></i>
                <span class="text-white">Register</span>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>