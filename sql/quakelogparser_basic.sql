-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 29-Jan-2018 às 17:06
-- Versão do servidor: 5.7.19
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quakelogparser`
--

--
-- Estrutura da tabela `games`
--

DROP TABLE IF EXISTS `games`;
CREATE TABLE IF NOT EXISTS `games` (
  `Game_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Game_Name` varchar(50) NOT NULL,
  `Tot_Kills` int(10) NOT NULL,
  PRIMARY KEY (`Game_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2074 DEFAULT CHARSET=latin1;

--
-- Estrutura da tabela `kills`
--

DROP TABLE IF EXISTS `kills`;
CREATE TABLE IF NOT EXISTS `kills` (
  `Kills_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Game_id` int(11) NOT NULL,
  `Nick_Name` varchar(50) NOT NULL,
  `Causa_Mortis` varchar(50) NOT NULL,
  PRIMARY KEY (`Kills_Id`),
  KEY `Game_id_fk` (`Game_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=54307 DEFAULT CHARSET=latin1;

--
-- Estrutura da tabela `players`
--

DROP TABLE IF EXISTS `players`;
CREATE TABLE IF NOT EXISTS `players` (
  `Player_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Game_Id` int(11) NOT NULL,
  `Nick_Name` varchar(50) NOT NULL,
  `Player_Kills` int(11) NOT NULL,
  PRIMARY KEY (`Player_Id`),
  KEY `Game_Id_Fk` (`Game_Id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11125 DEFAULT CHARSET=latin1;


--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `kills`
--
ALTER TABLE `kills`
  ADD CONSTRAINT `kills_ibfk_1` FOREIGN KEY (`Game_id`) REFERENCES `games` (`Game_Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limitadores para a tabela `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`Game_Id`) REFERENCES `games` (`Game_Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
