-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2025 at 06:51 PM
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
-- Database: `ess_projeto`
--

-- --------------------------------------------------------

--
-- Table structure for table `errors`
--

CREATE TABLE `errors` (
  `id` int(11) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `error_type` varchar(100) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `reviewed` tinyint(4) DEFAULT 0,
  `user_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `errors`
--

INSERT INTO `errors` (`id`, `timestamp`, `error_type`, `details`, `filename`, `reviewed`, `user_id`, `student_id`, `feedback`) VALUES
(5686, '2025-06-02 17:39:05', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250602_173902.mp4', 1, NULL, 1, NULL),
(5687, '2025-06-03 18:22:30', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_182227.mp4', 1, NULL, 1, NULL),
(5688, '2025-06-03 18:45:15', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_184512.mp4', 0, NULL, NULL, NULL),
(5689, '2025-06-03 18:46:28', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_184626.mp4', 0, NULL, NULL, NULL),
(5690, '2025-06-03 18:47:23', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_184721.mp4', 0, NULL, NULL, NULL),
(5691, '2025-06-03 18:48:10', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_184808.mp4', 0, NULL, NULL, NULL),
(5692, '2025-06-03 18:48:34', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_184831.mp4', 0, NULL, NULL, NULL),
(5693, '2025-06-03 18:49:43', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_184941.mp4', 0, NULL, NULL, NULL),
(5694, '2025-06-03 18:53:28', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_185325.mp4', 0, NULL, NULL, NULL),
(5695, '2025-06-03 18:57:35', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_185733.mp4', 0, NULL, NULL, NULL),
(5696, '2025-06-03 19:02:23', 'Execução Incorreta', 'Esq: erro, Dir: inicial', 'videos/erro_20250603_190220.mp4', 0, NULL, NULL, NULL),
(5697, '2025-06-03 19:02:29', 'Execução Incorreta', 'Esq: erro, Dir: inicial', 'videos/erro_20250603_190226.mp4', 0, NULL, NULL, NULL),
(5698, '2025-06-03 19:02:45', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_190242.mp4', 0, NULL, NULL, NULL),
(5699, '2025-06-03 19:04:21', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_190417.mp4', 1, NULL, 1, NULL),
(5700, '2025-06-03 19:51:08', 'Execução Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_195105_h264.mp4', 0, NULL, NULL, NULL),
(5701, '2025-06-03 20:19:04', 'Execucao Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_201900_h264.mp4', 0, NULL, NULL, NULL),
(5702, '2025-06-03 20:19:21', 'Execucao Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_201918_h264.mp4', 0, NULL, NULL, NULL),
(5703, '2025-06-03 20:19:52', 'Execucao Incorreta', 'Esq: erro, Dir: inicial', 'videos/erro_20250603_201948_h264.mp4', 0, NULL, NULL, NULL),
(5704, '2025-06-03 20:20:02', 'Execucao Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_201958_h264.mp4', 0, NULL, NULL, NULL),
(5705, '2025-06-03 21:30:11', 'Execucao Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_213007_h264.mp4', 0, NULL, NULL, NULL),
(5706, '2025-06-03 21:30:20', 'Execucao Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_213017_h264.mp4', 0, NULL, NULL, NULL),
(5707, '2025-06-03 21:30:39', 'Execucao Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250603_213035_h264.mp4', 0, NULL, NULL, NULL),
(5708, '2025-06-04 11:19:08', 'Execucao Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250604_111903_h264.mp4', 0, NULL, NULL, NULL),
(5709, '2025-06-04 11:23:51', 'Execucao Incorreta', 'Esq: erro, Dir: erro', 'videos/erro_20250604_112347_h264.mp4', 1, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `idade` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `contacto_emergencia` varchar(50) DEFAULT NULL,
  `historico_lesoes` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `nome`, `idade`, `foto`, `contacto_emergencia`, `historico_lesoes`, `observacoes`) VALUES
(1, 'Guilherme', 21, 'uploads/aluno_683f554372d95.png', '969418292', 'Lesão no ombro direito em 2023. Atualmente recuperado, mas com restrição para exercícios de carga excessiva e movimentos acima da cabeça.', 'Aluno motivado e dedicado. Foco no fortalecimento muscular e prevenção de novas lesões. Recomenda-se atenção especial em exercícios de membros superiores e acompanhamento regular.\r\n\r\n'),
(2, 'Afonso', 21, 'uploads/aluno_683f5567bb470.png', '12345678', 'Lesao no ombro', 'Ter em conta que te lesao no ombro por isso ter muito atença durante a execuçao'),
(3, 'Teixeira', 21, 'uploads/aluno_683f5523b839d.jpg', '969418292', '32', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`) VALUES
(1, '2221851', '1234', 'Guilherme Teixeira');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `errors`
--
ALTER TABLE `errors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `errors`
--
ALTER TABLE `errors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5710;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `errors`
--
ALTER TABLE `errors`
  ADD CONSTRAINT `errors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `errors_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
