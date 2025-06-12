-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table warungdb.detail_pemesanan
DROP TABLE IF EXISTS `detail_pemesanan`;
CREATE TABLE IF NOT EXISTS `detail_pemesanan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pemesanan_id` int NOT NULL,
  `jumlah` int NOT NULL,
  `menu_id` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pemesanan_id` (`pemesanan_id`),
  KEY `menu_id` (`menu_id`),
  CONSTRAINT `detail_pemesanan_ibfk_1` FOREIGN KEY (`pemesanan_id`) REFERENCES `pemesanan` (`id`),
  CONSTRAINT `detail_pemesanan_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table warungdb.detail_pemesanan: ~0 rows (approximately)
REPLACE INTO `detail_pemesanan` (`id`, `pemesanan_id`, `jumlah`, `menu_id`, `subtotal`, `created_at`) VALUES
	(2, 2, 1, 1, 3000.00, '2025-06-11 14:14:22'),
	(3, 3, 3, 7, 6000.00, '2025-06-11 14:38:15'),
	(4, 4, 3, 4, 9000.00, '2025-06-11 14:39:35');

-- Dumping structure for table warungdb.menu
DROP TABLE IF EXISTS `menu`;
CREATE TABLE IF NOT EXISTS `menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `category_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `menu_category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table warungdb.menu: ~0 rows (approximately)
REPLACE INTO `menu` (`id`, `name`, `harga`, `gambar`, `category_id`, `created_at`) VALUES
	(1, 'Beng Beng', 3000.00, '684990c57bb1c-bengbeng.jpg', 7, '2025-06-11 13:23:21'),
	(2, 'Beras', 70000.00, '684992e73de23-beras.jpg', 10, '2025-06-11 14:29:59'),
	(3, 'Bodrex Migra', 5500.00, '684992f9cedde-bodrex.jpg', 9, '2025-06-11 14:30:17'),
	(4, 'Bumbu Racik Nasi Goreng', 3000.00, '68499310763d8-bumburacik.jpg', 11, '2025-06-11 14:30:40'),
	(5, 'Indomie Kari', 3500.00, '6849932e47dec-indomie.jpg', 10, '2025-06-11 14:31:10'),
	(6, 'Kopi Kapal Api', 4000.00, '684993455ee5b-kopi.jpg', 10, '2025-06-11 14:31:33'),
	(7, 'Air Mineral', 2000.00, '684993a938646-airmineral.jpg', 11, '2025-06-11 14:33:13'),
	(8, 'Antimo', 4000.00, '684993b9a513b-antimo.jpg', 9, '2025-06-11 14:33:29'),
	(9, 'Basreng Pedas', 2500.00, '684993ca62abf-basreng.jpg', 7, '2025-06-11 14:33:46'),
	(11, 'Rexona Deodorant', 3000.00, '684993e9a97b6-deodorant.jpeg', 11, '2025-06-11 14:34:17'),
	(12, 'Entrostop', 6000.00, '684994381217c-entrostop.jpg', 9, '2025-06-11 14:35:01'),
	(13, 'Sikat Besi', 6000.00, '68499552ab666-genjreng.jpg', 12, '2025-06-11 14:35:27'),
	(14, 'Gula Pasir', 20000.00, '684994971784f-gula.jpg', 10, '2025-06-11 14:37:11');

-- Dumping structure for table warungdb.menu_category
DROP TABLE IF EXISTS `menu_category`;
CREATE TABLE IF NOT EXISTS `menu_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table warungdb.menu_category: ~1 rows (approximately)
REPLACE INTO `menu_category` (`id`, `name`, `description`, `created_at`) VALUES
	(7, 'Snack', 'jajanan', '2025-06-09 18:23:23'),
	(9, 'Obat', 'obat obatan', '2025-06-11 14:29:08'),
	(10, 'Sembako', 'beras dll', '2025-06-11 14:29:23'),
	(11, 'Kebutuhan', 'kebutuhan rumah tangga', '2025-06-11 14:29:35'),
	(12, 'Peralatan', '', '2025-06-11 14:39:56');

-- Dumping structure for table warungdb.pembayaran
DROP TABLE IF EXISTS `pembayaran`;
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pemesanan_id` int NOT NULL,
  `total_bayar` decimal(10,2) NOT NULL,
  `metode_pembayaran` enum('cash','qris') NOT NULL,
  `tanggal_bayar` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pemesanan_id` (`pemesanan_id`),
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`pemesanan_id`) REFERENCES `pemesanan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table warungdb.pembayaran: ~0 rows (approximately)
REPLACE INTO `pembayaran` (`id`, `pemesanan_id`, `total_bayar`, `metode_pembayaran`, `tanggal_bayar`, `created_at`) VALUES
	(1, 2, 3000.00, 'cash', '2025-06-11 00:00:00', '2025-06-11 14:14:22'),
	(2, 3, 6000.00, 'qris', '2025-06-11 00:00:00', '2025-06-11 14:38:15'),
	(3, 4, 9000.00, 'cash', '2025-06-11 00:00:00', '2025-06-11 14:39:35');

-- Dumping structure for table warungdb.pemesanan
DROP TABLE IF EXISTS `pemesanan`;
CREATE TABLE IF NOT EXISTS `pemesanan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(100) NOT NULL,
  `user_id` int NOT NULL,
  `tanggal` datetime NOT NULL,
  `status` enum('pending','selesai','batal') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table warungdb.pemesanan: ~0 rows (approximately)
REPLACE INTO `pemesanan` (`id`, `kode_transaksi`, `user_id`, `tanggal`, `status`, `created_at`) VALUES
	(2, 'WJ-2025061168498F3E7349E', 1, '2025-06-11 00:00:00', 'selesai', '2025-06-11 14:14:22'),
	(3, 'WJ-20250611684994D71E6A3', 1, '2025-06-11 00:00:00', 'selesai', '2025-06-11 14:38:15'),
	(4, 'WJ-20250611684995278692F', 1, '2025-06-11 00:00:00', 'selesai', '2025-06-11 14:39:35');

-- Dumping structure for table warungdb.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table warungdb.users: ~0 rows (approximately)
REPLACE INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
	(1, 'Rasya', '$2y$10$1P3XbPuNmJ4aEKDcvkPk2uR9CWP77F2AILdIb3IM0GuPiJqIKOqqG', 'inirasya16@gmail.com', 'admin', '2025-06-11 12:08:00'),
	(2, 'ahmad', '$2y$10$mdrzysLKL49cY0d6S7sICum7yTKwMsYYFkoIG84Zx1fdqaQypDak2', 'ahmad@gmail.com', 'user', '2025-06-11 14:25:03');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
