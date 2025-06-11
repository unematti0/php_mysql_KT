-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Loomise aeg: Juuni 11, 2025 kell 08:12 EL
-- Serveri versioon: 10.4.32-MariaDB
-- PHP versioon: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Andmebaas: `autod`
--

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `broneering`
--

CREATE TABLE `broneering` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `klient_id` int(11) NOT NULL,
  `teenus_id` int(11) NOT NULL,
  `tookoht_id` int(11) NOT NULL,
  `kuupaev` date NOT NULL,
  `algus_kellaaeg` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Andmete tõmmistamine tabelile `broneering`
--

INSERT INTO `broneering` (`id`, `klient_id`, `teenus_id`, `tookoht_id`, `kuupaev`, `algus_kellaaeg`) VALUES
(1, 1, 1, 1, '2025-06-10', '09:00:00'),
(2, 2, 2, 2, '2025-06-10', '10:00:00'),
(3, 3, 3, 3, '2025-06-10', '11:00:00'),
(4, 4, 4, 1, '2025-06-10', '12:00:00'),
(5, 5, 5, 2, '2025-06-11', '09:00:00'),
(6, 1, 3, 3, '2025-06-11', '10:00:00'),
(8, 3, 2, 2, '2025-06-12', '08:30:00'),
(9, 4, 4, 3, '2025-06-12', '10:00:00'),
(10, 5, 1, 1, '2025-06-12', '11:30:00');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `kasutaja`
--

CREATE TABLE `kasutaja` (
  `id` int(11) NOT NULL,
  `kasutaja` varchar(50) NOT NULL,
  `parool` varchar(255) NOT NULL,
  `privileegid` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Andmete tõmmistamine tabelile `kasutaja`
--

INSERT INTO `kasutaja` (`id`, `kasutaja`, `parool`, `privileegid`) VALUES
(1, 'admin', '$2y$10$jLn.YeAEsggNlAT8k65Eq.Owwp4BMikNuNtFUBbETwU2mK8Jjv3pG', 'admin');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `klient`
--

CREATE TABLE `klient` (
  `id` int(11) NOT NULL,
  `eesnimi` varchar(50) NOT NULL,
  `perekonnanimi` varchar(50) NOT NULL,
  `isikukood` varchar(11) NOT NULL,
  `email` varchar(100) NOT NULL
) ;

--
-- Andmete tõmmistamine tabelile `klient`
--

INSERT INTO `klient` (`id`, `eesnimi`, `perekonnanimi`, `isikukood`, `email`) VALUES
(1, 'Mati', 'Maasikas', '38101010000', 'mati@example.com'),
(2, 'Kati', 'Kask', '49502230011', 'kati.kask@example.ee'),
(3, 'Jaan', 'Jõgi', '38807070022', 'jaan.jogi@example.com'),
(4, 'Liis', 'Lind', '48603150033', 'liis.lind@example.ee'),
(5, 'Toomas', 'Tamm', '37912120044', 'toomas.tamm@example.com');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `teenus`
--

CREATE TABLE `teenus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nimi` varchar(100) NOT NULL,
  `kirjeldus` text DEFAULT NULL,
  `kestus` int(11) NOT NULL,
  `hind` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Andmete tõmmistamine tabelile `teenus`
--

INSERT INTO `teenus` (`id`, `nimi`, `kirjeldus`, `kestus`, `hind`) VALUES
(1, 'Õlivahetus', 'Mootoriõli ja filtri vahetus', 30, 45.00),
(2, 'Diagnostika', 'Sõiduki elektrooniline diagnostika', 60, 60.00),
(3, 'Rehvivahetus', 'Suve-/talverehvide vahetus', 45, 35.00),
(4, 'Piduriklotside vahetus', 'Esi- või tagapiduriklotside vahetus', 90, 85.00),
(5, 'Kliimaseadme kontroll', 'Kliimaseadme testimine ja täitmine', 60, 55.00);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `tookoht`
--

CREATE TABLE `tookoht` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nimi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Andmete tõmmistamine tabelile `tookoht`
--

INSERT INTO `tookoht` (`id`, `nimi`) VALUES
(1, 'Töökoht 1'),
(2, 'Töökoht 2'),
(3, 'Töökoht 3');

--
-- Indeksid tõmmistatud tabelitele
--

--
-- Indeksid tabelile `broneering`
--
ALTER TABLE `broneering`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tookoht_id` (`tookoht_id`,`kuupaev`,`algus_kellaaeg`);

--
-- Indeksid tabelile `kasutaja`
--
ALTER TABLE `kasutaja`
  ADD PRIMARY KEY (`id`);

--
-- Indeksid tabelile `klient`
--
ALTER TABLE `klient`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isikukood` (`isikukood`);

--
-- Indeksid tabelile `teenus`
--
ALTER TABLE `teenus`
  ADD PRIMARY KEY (`id`);

--
-- Indeksid tabelile `tookoht`
--
ALTER TABLE `tookoht`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nimi` (`nimi`);

--
-- AUTO_INCREMENT tõmmistatud tabelitele
--

--
-- AUTO_INCREMENT tabelile `broneering`
--
ALTER TABLE `broneering`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT tabelile `kasutaja`
--
ALTER TABLE `kasutaja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT tabelile `klient`
--
ALTER TABLE `klient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT tabelile `teenus`
--
ALTER TABLE `teenus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT tabelile `tookoht`
--
ALTER TABLE `tookoht`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
