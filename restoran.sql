-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2025 at 01:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restoran`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `username` varchar(50) DEFAULT NULL,
  `password` char(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username`, `password`) VALUES
('admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `menu` varchar(250) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` decimal(10,3) NOT NULL,
  `kategori` varchar(250) NOT NULL,
  `gambar` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `menu`, `deskripsi`, `harga`, `kategori`, `gambar`) VALUES
(1, 'Ayam Bakar', 'Hidangan Asia Tenggara Maritim, terutama hidangan Indonesia atau Malaysia, dari ayam yang dipanggang di atas arang.', 12000.000, 'Makanan', 'ayamb.webp'),
(2, 'Rendang', 'Daging yang dimasak dengan santan dan rempah-rempah hingga kuahnya kering, sehingga hanya tersisa daging dengan bumbu yang melekat.', 13000.000, 'Makanan', 'rendang.jpg'),
(3, 'Kikil', 'Bagian kaki sapi yang mengandung kulit, tulang rawan, dan otot.', 16000.000, 'Makanan', 'kikil.jpg'),
(4, 'Ayam Gulai', 'Hidangan Indonesia yang menggabungkan daging ayam dengan kuah santan kental yang kaya rempah.', 12000.000, 'Makanan', 'ayamg.webp'),
(5, 'Telur Dadar', 'Variasi hidangan telur goreng yang disiapkan dengan cara mengocok telur terlebih dahulu dan menggorengnya dengan minyak goreng atau mentega panas pada sebuah wajan.', 7000.000, 'Makanan', 'telurd.jpg'),
(6, 'Kerupuk Kulit / Jangek', 'Kerupuk yang terbuat dari kulit hewan, biasanya kulit sapi atau kerbau, yang diolah menjadi bentuk tipis dan digoreng hingga renyah.', 6000.000, 'Makanan', 'jangek.jpg'),
(7, 'Gulai Otak', 'Hidangan gulai yang bahan utamanya adalah otak sapi atau kambing yang dimasak dengan kuah santan bumbu rempah pedas, khas dari Minangkabau, Sumatera Barat.', 13000.000, 'Makanan', 'otak.jpg'),
(8, 'Dendeng Balado', 'Hidangan khas Sumatera Barat yang terbuat dari daging sapi tipis yang dikeringkan dan digoreng hingga garing, kemudian dibumbui dengan bumbu balado yang pedas dan gurih.', 13000.000, 'Makanan', 'dendeng.jpg'),
(9, 'Daun Singkong', 'Daun dari tanaman singkong (Manihot esculenta) yang bisa diolah dan dikonsumsi sebagai sayuran.', 8000.000, 'Makanan', 'singkong.jpg'),
(10, 'Sayur Nangka', 'Masakan sayur yang menggunakan buah nangka, terutama nangka muda, sebagai bahan utama.', 8000.000, 'Makanan', 'nangka.jpg'),
(11, 'Nasi Putih', 'Makanan pokok yang dihasilkan dari beras putih yang telah diolah, di mana kulit arinya telah dibuang.', 7000.000, 'Makanan', 'nasput.jpg'),
(12, 'Ayam Goreng', 'Hidangan yang terbuat dari daging ayam yang digoreng, baik itu dengan atau tanpa lapisan tepung atau adonan.', 12000.000, 'Makanan', 'ayamgo.avif');

-- --------------------------------------------------------

--
-- Table structure for table `minuman`
--

CREATE TABLE `minuman` (
  `id` int(11) NOT NULL,
  `minuman` varchar(250) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` decimal(10,3) NOT NULL,
  `kategori` varchar(250) NOT NULL,
  `gambar` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `minuman`
--

INSERT INTO `minuman` (`id`, `minuman`, `deskripsi`, `harga`, `kategori`, `gambar`) VALUES
(1, 'Air Putih', 'Air yang bersih dan jernih yang dapat diminum.', 3000.000, 'Minuman', 'air.jpg'),
(2, 'Es Teh', 'Minuman teh yang disajikan dalam keadaan dingin, dengan tambahan es batu dan biasanya juga gula.', 4000.000, 'Minuman', 'es teh.jpg'),
(3, 'Es Jeruk', 'Minuman segar yang terbuat dari perasan jeruk segar, air, dan gula yang disajikan dengan es batu.', 4000.000, 'Minuman', 'es jeruk.jpg'),
(4, 'Kopi', 'Minuman seduh yang terbuat dari biji kopi yang telah disangrai dan dihaluskan menjadi bubuk.', 4000.000, 'Minuman', 'KOPI ARKANNNNNNN.webp');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `minuman`
--
ALTER TABLE `minuman`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `minuman`
--
ALTER TABLE `minuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
