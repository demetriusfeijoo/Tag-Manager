SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `slugs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `data_criacao` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` int(10) unsigned NOT NULL,
  `nome` varchar(100) NOT NULL,
  `data_criacao` datetime NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `cliques` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `slug_fk` (`slug`),
  KEY `status` (`status`),
  KEY `cliques` (`cliques`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


ALTER TABLE `tags`
  ADD CONSTRAINT `slug` FOREIGN KEY (`slug`) REFERENCES `slugs` (`id`);
