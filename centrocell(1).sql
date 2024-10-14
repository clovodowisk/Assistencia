-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2024 at 02:09 AM
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
-- Database: `centrocell`
--

-- --------------------------------------------------------

--
-- Table structure for table `servicos`
--

CREATE TABLE `servicos` (
  `id` int(11) NOT NULL,
  `os` varchar(255) NOT NULL,
  `modelo` varchar(255) NOT NULL,
  `servico` varchar(255) NOT NULL,
  `prazo` datetime NOT NULL,
  `responsavel_os` varchar(255) NOT NULL,
  `tecnico` varchar(255) NOT NULL,
  `carimbo` datetime NOT NULL,
  `entrega` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `servicos`
--

INSERT INTO `servicos` (`id`, `os`, `modelo`, `servico`, `prazo`, `responsavel_os`, `tecnico`, `carimbo`, `entrega`) VALUES
(6, '0002', 'G22', 'Troca do conector', '0000-00-00 00:00:00', 'Gustavo', 'Gustavo', '2024-10-13 23:27:37', 0),
(9, '0003', 'A33', 'Troca do vidro', '2024-10-17 12:35:00', 'Midiã', 'Gustavo', '2024-10-13 19:14:57', 0),
(10, '0004', 'A54', 'Troca do auto falante', '2024-10-22 14:50:00', 'Janaina', 'Kevin', '2024-10-13 19:15:30', 0),
(11, '0005', 'A12', 'Troca da tela', '2024-10-22 14:50:00', 'Janaina', 'Maria', '2024-10-13 19:21:11', 1),
(12, '0006', 'A31', 'Troca da tela ', '2024-10-15 09:20:00', 'Janaina', 'Kevin', '2024-10-13 20:22:57', 1);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'kevintec', '$2y$10$TlbRx1ldheSr8.q3ZyL0ouFWMfKJo77qwZgjQ5HdWtTlnswQ2ZSLu', '2024-10-13 20:26:36'),
(6, 'teste', '$2y$10$fjD3MtRJ25ghiviY8gRuJ..20d8DkpFcdF6hejpkinw92ZxNbzqOq', '2024-10-14 00:09:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
