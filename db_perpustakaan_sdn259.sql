-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 03:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_perpustakaan_sdn259`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telepon` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `foto_identitas` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('menunggu','aktif','nonaktif') DEFAULT 'menunggu',
  `tanggal_daftar` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `id_user`, `nama_lengkap`, `email`, `no_telepon`, `alamat`, `foto_identitas`, `status_verifikasi`, `tanggal_daftar`) VALUES
(1, 3, 'Agile', 'budi@email.com', '081234567892', 'Jl. Bukit Subur No. 123', NULL, 'aktif', '2025-11-01'),
(2, 5, 'yobi aja', 'yobi@gmail.com', '45y34698356', 'ji jalan', NULL, 'aktif', '2025-11-18'),
(3, 6, 'alif', 'alif123@gmail.com', '7687564', 'jl. tersesat', NULL, 'menunggu', '2025-11-18'),
(4, 7, 'fatiah lengkap', 'fatiah@gmailcom', '7t6856574', 'jl dimana', NULL, 'aktif', '2025-11-18'),
(5, 8, 'ichsandi', 'mr.sun025@gmail.com', NULL, NULL, NULL, 'menunggu', '2025-11-18'),
(6, 9, 'boby boboboy', 'boby@gmail.com', NULL, NULL, NULL, 'aktif', '2025-11-18'),
(7, 10, 'aydil adha', 'aydil@gmail.com', NULL, NULL, NULL, 'aktif', '2025-11-20');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id_buku` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `pengarang` varchar(100) DEFAULT NULL,
  `penerbit` varchar(100) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `jumlah_total` int(11) NOT NULL,
  `jumlah_tersedia` int(11) NOT NULL,
  `status` enum('tersedia','dipinjam','rusak') DEFAULT 'tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id_buku`, `judul`, `pengarang`, `penerbit`, `isbn`, `kategori`, `tahun_terbit`, `jumlah_total`, `jumlah_tersedia`, `status`, `created_at`) VALUES
(1, 'Pengantar Teknologi Iinformasi : Konsep, Tren dan Implementasi Moderen', 'Ichsandi', 'Hira Institute', '57647567477', 'Pendidikan', '2025', 10, 9, 'tersedia', '2025-11-18 12:49:41'),
(2, 'e-commerce', 'Ichsandi', 'Hira Institute', '57465657', 'Pendidikan', '2025', 20, 20, 'tersedia', '2025-11-20 02:05:04');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  `no_telepon` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `id_user`, `nama_lengkap`, `email`, `jabatan`, `no_telepon`) VALUES
(1, 1, 'Administrator', 'admin@sdn259bukitsubur.sch.id', 'Administrator', '081234567890'),
(2, 2, 'Siti Aminah', 'pustakawan@sdn259bukitsubur.sch.id', 'Pustakawan', '081234567891');

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notifikasi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `status_baca` enum('belum_dibaca','sudah_dibaca') DEFAULT 'belum_dibaca',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id_notifikasi`, `id_user`, `judul`, `pesan`, `status_baca`, `created_at`) VALUES
(1, 1, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 13:39:00', 'belum_dibaca', '2025-11-18 12:39:00'),
(2, 1, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 13:56:06', 'belum_dibaca', '2025-11-18 12:56:06'),
(3, 1, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:00:17', 'belum_dibaca', '2025-11-18 13:00:17'),
(4, 1, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:00:23', 'belum_dibaca', '2025-11-18 13:00:23'),
(5, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:02:24', 'belum_dibaca', '2025-11-18 13:02:24'),
(6, 3, 'Peminjaman Berhasil', 'Peminjaman buku \"Pengantar Teknologi Iinformasi : Konsep, Tren dan Implementasi Moderen\" berhasil. Jatuh tempo: 25 November 2025', 'belum_dibaca', '2025-11-18 13:07:31'),
(7, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:08:11', 'belum_dibaca', '2025-11-18 13:08:11'),
(8, 1, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:08:27', 'belum_dibaca', '2025-11-18 13:08:27'),
(9, 1, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:11:49', 'belum_dibaca', '2025-11-18 13:11:49'),
(10, 3, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:12:02', 'belum_dibaca', '2025-11-18 13:12:02'),
(11, 3, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:14:12', 'belum_dibaca', '2025-11-18 13:14:12'),
(12, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:14:15', 'belum_dibaca', '2025-11-18 13:14:15'),
(13, 3, 'Pengembalian Berhasil', 'Pengembalian buku berhasil dicatat.', 'belum_dibaca', '2025-11-18 13:14:31'),
(14, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:14:33', 'belum_dibaca', '2025-11-18 13:14:33'),
(15, 3, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:14:38', 'belum_dibaca', '2025-11-18 13:14:38'),
(16, 3, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:14:55', 'belum_dibaca', '2025-11-18 13:14:55'),
(17, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:20:21', 'belum_dibaca', '2025-11-18 13:20:21'),
(18, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:20:25', 'belum_dibaca', '2025-11-18 13:20:25'),
(19, 1, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:20:28', 'belum_dibaca', '2025-11-18 13:20:28'),
(20, 1, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:21:15', 'belum_dibaca', '2025-11-18 13:21:15'),
(21, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:27:32', 'belum_dibaca', '2025-11-18 13:27:32'),
(22, 7, 'Akun Diverifikasi', 'Selamat! Akun Anda telah diverifikasi. Silakan login untuk menggunakan layanan perpustakaan.', 'belum_dibaca', '2025-11-18 13:27:47'),
(23, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:27:50', 'belum_dibaca', '2025-11-18 13:27:50'),
(24, 7, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:27:54', 'belum_dibaca', '2025-11-18 13:27:54'),
(25, 7, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:28:02', 'belum_dibaca', '2025-11-18 13:28:02'),
(26, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:28:08', 'belum_dibaca', '2025-11-18 13:28:08'),
(27, 5, 'Akun Diverifikasi', 'Selamat! Akun Anda telah diverifikasi. Silakan login untuk menggunakan layanan perpustakaan.', 'belum_dibaca', '2025-11-18 13:28:20'),
(28, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:28:24', 'belum_dibaca', '2025-11-18 13:28:24'),
(29, 5, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:28:28', 'belum_dibaca', '2025-11-18 13:28:28'),
(30, 5, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:29:34', 'belum_dibaca', '2025-11-18 13:29:34'),
(31, 1, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:45:11', 'belum_dibaca', '2025-11-18 13:45:11'),
(32, 1, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:45:51', 'belum_dibaca', '2025-11-18 13:45:51'),
(33, 3, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:46:07', 'belum_dibaca', '2025-11-18 13:46:07'),
(34, 3, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:46:32', 'belum_dibaca', '2025-11-18 13:46:32'),
(35, 7, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 14:46:36', 'belum_dibaca', '2025-11-18 13:46:36'),
(36, 7, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 14:46:47', 'belum_dibaca', '2025-11-18 13:46:47'),
(37, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:08:38', 'belum_dibaca', '2025-11-18 14:08:38'),
(38, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:08:41', 'belum_dibaca', '2025-11-18 14:08:41'),
(39, 5, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:08:46', 'belum_dibaca', '2025-11-18 14:08:46'),
(40, 5, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:08:47', 'belum_dibaca', '2025-11-18 14:08:47'),
(41, 1, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:08:51', 'belum_dibaca', '2025-11-18 14:08:51'),
(42, 1, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:08:58', 'belum_dibaca', '2025-11-18 14:08:58'),
(43, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:23:02', 'belum_dibaca', '2025-11-18 14:23:02'),
(44, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:23:04', 'belum_dibaca', '2025-11-18 14:23:04'),
(45, 1, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:23:07', 'belum_dibaca', '2025-11-18 14:23:07'),
(46, 1, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:23:10', 'belum_dibaca', '2025-11-18 14:23:10'),
(47, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:29:53', 'belum_dibaca', '2025-11-18 14:29:53'),
(48, 9, 'Akun Diverifikasi', 'Selamat! Akun Anda telah diverifikasi. Silakan login untuk menggunakan layanan perpustakaan.', 'belum_dibaca', '2025-11-18 14:30:06'),
(49, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:30:08', 'belum_dibaca', '2025-11-18 14:30:08'),
(50, 9, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:30:19', 'belum_dibaca', '2025-11-18 14:30:19'),
(51, 9, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:30:26', 'belum_dibaca', '2025-11-18 14:30:26'),
(52, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:31:31', 'belum_dibaca', '2025-11-18 14:31:31'),
(53, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:31:34', 'belum_dibaca', '2025-11-18 14:31:34'),
(54, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:41:23', 'belum_dibaca', '2025-11-18 14:41:23'),
(55, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:41:37', 'belum_dibaca', '2025-11-18 14:41:37'),
(56, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:44:24', 'belum_dibaca', '2025-11-18 14:44:24'),
(57, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:44:38', 'belum_dibaca', '2025-11-18 14:44:38'),
(58, 9, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:44:42', 'belum_dibaca', '2025-11-18 14:44:42'),
(59, 9, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:45:08', 'belum_dibaca', '2025-11-18 14:45:08'),
(60, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:45:12', 'belum_dibaca', '2025-11-18 14:45:12'),
(61, 9, 'Pemesanan Disetujui', 'Pemesanan buku Anda telah disetujui. Silakan datang ke perpustakaan untuk meminjam buku.', 'belum_dibaca', '2025-11-18 14:45:22'),
(62, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:45:26', 'belum_dibaca', '2025-11-18 14:45:26'),
(63, 9, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:45:29', 'belum_dibaca', '2025-11-18 14:45:29'),
(64, 9, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:45:49', 'belum_dibaca', '2025-11-18 14:45:49'),
(65, 2, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:45:52', 'belum_dibaca', '2025-11-18 14:45:52'),
(66, 9, 'Peminjaman Berhasil', 'Peminjaman buku \"Pengantar Teknologi Iinformasi : Konsep, Tren dan Implementasi Moderen\" berhasil. Jatuh tempo: 25 November 2025', 'belum_dibaca', '2025-11-18 14:46:33'),
(67, 2, 'Logout Berhasil', 'Anda berhasil logout pada 18-11-2025 15:46:55', 'belum_dibaca', '2025-11-18 14:46:55'),
(68, 9, 'Login Berhasil', 'Anda berhasil login pada 18-11-2025 15:46:58', 'belum_dibaca', '2025-11-18 14:46:58'),
(69, 2, 'Login Berhasil', 'Anda berhasil login pada 20-11-2025 03:04:24', 'belum_dibaca', '2025-11-20 02:04:24'),
(70, 2, 'Logout Berhasil', 'Anda berhasil logout pada 20-11-2025 03:04:29', 'belum_dibaca', '2025-11-20 02:04:29'),
(71, 1, 'Login Berhasil', 'Anda berhasil login pada 20-11-2025 03:04:33', 'belum_dibaca', '2025-11-20 02:04:33'),
(72, 1, 'Logout Berhasil', 'Anda berhasil logout pada 20-11-2025 03:05:10', 'belum_dibaca', '2025-11-20 02:05:10'),
(73, 2, 'Login Berhasil', 'Anda berhasil login pada 20-11-2025 03:07:11', 'belum_dibaca', '2025-11-20 02:07:11'),
(74, 2, 'Logout Berhasil', 'Anda berhasil logout pada 20-11-2025 03:07:36', 'belum_dibaca', '2025-11-20 02:07:36'),
(75, 2, 'Login Berhasil', 'Anda berhasil login pada 20-11-2025 03:08:00', 'belum_dibaca', '2025-11-20 02:08:00'),
(76, 10, 'Akun Diverifikasi', 'Selamat! Akun Anda telah diverifikasi. Silakan login untuk menggunakan layanan perpustakaan.', 'belum_dibaca', '2025-11-20 02:08:06'),
(77, 2, 'Logout Berhasil', 'Anda berhasil logout pada 20-11-2025 03:08:07', 'belum_dibaca', '2025-11-20 02:08:07'),
(78, 10, 'Login Berhasil', 'Anda berhasil login pada 20-11-2025 03:08:20', 'belum_dibaca', '2025-11-20 02:08:20'),
(79, 10, 'Logout Berhasil', 'Anda berhasil logout pada 20-11-2025 03:09:52', 'belum_dibaca', '2025-11-20 02:09:52'),
(80, 2, 'Login Berhasil', 'Anda berhasil login pada 20-11-2025 03:09:54', 'belum_dibaca', '2025-11-20 02:09:54'),
(81, 9, 'Peminjaman Berhasil', 'Peminjaman buku \"Pengantar Teknologi Iinformasi : Konsep, Tren dan Implementasi Moderen\" berhasil. Jatuh tempo: 27 November 2025', 'belum_dibaca', '2025-11-20 02:11:05'),
(82, 2, 'Logout Berhasil', 'Anda berhasil logout pada 20-11-2025 03:11:39', 'belum_dibaca', '2025-11-20 02:11:39'),
(83, 1, 'Login Berhasil', 'Anda berhasil login pada 20-11-2025 03:11:43', 'belum_dibaca', '2025-11-20 02:11:43'),
(84, 1, 'Logout Berhasil', 'Anda berhasil logout pada 20-11-2025 03:14:40', 'belum_dibaca', '2025-11-20 02:14:40'),
(85, 10, 'Login Berhasil', 'Anda berhasil login pada 20-11-2025 03:14:48', 'belum_dibaca', '2025-11-20 02:14:48'),
(86, 10, 'Logout Berhasil', 'Anda berhasil logout pada 20-11-2025 03:15:21', 'belum_dibaca', '2025-11-20 02:15:21'),
(87, 2, 'Login Berhasil', 'Anda berhasil login pada 20-11-2025 03:15:23', 'belum_dibaca', '2025-11-20 02:15:23'),
(88, 10, 'Pemesanan Disetujui', 'Pemesanan buku Anda telah disetujui. Silakan datang ke perpustakaan untuk meminjam buku.', 'belum_dibaca', '2025-11-20 02:16:01'),
(89, 2, 'Logout Berhasil', 'Anda berhasil logout pada 20-11-2025 03:16:12', 'belum_dibaca', '2025-11-20 02:16:12'),
(90, 10, 'Login Berhasil', 'Anda berhasil login pada 20-11-2025 03:16:16', 'belum_dibaca', '2025-11-20 02:16:16'),
(91, 10, 'Logout Berhasil', 'Anda berhasil logout pada 20-11-2025 03:17:03', 'belum_dibaca', '2025-11-20 02:17:03'),
(92, 2, 'Login Berhasil', 'Anda berhasil login pada 20-11-2025 03:20:20', 'belum_dibaca', '2025-11-20 02:20:20'),
(93, 9, 'Pengembalian Berhasil', 'Pengembalian buku berhasil dicatat.', 'belum_dibaca', '2025-11-20 02:20:45'),
(94, 2, 'Logout Berhasil', 'Anda berhasil logout pada 20-11-2025 04:15:12', 'belum_dibaca', '2025-11-20 03:15:12'),
(95, 1, 'Login Berhasil', 'Anda berhasil login pada 20-11-2025 04:15:16', 'belum_dibaca', '2025-11-20 03:15:16'),
(96, 1, 'Logout Berhasil', 'Anda berhasil logout pada 20-11-2025 04:15:53', 'belum_dibaca', '2025-11-20 03:15:53');

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id_pemesanan` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `tanggal_pesan` date NOT NULL,
  `status_pemesanan` enum('menunggu','disetujui','ditolak') DEFAULT 'menunggu',
  `alasan_penolakan` text DEFAULT NULL,
  `id_pustakawan` int(11) DEFAULT NULL,
  `tanggal_verifikasi` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemesanan`
--

INSERT INTO `pemesanan` (`id_pemesanan`, `id_anggota`, `id_buku`, `tanggal_pesan`, `status_pemesanan`, `alasan_penolakan`, `id_pustakawan`, `tanggal_verifikasi`) VALUES
(1, 6, 1, '2025-11-18', 'disetujui', NULL, 2, '2025-11-18 21:45:22'),
(2, 7, 1, '2025-11-20', 'disetujui', NULL, 2, '2025-11-20 09:16:01');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_pemesanan` int(11) DEFAULT NULL,
  `id_anggota` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_jatuh_tempo` date NOT NULL,
  `id_pustakawan` int(11) NOT NULL,
  `status` enum('dipinjam','dikembalikan') DEFAULT 'dipinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_pemesanan`, `id_anggota`, `id_buku`, `tanggal_pinjam`, `tanggal_jatuh_tempo`, `id_pustakawan`, `status`) VALUES
(1, NULL, 1, 1, '2025-11-18', '2025-11-25', 2, 'dikembalikan'),
(2, 1, 6, 1, '2025-11-18', '2025-11-25', 2, 'dikembalikan'),
(3, 1, 6, 1, '2025-11-20', '2025-11-27', 2, 'dipinjam');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan_sistem`
--

CREATE TABLE `pengaturan_sistem` (
  `id_pengaturan` int(11) NOT NULL,
  `nama_parameter` varchar(50) NOT NULL,
  `nilai_parameter` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengaturan_sistem`
--

INSERT INTO `pengaturan_sistem` (`id_pengaturan`, `nama_parameter`, `nilai_parameter`, `deskripsi`, `updated_at`) VALUES
(1, 'durasi_peminjaman', '14', 'Durasi peminjaman dalam hari', '2025-11-20 02:14:35'),
(2, 'batas_peminjaman', '3', 'Batas maksimal buku yang dapat dipinjam per anggota', '2025-11-18 12:29:33'),
(3, 'denda_per_hari', '1000000', 'Besaran denda keterlambatan per hari dalam rupiah', '2025-11-20 02:14:35'),
(4, 'email_notifikasi', 'perpustakaan@sdn259bukitsubur.sch.id', 'Email untuk notifikasi sistem', '2025-11-18 12:29:33');

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_pengembalian` int(11) NOT NULL,
  `id_peminjaman` int(11) NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `kondisi_buku` enum('baik','rusak') DEFAULT 'baik',
  `keterlambatan_hari` int(11) DEFAULT 0,
  `denda_keterlambatan` decimal(10,2) DEFAULT 0.00,
  `denda_kerusakan` decimal(10,2) DEFAULT 0.00,
  `total_denda` decimal(10,2) DEFAULT 0.00,
  `status_pembayaran` enum('lunas','belum_lunas') DEFAULT 'belum_lunas',
  `id_pustakawan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengembalian`
--

INSERT INTO `pengembalian` (`id_pengembalian`, `id_peminjaman`, `tanggal_kembali`, `kondisi_buku`, `keterlambatan_hari`, `denda_keterlambatan`, `denda_kerusakan`, `total_denda`, `status_pembayaran`, `id_pustakawan`) VALUES
(1, 1, '2025-11-18', 'baik', 0, 0.00, 0.00, 0.00, 'lunas', 2),
(2, 2, '2025-11-20', 'baik', 0, 0.00, 0.00, 0.00, 'lunas', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pustakawan','anggota') NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'admin', '$2y$10$QujhkSKIIm0hFqMFAl16SeL4rHL84fyu4DDj5R8WLMpjThl0r4Z5u', 'admin', 'aktif', '2025-11-18 12:38:36'),
(2, 'pustakawan', '$2y$10$QujhkSKIIm0hFqMFAl16SeL4rHL84fyu4DDj5R8WLMpjThl0r4Z5u', 'pustakawan', 'aktif', '2025-11-18 12:38:36'),
(3, 'anggota', '$2y$10$QujhkSKIIm0hFqMFAl16SeL4rHL84fyu4DDj5R8WLMpjThl0r4Z5u', 'anggota', 'aktif', '2025-11-18 12:38:36'),
(5, 'yobi', '$2y$10$HRzmtW0udrOSasriHu2Axu3LYGxhmnpY6wxm7jZSJm/JfsWnEG2Mm', 'anggota', 'aktif', '2025-11-18 13:22:49'),
(6, 'alif123', '$2y$10$3qe7inu.7ggANONFWLtTWeMl0EaZedmkomhl034BNTeXx2UKLDv1e', 'anggota', 'nonaktif', '2025-11-18 13:24:54'),
(7, 'fatiah22', '$2y$10$KS6/x4/4Z2dyQ81vmeM4E.o0.p2NXqDe.HyA6RvkSarTj1rzfh0yS', 'anggota', 'aktif', '2025-11-18 13:26:08'),
(8, 'ichsandi', '$2y$10$8KF1VcXUL8iX1azEPdIu8.srgXGolKZ0ZG86MxAB2COB59gRChw02', 'anggota', 'nonaktif', '2025-11-18 13:40:44'),
(9, 'bobyboboboy', '$2y$10$04v6GvT35gdHbwjZHtL5NeY4qtzOhh7kcj7dJwZHFMq5b0QOyibG6', 'anggota', 'aktif', '2025-11-18 14:29:23'),
(10, 'aydiladha', '$2y$10$jSGf61pkz6Pk5v0IzdBwUu9goY8lEvp0IguP09EtcXJiUrvLPwejC', 'anggota', 'aktif', '2025-11-20 02:06:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD KEY `id_anggota` (`id_anggota`),
  ADD KEY `id_buku` (`id_buku`),
  ADD KEY `id_pustakawan` (`id_pustakawan`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_pemesanan` (`id_pemesanan`),
  ADD KEY `id_anggota` (`id_anggota`),
  ADD KEY `id_buku` (`id_buku`),
  ADD KEY `id_pustakawan` (`id_pustakawan`);

--
-- Indexes for table `pengaturan_sistem`
--
ALTER TABLE `pengaturan_sistem`
  ADD PRIMARY KEY (`id_pengaturan`),
  ADD UNIQUE KEY `nama_parameter` (`nama_parameter`);

--
-- Indexes for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`),
  ADD KEY `id_peminjaman` (`id_peminjaman`),
  ADD KEY `id_pustakawan` (`id_pustakawan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id_pemesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengaturan_sistem`
--
ALTER TABLE `pengaturan_sistem`
  MODIFY `id_pengaturan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_pengembalian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `anggota_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `karyawan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemesanan_ibfk_3` FOREIGN KEY (`id_pustakawan`) REFERENCES `karyawan` (`id_karyawan`);

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_3` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_4` FOREIGN KEY (`id_pustakawan`) REFERENCES `karyawan` (`id_karyawan`);

--
-- Constraints for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengembalian_ibfk_2` FOREIGN KEY (`id_pustakawan`) REFERENCES `karyawan` (`id_karyawan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
