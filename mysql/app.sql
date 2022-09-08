-- Adminer 4.8.1 MySQL 8.0.22 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `companies`;
CREATE TABLE `companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` double NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthday` date NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_company` int NOT NULL,
  `first_day` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_company` (`id_company`),
  CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`id_company`) REFERENCES `companies` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` float NOT NULL,
  `tax` float NOT NULL,
  `stock` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `name`, `price`, `tax`, `stock`) VALUES
(2,	'Run24',	3285.85,	18,	437),
(3,	'Arty',	1588.16,	20,	188),
(4,	'DeskGet',	1600.26,	14,	145),
(5,	'Redsky',	2616.02,	23,	231),
(6,	'Ingocal',	533.27,	21,	372),
(7,	'Ingocal',	4563.3,	16,	212),
(8,	'ROF',	2514.42,	14,	412),
(9,	'Yola',	990.05,	14,	465),
(10,	'Trafor',	946.27,	23,	320),
(11,	'Prayons',	1606.29,	15,	300),
(12,	'Fdeam',	2686.56,	22,	143),
(13,	'Nime',	4500.96,	23,	397),
(14,	'Koob',	2074.69,	19,	126),
(15,	'SolSol',	1736.09,	21,	399),
(16,	'Fdeam',	3383.35,	16,	299),
(62,	'product B',	2999.99,	10,	800);

DROP TABLE IF EXISTS `providers`;
CREATE TABLE `providers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_company` int NOT NULL,
  `id_client` int DEFAULT NULL,
  `id_provider` int DEFAULT NULL,
  `id_product` int NOT NULL,
  `quantity_product` int NOT NULL,
  `id_employee` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_company` (`id_company`),
  KEY `id_client` (`id_client`),
  KEY `id_provider` (`id_provider`),
  KEY `id_product` (`id_product`),
  KEY `id_employee` (`id_employee`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`id_company`) REFERENCES `companies` (`id`),
  CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id`),
  CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`id_provider`) REFERENCES `providers` (`id`),
  CONSTRAINT `transactions_ibfk_4` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`),
  CONSTRAINT `transactions_ibfk_5` FOREIGN KEY (`id_employee`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2022-09-08 19:00:46
