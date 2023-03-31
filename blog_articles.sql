-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 31, 2023 at 08:58 PM
-- Server version: 5.7.36
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blog`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id_article` int(11) NOT NULL AUTO_INCREMENT,
  `date_pub` date NOT NULL,
  `date_modif` date DEFAULT NULL,
  `contenu` text NOT NULL,
  `login` varchar(255) NOT NULL,
  PRIMARY KEY (`id_article`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`id_article`, `date_pub`, `date_modif`, `contenu`, `login`) VALUES
(6, '2023-03-29', NULL, 'En moyenne 650 parisiens par an sont hospitalisés car ils ont glissé sur une crotte de chien', 'publisher1'),
(7, '2023-03-29', NULL, 'Avec un mouton on peut faire 14 pull over', 'publisher1'),
(8, '2023-03-31', NULL, 'La banane ne peut pas se reproduire par elle-même. La manipulation humaine est la seule manière de la propager', 'publisher2'),
(9, '2023-03-31', NULL, 'Les aéroports situés en haute altitude nécessitent des pistes d\'envol plus longues car la densité de l\'air y est moindre', 'publisher3'),
(10, '2023-03-31', NULL, 'Les gens intelligents ont davantage de zinc et de cuivre dans leurs cheveux', 'publisher3'),
(11, '2023-03-31', NULL, 'Si vous vous trouvez au fond d\'un puits ou d\'une cheminée, regarder vers le haut vous permettra de voir les étoiles, même en plein jour', 'publisher1'),
(12, '2023-03-31', NULL, 'La framboise est le seul fruit dont la graine pousse à l\'extérieur', 'publisher2');

-- --------------------------------------------------------

--
-- Table structure for table `liker`
--

DROP TABLE IF EXISTS `liker`;
CREATE TABLE IF NOT EXISTS `liker` (
  `login` varchar(255) NOT NULL,
  `id_article` int(11) NOT NULL,
  `typeLike` enum('-1','1') NOT NULL,
  PRIMARY KEY (`login`,`id_article`),
  KEY `id_article` (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `liker`
--

INSERT INTO `liker` (`login`, `id_article`, `typeLike`) VALUES
('publisher1', 8, '-1'),
('publisher1', 9, '1'),
('publisher2', 6, '1'),
('publisher2', 7, '-1'),
('publisher3', 6, '1'),
('publisher3', 7, '-1');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `login` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'ANON',
  PRIMARY KEY (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`login`, `mdp`, `username`, `role`) VALUES
('moderator1', '$2y$10$p2i1JQS0i3QnjVbvQvCLS.EXwiX3KVk1mEOkOJWo3ANLr.h7k9irq', 'Bruno Armel', 'Moderator'),
('moderator2', '$2y$10$nE9.rkUdZDaQdngbGGawbufnWKCSoinbwjwrWzXxX6kplrDfiB7bi', 'Miguel Rondon', 'Moderator'),
('moderator3', '$2y$10$pj/WpQFEuhiu/8fFGbQKMeNuKq6bhcFJkwNV1QfHrXcKq/D4KLzja', 'Vincent Vigouroux', 'Moderator'),
('publisher1', '$2y$10$zItpXetTiRC0Rsr7gM7YFuWnOuO0gqxgHIIIuLV8Mkr7gAV0kgSOa', 'J.K Rowling', 'Publisher'),
('publisher2', '$2y$10$5GIfn1zC0PcPvXPUfMiyU.OWuzZdFVIiowpeNEJBlWPI.lj3cKTxy', 'Paul Tierney', 'Publisher'),
('publisher3', '$2y$10$BePrW5nsEStdNncrDgBZJueSuFPTyWPS5JX/yaBrbWnhF/PODCWoy', 'Clement Noguero', 'Publisher');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `liker`
--
ALTER TABLE `liker`
  ADD CONSTRAINT `liker_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `liker_ibfk_2` FOREIGN KEY (`login`) REFERENCES `user` (`login`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
