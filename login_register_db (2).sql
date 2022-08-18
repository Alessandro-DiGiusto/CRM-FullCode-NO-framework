-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Ago 17, 2022 alle 13:36
-- Versione del server: 10.4.21-MariaDB
-- Versione PHP: 7.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `login_register_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `contratti`
--

CREATE TABLE `contratti` (
  `id` int(10) NOT NULL,
  `r_sociale` varchar(255) NOT NULL,
  `iban` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tel` varchar(15) NOT NULL,
  `FK_id_users` int(11) DEFAULT NULL,
  `stipula` date DEFAULT NULL,
  `stato` varchar(20) NOT NULL DEFAULT 'INSERITO',
  `insert_date` varchar(255) DEFAULT NULL,
  `via_for` varchar(255) NOT NULL,
  `cap_for` int(6) NOT NULL,
  `comune_for` varchar(100) NOT NULL,
  `citta_for` varchar(2) NOT NULL,
  `luce` varchar(1) DEFAULT NULL,
  `gas` varchar(1) DEFAULT NULL,
  `luce_gas` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `contratti`
--

INSERT INTO `contratti` (`id`, `r_sociale`, `iban`, `email`, `tel`, `FK_id_users`, `stipula`, `stato`, `insert_date`, `via_for`, `cap_for`, `comune_for`, `citta_for`, `luce`, `gas`, `luce_gas`) VALUES
(206, 'Prova 1', '123', 'ale@gmail.com', '16546165216', 1, '2022-01-01', 'INSERITO', '13:35:11 |  17-08-2022', 'viao letargo', 1231, 'giada', 'FA', 'X', NULL, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(1, 'Alessandro', 'ale@gmail.com', '202cb962ac59075b964b07152d234b70'),
(2, 'Silvia Petralia', 'silvia@gmail.com', '202cb962ac59075b964b07152d234b70'),
(7, 'provadel9', 'provadel9@gmail.com', '202cb962ac59075b964b07152d234b70'),
(14, 'Mario Rossi', 'mario@gmail.com', '202cb962ac59075b964b07152d234b70'),
(15, 'Roberto', 'roberto@gmail.com', '202cb962ac59075b964b07152d234b70'),
(16, 'Silvia', 'silvia@gmaill.com', '202cb962ac59075b964b07152d234b70');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `contratti`
--
ALTER TABLE `contratti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `insert_date` (`insert_date`),
  ADD KEY `FK_id_users` (`FK_id_users`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `contratti`
--
ALTER TABLE `contratti`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `contratti`
--
ALTER TABLE `contratti`
  ADD CONSTRAINT `contratti_ibfk_1` FOREIGN KEY (`FK_id_users`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

