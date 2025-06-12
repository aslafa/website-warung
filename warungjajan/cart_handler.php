<?php
require_once 'config/database.php'; // Ini akan otomatis memanggil session_start()

// Pastikan ada aksi yang dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    
    $action = $_POST['action'];
    $menu_id = $_POST['menu_id'];

    // Inisialisasi keranjang jika belum ada
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    switch ($action) {
        case 'add':
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            if ($quantity > 0) {
                // Jika produk sudah ada di keranjang, tambahkan jumlahnya
                if (isset($_SESSION['cart'][$menu_id])) {
                    $_SESSION['cart'][$menu_id] += $quantity;
                } else {
                    // Jika belum ada, tambahkan sebagai item baru
                    $_SESSION['cart'][$menu_id] = $quantity;
                }
            }
            break;

        case 'update':
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
            if ($quantity > 0) {
                $_SESSION['cart'][$menu_id] = $quantity;
            } else {
                // Jika jumlah 0 atau kurang, hapus item dari keranjang
                unset($_SESSION['cart'][$menu_id]);
            }
            break;
            
        case 'remove':
            unset($_SESSION['cart'][$menu_id]);
            break;
    }
}

// Setelah memproses, arahkan pengguna kembali ke halaman keranjang
header('Location: cart.php');
exit();