-- Database Dump for History Kedai
-- Saved as history.sql

CREATE DATABASE IF NOT EXISTS `history`;
USE `history`;

-- --------------------------------------------------------
-- 1. Table structure for table `Karyawan`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Karyawan` (
  `id_karyawan` INT(11) NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `jabatan` VARCHAR(50) NOT NULL COMMENT 'Pemilik, Admin, Kasir',
  `status_karyawan` VARCHAR(20) NOT NULL DEFAULT 'Aktif',
  PRIMARY KEY (`id_karyawan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping initial data for table `Karyawan`
INSERT INTO `Karyawan` (`id_karyawan`, `nama`, `username`, `password`, `jabatan`, `status_karyawan`) VALUES
(1, 'Pemilik Kedai', 'pemilik', 'admin123', 'Pemilik', 'Aktif'),
(2, 'Admin Kedai', 'admin', 'admin123', 'Admin', 'Aktif'),
(3, 'Kasir Kedai', 'kasir', 'kasir123', 'Kasir', 'Aktif');

-- --------------------------------------------------------
-- 2. Table structure for table `Kategori`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Kategori` (
  `id_kategori` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `status_kategori` VARCHAR(20) NOT NULL DEFAULT 'Tersedia',
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping initial data for table `Kategori`
INSERT INTO `Kategori` (`id_kategori`, `nama_kategori`, `status_kategori`) VALUES
(1, 'Kopi', 'Tersedia'),
(2, 'Non-Kopi', 'Tersedia'),
(3, 'Makanan', 'Tersedia'),
(4, 'Camilan', 'Tersedia');

-- --------------------------------------------------------
-- 3. Table structure for table `Menu`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Menu` (
  `id_menu` INT(11) NOT NULL AUTO_INCREMENT,
  `id_kategori` INT(11) NOT NULL,
  `nama_menu` VARCHAR(100) NOT NULL,
  `harga` DECIMAL(10,2) NOT NULL,
  `gambar` VARCHAR(255) DEFAULT '',
  `status_menu` VARCHAR(20) NOT NULL DEFAULT 'Tersedia',
  PRIMARY KEY (`id_menu`),
  KEY `fk_menu_kategori` (`id_kategori`),
  CONSTRAINT `fk_menu_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `Kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping initial data for table `Menu`
INSERT INTO `Menu` (`id_menu`, `id_kategori`, `nama_menu`, `harga`, `gambar`, `status_menu`) VALUES
(1, 1, 'Kopi Susu History', 18000.00, '', 'Tersedia'),
(2, 1, 'Americano Hot/Ice', 15000.00, '', 'Tersedia'),
(3, 2, 'Matcha Latte', 22000.00, '', 'Tersedia'),
(4, 2, 'Chocolate Classic', 20000.00, '', 'Tersedia'),
(5, 3, 'Nasi Goreng Special', 25000.00, '', 'Tersedia'),
(6, 4, 'Roti Bakar Cokelat', 15000.00, '', 'Tersedia');

-- --------------------------------------------------------
-- 4. Table structure for table `Pelanggan`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Pelanggan` (
  `id_pelanggan` INT(11) NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_pelanggan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping initial data for table `Pelanggan`
INSERT INTO `Pelanggan` (`id_pelanggan`, `nama`) VALUES
(1, 'Pelanggan Umum');

-- --------------------------------------------------------
-- 5. Table structure for table `Transaksi`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Transaksi` (
  `id_transaksi` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pelanggan` INT(11) NOT NULL,
  `id_karyawan` INT(11) NOT NULL,
  `tanggal` DATETIME NOT NULL,
  `total_harga` DECIMAL(12,2) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'Baru Masuk',
  `metode_bayar` VARCHAR(50) NOT NULL DEFAULT 'Tunai',
  PRIMARY KEY (`id_transaksi`),
  KEY `fk_transaksi_pelanggan` (`id_pelanggan`),
  KEY `fk_transaksi_karyawan` (`id_karyawan`),
  CONSTRAINT `fk_transaksi_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `Pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_transaksi_karyawan` FOREIGN KEY (`id_karyawan`) REFERENCES `Karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 6. Table structure for table `Detail_Transaksi`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Detail_Transaksi` (
  `id_detail` INT(11) NOT NULL AUTO_INCREMENT,
  `id_transaksi` INT(11) NOT NULL,
  `id_menu` INT(11) NOT NULL,
  `jumlah` INT(11) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `fk_detail_transaksi` (`id_transaksi`),
  KEY `fk_detail_menu` (`id_menu`),
  CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `Transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_menu` FOREIGN KEY (`id_menu`) REFERENCES `Menu` (`id_menu`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
