<?php
// Mulai session di sini agar tersedia di semua halaman
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$user = 'root'; // Sesuaikan dengan username database Anda
$pass = '';     // Sesuaikan dengan password database Anda
$db_name = 'db_warungjajan';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
