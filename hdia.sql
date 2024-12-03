-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-12-2024 a las 12:17:08
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `hdia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conversations`
--

CREATE TABLE `conversations` (
  `conversation_id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `last_message_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `conversations`
--

INSERT INTO `conversations` (`conversation_id`, `user1_id`, `user2_id`, `last_message_time`) VALUES
(1, 9, 2, '2024-10-23 13:12:24'),
(2, 4, 2, '2024-10-23 16:04:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forms`
--

CREATE TABLE `forms` (
  `form_id` int(11) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `form_type` enum('internacion','sacarato','quimioterapia') NOT NULL,
  `nombre_pte` varchar(255) NOT NULL,
  `hc_pte` int(11) NOT NULL,
  `dni_pte` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `status` enum('en revision','rechazado','correcto','') NOT NULL,
  `fecha_tto` varchar(255) DEFAULT NULL,
  `fecha_1` varchar(255) DEFAULT NULL,
  `fecha_2` varchar(255) DEFAULT NULL,
  `fecha_3` varchar(255) DEFAULT NULL,
  `fecha_4` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `forms`
--

INSERT INTO `forms` (`form_id`, `creator_id`, `form_type`, `nombre_pte`, `hc_pte`, `dni_pte`, `creation_date`, `file_name`, `status`, `fecha_tto`, `fecha_1`, `fecha_2`, `fecha_3`, `fecha_4`) VALUES
(4, 4, 'quimioterapia', '', 36500461, 36500461, '2024-10-29 16:10:03', '4_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(6, 6, 'quimioterapia', '', 0, 14106827, '2024-10-29 18:26:54', '6_quimioterapia.pdf', 'rechazado', NULL, NULL, NULL, NULL, NULL),
(8, 2, 'quimioterapia', '', 14413236, 14413236, '2024-10-30 15:45:59', '8_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(10, 5, 'quimioterapia', '', 5483250, 5483250, '2024-10-31 09:25:05', '10_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(11, 5, 'quimioterapia', '', 5972348, 5972348, '2024-10-31 09:36:27', '11_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(13, 2, 'quimioterapia', '', 35996408, 35996408, '2024-10-31 10:30:12', '13_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(14, 4, 'quimioterapia', '', 10816596, 10816596, '2024-10-31 11:02:37', '14_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(15, 4, 'quimioterapia', '', 5735721, 5735721, '2024-10-31 11:30:08', '15_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(18, 4, 'quimioterapia', '', 11513480, 11513480, '2024-11-01 10:03:23', '18_quimioterapia.pdf', 'correcto', '04/11/2024', '25/11/2024', '02/12/2024', '12/12/2024', NULL),
(19, 4, 'quimioterapia', '', 13869758, 13869758, '2024-11-01 10:55:18', '19_quimioterapia.pdf', 'correcto', '05/11/2024', '26/11/2024', '17/12/2024', NULL, NULL),
(20, 4, 'quimioterapia', '', 11019900, 11019900, '2024-11-01 12:40:48', '20_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(21, 2, 'quimioterapia', '', 0, 0, '2024-11-01 12:46:17', '21_quimioterapia.pdf', 'rechazado', '25/10/2024', '25/02/2024', NULL, NULL, NULL),
(22, 2, 'quimioterapia', '', 0, 0, '2024-11-01 12:53:03', '22_quimioterapia.pdf', 'rechazado', '30/12/2024', '31/12/2024', NULL, NULL, NULL),
(23, 4, 'quimioterapia', '', 12171271, 12171271, '2024-11-01 13:01:45', '23_quimioterapia.pdf', 'correcto', '04/11/2024', NULL, NULL, NULL, NULL),
(24, 4, 'quimioterapia', '', 10848624, 10848624, '2024-11-01 13:41:24', '24_quimioterapia.pdf', 'correcto', '06/11/2024', '27/11/2024', NULL, NULL, NULL),
(25, 6, 'quimioterapia', '', 5414975, 5414975, '2024-11-01 15:21:06', '25_quimioterapia.pdf', 'correcto', '26/11/2024', '03/12/2024', '10/12/2024', NULL, NULL),
(26, 6, 'quimioterapia', '', 6672705, 6672705, '2024-11-01 16:08:33', '26_quimioterapia.pdf', 'rechazado', '15/11/2024', '06/12/2024', '27/12/2024', '17/01/2025', NULL),
(27, 4, 'quimioterapia', '', 11059626, 11059626, '2024-11-01 17:17:06', '27_quimioterapia.pdf', 'correcto', '04/11/2024', NULL, NULL, NULL, NULL),
(28, 5, 'quimioterapia', '', 0, 12917550, '2024-11-04 09:50:19', '28_quimioterapia.pdf', 'correcto', '21/11/2024', '21/11/2024', '22/11/2024', NULL, NULL),
(29, 5, 'quimioterapia', '', 0, 12917550, '2024-11-04 09:50:27', '29_quimioterapia.pdf', 'rechazado', '21/11/2024', '21/11/2024', '22/11/2024', NULL, NULL),
(30, 5, 'quimioterapia', '', 0, 12917550, '2024-11-04 09:50:29', '30_quimioterapia.pdf', 'rechazado', '21/11/2024', '21/11/2024', '22/11/2024', NULL, NULL),
(31, 5, 'quimioterapia', '', 0, 12917550, '2024-11-04 09:50:33', '31_quimioterapia.pdf', 'rechazado', '21/11/2024', '21/11/2024', '22/11/2024', NULL, NULL),
(32, 5, 'quimioterapia', 'cancinos mario', 0, 8109644, '2024-11-04 09:59:29', '32_quimioterapia.pdf', 'correcto', '21/11/0124', '21/11/2024', '22/11/2024', NULL, NULL),
(33, 5, 'quimioterapia', 'cancinos mario', 0, 8109644, '2024-11-04 09:59:32', '33_quimioterapia.pdf', 'rechazado', '21/11/0124', '21/11/2024', '22/11/2024', NULL, NULL),
(34, 5, 'quimioterapia', 'chaperon juan manuel', 0, 39316486, '2024-11-04 10:14:01', '34_quimioterapia.pdf', 'rechazado', '07/11/2024', '07/11/2024', '11/11/2024', '14/11/2024', '18/11/2024'),
(35, 5, 'quimioterapia', 'harok ricardo', 0, 14355619, '2024-11-04 10:18:32', '35_quimioterapia.pdf', 'correcto', '25/11/2024', '25/11/2024', NULL, NULL, NULL),
(36, 5, 'quimioterapia', 'ohannessian ana maria', 0, 12634810, '2024-11-04 10:27:09', '36_quimioterapia.pdf', 'correcto', '19/11/2024', '19/11/2024', '05/12/2024', NULL, NULL),
(37, 5, 'quimioterapia', 'nadaff sergio', 0, 14937438, '2024-11-04 10:34:47', '37_quimioterapia.pdf', 'correcto', '25/11/2024', '25/11/2024', NULL, NULL, NULL),
(38, 5, 'quimioterapia', 'transito navarro', 0, 11365118, '2024-11-04 10:39:43', '38_quimioterapia.pdf', 'correcto', '12/11/2024', '12/11/2024', NULL, NULL, NULL),
(39, 5, 'quimioterapia', 'sachetti jose ', 0, 5255298, '2024-11-04 10:46:15', '39_quimioterapia.pdf', 'correcto', '12/11/2024', NULL, NULL, NULL, NULL),
(40, 5, 'quimioterapia', 'sanabria avalos julio', 0, 93285331, '2024-11-04 10:54:54', '40_quimioterapia.pdf', 'correcto', '13/11/2024', NULL, NULL, NULL, NULL),
(41, 5, 'quimioterapia', 'NICOLAS VALENZUELA', 0, 39979780, '2024-11-04 11:12:49', '41_quimioterapia.pdf', 'correcto', '30/10/2024', '13/11/2024', '27/11/2024', '11/12/2024', NULL),
(42, 5, 'quimioterapia', 'acuña soledad', 0, 9304689, '2024-11-04 11:16:49', '42_quimioterapia.pdf', 'correcto', '11/11/0024', NULL, NULL, NULL, NULL),
(44, 6, 'quimioterapia', 'Ruiz Emilia', 12797522, 12797522, '2024-11-04 13:21:43', '44_quimioterapia.pdf', 'rechazado', '05/11/2024', '02/12/2024', '30/12/2024', '27/01/2025', NULL),
(45, 4, 'quimioterapia', 'Flammia Ana María', 13987169, 13987169, '2024-11-04 13:48:19', '45_quimioterapia.pdf', 'correcto', '05/11/2024', '12/11/2024', '19/11/2024', '26/11/2024', NULL),
(46, 4, 'quimioterapia', 'Juarez Osvaldo', 13482920, 13482920, '2024-11-04 13:55:33', '46_quimioterapia.pdf', 'rechazado', '05/11/2024', '12/11/2024', '19/11/2024', '26/11/2024', NULL),
(47, 4, 'quimioterapia', 'Senarega Monica ', 6413225, 6413225, '2024-11-04 15:28:43', '47_quimioterapia.pdf', 'rechazado', '11/05/2024', NULL, NULL, NULL, NULL),
(48, 4, 'quimioterapia', 'Seranega Monica  ', 6413225, 6413225, '2024-11-04 15:34:18', '48_quimioterapia.pdf', 'correcto', '05/11/2024', NULL, NULL, NULL, NULL),
(49, 6, 'quimioterapia', 'Luna Marcos', 11695175, 11695175, '2024-11-05 16:22:16', '49_quimioterapia.pdf', 'rechazado', '06/11/2024', NULL, NULL, NULL, NULL),
(50, 6, 'quimioterapia', 'Luna Marcos', 11695175, 11695175, '2024-11-05 16:22:43', '50_quimioterapia.pdf', 'correcto', '06/11/2024', NULL, NULL, NULL, NULL),
(51, 4, 'quimioterapia', 'Sosa Carlos', 12683104, 12683104, '2024-11-07 08:19:41', '51_quimioterapia.pdf', 'rechazado', NULL, NULL, NULL, NULL, NULL),
(52, 4, 'quimioterapia', 'Reyna David', 20038081, 20038081, '2024-11-07 08:28:54', '52_quimioterapia.pdf', 'correcto', '12/11/2024', '03/12/2024', '26/12/2024', NULL, NULL),
(53, 4, 'quimioterapia', 'Senarega Monica', 6413225, 6413225, '2024-11-07 08:36:13', '53_quimioterapia.pdf', 'correcto', '19/11/2024', '26/11/2024', '03/12/2024', '18/12/2024', NULL),
(54, 4, 'quimioterapia', 'Roberto Blas Sanchez', 11059626, 11059626, '2024-11-07 09:24:36', '54_quimioterapia.pdf', 'rechazado', '25/11/2024', NULL, NULL, NULL, NULL),
(55, 4, 'quimioterapia', 'Marcelo Montenegro', 12698982, 12698982, '2024-11-07 09:56:36', '55_quimioterapia.pdf', 'correcto', '12/11/2024', '03/12/2024', NULL, NULL, NULL),
(56, 4, 'quimioterapia', 'Rodriguez María Angelica ', 0, 13816977, '2024-11-07 12:58:17', '56_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(57, 4, 'quimioterapia', 'Sosa Carlos', 12683104, 12683104, '2024-11-07 13:07:58', '57_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(60, 4, 'quimioterapia', 'Santillan Robert', 8790930, 8790930, '2024-11-08 09:44:33', '60_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(61, 4, 'quimioterapia', 'Bret Roberto', 10816596, 10816596, '2024-11-08 11:28:12', '61_quimioterapia.pdf', 'correcto', '11/12/0024', NULL, NULL, NULL, NULL),
(62, 23, 'quimioterapia', 'Mancuello Olga', 0, 10516867, '2024-11-08 12:07:16', '62_quimioterapia.pdf', 'rechazado', '22/11/2024', '22/11/2024', '29/11/2024', '06/12/2024', NULL),
(63, 6, 'quimioterapia', 'Puertas Roberto Carlos', 8591777, 8591777, '2024-11-08 12:08:25', '63_quimioterapia.pdf', 'correcto', '08/11/2024', '15/11/2024', '22/11/2024', NULL, NULL),
(64, 23, 'quimioterapia', 'Vono Concepción', 0, 14108692, '2024-11-08 12:20:14', '64_quimioterapia.pdf', 'correcto', '20/11/2024', '20/11/2024', NULL, NULL, NULL),
(65, 6, 'quimioterapia', 'Presti Nazareno', 10895499, 10895499, '2024-11-08 12:28:25', '65_quimioterapia.pdf', 'correcto', '08/11/2024', '11/11/2024', '12/11/2024', NULL, NULL),
(66, 4, 'quimioterapia', 'Carrizo Graciela', 12805224, 12805224, '2024-11-08 14:39:39', '66_quimioterapia.pdf', 'correcto', '12/11/2024', '19/11/2024', '26/11/2024', '03/12/2024', NULL),
(67, 6, 'quimioterapia', 'Lacuadra Maria Eva ', 14106827, 14106827, '2024-11-08 15:28:58', '67_quimioterapia.pdf', 'rechazado', '11/11/2024', '18/11/2024', '25/11/2024', '02/12/2024', '09/12/2024'),
(68, 6, 'quimioterapia', 'Lacuadra Maria Eva ', 14106827, 14106827, '2024-11-08 15:33:29', '68_quimioterapia.pdf', 'correcto', '16/12/2024', '23/12/2024', NULL, NULL, NULL),
(69, 4, 'quimioterapia', 'Baez Juana', 12570099, 12570099, '2024-11-12 08:52:45', '69_quimioterapia.pdf', 'rechazado', '14/11/2024', NULL, NULL, NULL, NULL),
(70, 4, 'quimioterapia', 'Bret Roberto', 10816596, 10816596, '2024-11-12 09:30:08', '70_quimioterapia.pdf', 'rechazado', '19/11/2024', NULL, NULL, NULL, NULL),
(71, 4, 'quimioterapia', 'Juarez Enrique', 16104995, 16104995, '2024-11-12 10:17:26', '71_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(72, 4, 'quimioterapia', 'Reyna David', 20038081, 20038081, '2024-11-12 11:48:17', '72_quimioterapia.pdf', 'correcto', '03/12/2024', NULL, NULL, NULL, NULL),
(73, 4, 'quimioterapia', 'Baez Juana', 12570099, 12570099, '2024-11-12 13:48:45', '73_quimioterapia.pdf', 'correcto', '15/11/2024', NULL, NULL, NULL, NULL),
(74, 6, 'quimioterapia', 'presti nzareno', 10895499, 10895499, '2024-11-12 15:36:10', '74_quimioterapia.pdf', 'rechazado', '09/12/2024', NULL, NULL, NULL, NULL),
(75, 6, 'quimioterapia', 'sosa abel', 16400759, 16400759, '2024-11-12 16:25:06', '75_quimioterapia.pdf', 'rechazado', '15/11/2024', NULL, NULL, NULL, NULL),
(76, 6, 'quimioterapia', 'presti nzareno', 10895499, 10895499, '2024-11-12 17:07:22', '76_quimioterapia.pdf', 'correcto', '09/12/2024', '06/01/2025', NULL, NULL, NULL),
(77, 6, 'quimioterapia', 'sosa abel', 16400759, 16400759, '2024-11-12 17:12:51', '77_quimioterapia.pdf', 'correcto', '15/11/2024', '13/12/2024', NULL, NULL, NULL),
(78, 6, 'quimioterapia', 'fossati norma', 18365079, 18365079, '2024-11-12 17:32:47', '78_quimioterapia.pdf', 'correcto', '15/11/2024', '15/11/2024', '06/12/2024', '27/12/2024', NULL),
(79, 6, 'quimioterapia', 'feliciano iriene', 11658629, 11658629, '2024-11-12 18:37:16', '79_quimioterapia.pdf', 'correcto', '20/11/2024', '04/12/2024', '17/12/2024', '02/01/2025', NULL),
(80, 6, 'quimioterapia', 'torres patricia', 18091819, 18091819, '2024-11-12 19:13:01', '80_quimioterapia.pdf', 'correcto', '15/11/2024', '15/11/2024', NULL, NULL, NULL),
(81, 2, 'quimioterapia', 'BORDENAVE TAUZIA       ', 10671872, 10671872, '2024-11-13 09:43:06', '81_quimioterapia.pdf', 'rechazado', '25/11/2024', '26/11/2024', '27/11/2024', '28/11/2024', NULL),
(82, 21, 'quimioterapia', 'Julio Sanbria', 0, 93285331, '2024-11-13 13:35:05', '82_quimioterapia.pdf', 'rechazado', '12/11/2024', NULL, NULL, NULL, NULL),
(83, 21, 'quimioterapia', 'GALLARDO MARIA CRISTINA', 0, 13185528, '2024-11-13 13:39:55', '83_quimioterapia.pdf', 'rechazado', NULL, NULL, NULL, NULL, NULL),
(84, 21, 'quimioterapia', 'GALLARDO MARIA CRISTINA', 0, 13185528, '2024-11-13 13:45:36', '84_quimioterapia.pdf', 'rechazado', NULL, NULL, NULL, NULL, NULL),
(85, 21, 'quimioterapia', 'Julio Sanabria Avalos ', 93285331, 93285331, '2024-11-13 14:17:59', '85_quimioterapia.pdf', 'rechazado', '12/11/2024', NULL, NULL, NULL, NULL),
(86, 2, 'quimioterapia', 'Risoli Rosa', 93510062, 93510062, '2024-11-13 15:41:26', '86_quimioterapia.pdf', 'correcto', '15/11/2024', '13/12/2024', '10/01/2025', '07/02/2025', '07/03/2025'),
(87, 21, 'quimioterapia', 'Julio Sanabria Avalos', 0, 93285331, '2024-11-13 16:25:46', '87_quimioterapia.pdf', 'correcto', '11/12/2024', NULL, NULL, NULL, NULL),
(88, 21, 'quimioterapia', 'GALLARDO MARIA CRISTINA', 0, 13185528, '2024-11-13 16:35:26', '88_quimioterapia.pdf', 'correcto', '11/12/2024', NULL, NULL, NULL, NULL),
(91, 6, 'quimioterapia', 'Gomez Sergio', 26824404, 26824404, '2024-11-14 13:03:27', '91_quimioterapia.pdf', 'correcto', '15/11/2024', NULL, NULL, NULL, NULL),
(92, 6, 'quimioterapia', 'Barragan Gladys Noemi', 12171141, 12171141, '2024-11-14 13:28:42', '92_quimioterapia.pdf', 'correcto', '15/11/2024', NULL, NULL, NULL, NULL),
(93, 6, 'quimioterapia', 'Lacuadra Maria Eva', 14106827, 14106827, '2024-11-15 08:39:34', '93_quimioterapia.pdf', 'correcto', '19/11/2024', NULL, NULL, NULL, NULL),
(94, 4, 'quimioterapia', 'Perez Juana Carlota', 10671441, 10671441, '2024-11-15 10:48:53', '94_quimioterapia.pdf', 'rechazado', '20/11/2024', '11/12/2024', '03/01/2024', NULL, NULL),
(95, 4, 'quimioterapia', 'Amendola Marcelo Mario', 20024360, 20024360, '2024-11-19 11:02:43', '95_quimioterapia.pdf', 'rechazado', '21/11/2024', '13/12/2024', NULL, NULL, NULL),
(96, 4, 'quimioterapia', 'Sosa Carlos', 12683104, 12683104, '2024-11-19 11:28:28', '96_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(97, 4, 'quimioterapia', ' Amendola Marcelo Mario', 20024360, 20024360, '2024-11-19 13:51:50', '97_quimioterapia.pdf', 'correcto', '22/11/2024', '13/12/2024', NULL, NULL, NULL),
(98, 4, 'quimioterapia', 'Diaz Alicia Delia', 4730036, 4730036, '2024-11-19 08:47:06', '98_quimioterapia.pdf', 'correcto', '20/11/2024', NULL, NULL, NULL, NULL),
(99, 2, 'quimioterapia', ' BORDENAVE TAUZIA', 10671872, 10671872, '2024-11-19 09:15:41', '99_quimioterapia.pdf', 'correcto', '25/11/2024', '26/11/2024', '27/11/2024', '28/11/2024', '29/11/2024'),
(100, 2, 'quimioterapia', 'BORDENAVE TAUZIA', 10671872, 10671872, '2024-11-19 09:21:37', '100_quimioterapia.pdf', 'correcto', '02/12/2024', '03/12/2024', '04/12/2024', '05/12/2024', '06/12/2024'),
(101, 2, 'quimioterapia', ' BORDENAVE TAUZIA', 10671872, 10671872, '2024-11-19 09:33:13', '101_quimioterapia.pdf', 'correcto', '09/12/2024', '10/12/2024', '11/12/2024', '12/12/2024', '13/12/2024'),
(102, 2, 'quimioterapia', ' BORDENAVE TAUZIA', 10671872, 10671872, '2024-11-19 09:36:52', '102_quimioterapia.pdf', 'correcto', '16/12/2024', '17/12/2024', '18/12/2024', '19/12/2024', '20/12/2024'),
(103, 4, 'quimioterapia', 'Cabrera Hugo', 11019900, 11019900, '2024-11-20 10:18:30', '103_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(104, 4, 'quimioterapia', 'Herrera Rafael Maximiliano', 30054108, 30054108, '2024-11-20 10:21:50', '104_quimioterapia.pdf', 'correcto', '22/11/2024', NULL, NULL, NULL, NULL),
(106, 4, 'quimioterapia', 'Perez Juana Carlota ', 10671441, 10671441, '2024-11-20 16:33:56', '106_quimioterapia.pdf', 'rechazado', '22/11/2024', '13/12/2024', '03/01/2025', NULL, NULL),
(107, 6, 'quimioterapia', 'Porterie Eva ', 0, 6395721, '2024-11-20 16:52:02', '107_quimioterapia.pdf', 'rechazado', '25/11/0024', NULL, '16/12/0024', '06/01/0024', '27/01/0024'),
(108, 6, 'quimioterapia', 'gomez sergio', 26824404, 26824404, '2024-11-20 17:48:00', '108_quimioterapia.pdf', 'correcto', '06/12/2024', '10/01/2025', '31/01/2025', NULL, NULL),
(109, 6, 'quimioterapia', 'Barragan Gladys', 12171141, 12171141, '2024-11-20 18:05:12', '109_quimioterapia.pdf', 'correcto', '09/12/2024', '30/12/2024', NULL, NULL, NULL),
(110, 6, 'quimioterapia', 'puertas roberto', 0, 0, '2024-11-20 19:19:35', '110_quimioterapia.pdf', 'rechazado', NULL, NULL, NULL, NULL, NULL),
(111, 6, 'quimioterapia', 'puertas roberto', 8591777, 8591777, '2024-11-20 19:24:08', '111_quimioterapia.pdf', 'correcto', '06/12/2024', '13/12/2024', '20/12/2024', '13/01/2025', '20/01/2025'),
(112, 6, 'quimioterapia', 'maldonado etelvina', 12766797, 12766797, '2024-11-20 19:42:37', '112_quimioterapia.pdf', 'correcto', '09/12/2024', '16/12/2024', '23/12/2024', NULL, NULL),
(113, 6, 'quimioterapia', 'monte pedro', 10382647, 10382647, '2024-11-20 19:57:03', '113_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(114, 21, 'quimioterapia', 'GALLARDO MARIA CRISTINA ', 0, 13185528, '2024-11-21 08:41:04', '114_quimioterapia.pdf', 'correcto', '04/12/2024', NULL, NULL, NULL, NULL),
(116, 6, 'quimioterapia', 'Porterie Eva', 6395721, 6395721, '2024-11-21 16:46:14', '116_quimioterapia.pdf', 'rechazado', '25/11/2024', '16/12/2024', '06/01/2025', '27/01/2025', NULL),
(117, 4, 'quimioterapia', 'Arrayago Luis', 10078567, 10078567, '2024-11-22 09:09:22', '117_quimioterapia.pdf', 'correcto', '28/11/2024', '05/12/2024', '12/12/2024', NULL, NULL),
(118, 4, 'quimioterapia', 'Tancredi María Graciela', 16519350, 16519350, '2024-11-22 09:16:03', '118_quimioterapia.pdf', 'correcto', '22/11/2024', NULL, NULL, NULL, NULL),
(119, 4, 'quimioterapia', 'Rodriguez María Angelica ', 13816977, 13816977, '2024-11-22 10:27:17', '119_quimioterapia.pdf', 'rechazado', NULL, NULL, NULL, NULL, NULL),
(120, 4, 'quimioterapia', 'Lopez Eduardo', 11647593, 11647593, '2024-11-22 11:18:16', '120_quimioterapia.pdf', 'correcto', '28/11/2024', '19/12/2024', NULL, NULL, NULL),
(121, 4, 'quimioterapia', 'Cancinos María Josefa', 16123570, 16123570, '2024-11-22 12:40:48', '121_quimioterapia.pdf', 'correcto', '27/11/2024', '18/12/2024', NULL, NULL, NULL),
(122, 4, 'quimioterapia', 'Villan Jose', 17480923, 17480923, '2024-11-22 13:49:00', '122_quimioterapia.pdf', 'rechazado', NULL, NULL, NULL, NULL, NULL),
(123, 6, 'quimioterapia', 'Porterie Eva', 6395721, 6395721, '2024-11-22 13:51:44', '123_quimioterapia.pdf', 'correcto', '27/11/2024', '18/12/2024', '08/01/2025', '29/01/2025', NULL),
(124, 4, 'quimioterapia', 'Villan Jose', 17480923, 17480923, '2024-11-22 14:01:28', '124_quimioterapia.pdf', 'correcto', '29/11/2024', NULL, NULL, NULL, NULL),
(125, 4, 'quimioterapia', 'Rodriguez María Angelica', 13816977, 13816977, '2024-11-22 14:04:32', '125_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(126, 6, 'quimioterapia', ' Lacuadra Maria Eva', 14106827, 14106827, '2024-11-22 14:44:44', '126_quimioterapia.pdf', 'correcto', '11/11/2024', '18/11/2024', '26/11/2024', '03/12/2024', '10/12/2024'),
(127, 4, 'quimioterapia', 'Perez Juana Carlota', 10671441, 10671441, '2024-11-22 16:31:39', '127_quimioterapia.pdf', 'correcto', '25/11/2024', '16/12/2024', '06/01/2025', NULL, NULL),
(128, 5, '', 'CARDOZO FACUNDO ', 0, 0, '2024-11-25 10:46:52', '128_oncohematologia.pdf', 'correcto', '29/11/2024', '13/12/2024', NULL, NULL, NULL),
(129, 5, '', 'CHAPERON JUAN MANUEL ', 0, 29316486, '2024-11-25 10:50:22', '129_oncohematologia.pdf', 'correcto', '28/11/2024', '05/12/2024', '12/12/2024', '19/12/2024', NULL),
(130, 5, '', 'CABRERA CARLOS', 0, 12917550, '2024-11-25 10:55:52', '130_oncohematologia.pdf', 'correcto', '05/12/2024', '06/12/2024', NULL, NULL, NULL),
(131, 4, 'quimioterapia', 'Mercado Mirta', 6059358, 6059358, '2024-11-26 08:31:15', '131_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(132, 5, 'quimioterapia', 'chaperon juan manuel', 0, 29316480, '2024-11-26 10:52:41', '132_quimioterapia.pdf', 'en revision', '28/11/2024', '05/12/2024', '12/12/2024', '19/12/2024', NULL),
(133, 5, 'quimioterapia', '', 0, 0, '2024-11-26 10:53:03', '133_quimioterapia.pdf', 'rechazado', NULL, NULL, NULL, NULL, NULL),
(134, 5, '', 'BARA  ALICIA', 0, 5483250, '2024-11-26 11:10:10', '134_oncohematologia.pdf', 'correcto', '03/12/2024', NULL, NULL, NULL, NULL),
(135, 5, '', 'cabrera carlos', 0, 12917550, '2024-11-26 11:22:27', '135_oncohematologia.pdf', 'correcto', '05/12/2024', '06/12/2024', NULL, NULL, NULL),
(136, 5, '', 'OHANNESSIAN ANA MARIA ', 0, 12634810, '2024-11-26 11:41:30', '136_oncohematologia.pdf', 'correcto', '03/01/2025', '17/01/2025', NULL, NULL, NULL),
(137, 4, 'quimioterapia', 'Roberto Sanchez Blas', 11059626, 11059626, '2024-11-26 11:58:54', '137_quimioterapia.pdf', 'correcto', '29/11/2024', NULL, NULL, NULL, NULL),
(138, 5, '', 'CANCINOS MARIO RENE', 0, 8109644, '2024-11-26 12:19:52', '138_oncohematologia.pdf', 'en revision', NULL, NULL, NULL, NULL, NULL),
(139, 4, 'quimioterapia', 'Gomez Mercedes Alicia', 6524462, 6524462, '2024-11-26 13:46:39', '139_quimioterapia.pdf', 'correcto', '29/11/2024', NULL, NULL, NULL, NULL),
(140, 6, 'quimioterapia', 'fossati norma', 18365079, 18365079, '2024-11-26 14:32:00', '140_quimioterapia.pdf', 'correcto', '29/11/2024', '20/12/2024', '10/01/2025', NULL, NULL),
(141, 4, 'quimioterapia', 'Diaz Alicia Delia', 4730036, 4730036, '2024-11-26 14:42:02', '141_quimioterapia.pdf', 'correcto', '29/11/2024', NULL, NULL, NULL, NULL),
(142, 5, '', 'BARA ALICIA', 0, 5483250, '2024-11-27 14:44:04', '142_oncohematologia.pdf', 'correcto', '03/12/2024', NULL, NULL, NULL, NULL),
(143, 5, '', 'ROMEO GONZALEZ ISABEL', 0, 92052777, '2024-11-27 14:46:32', '143_oncohematologia.pdf', 'correcto', '03/12/2024', NULL, NULL, NULL, NULL),
(144, 5, '', 'CANCINOS MARIO RENE ', 0, 8109644, '2024-11-27 14:55:21', '144_oncohematologia.pdf', 'rechazado', NULL, NULL, NULL, NULL, NULL),
(145, 5, '', 'CANCINOS MARIO', 0, 8109644, '2024-11-27 14:56:17', '145_oncohematologia.pdf', 'correcto', '05/12/2024', '06/12/2024', NULL, NULL, NULL),
(146, 4, 'quimioterapia', 'Lencina Dora', 11167031, 11167031, '2024-11-29 10:19:05', '146_quimioterapia.pdf', 'en revision', '02/12/2024', NULL, NULL, NULL, NULL),
(147, 4, 'quimioterapia', 'Montiel Esteban', 93325597, 93325597, '2024-11-29 10:50:11', '147_quimioterapia.pdf', 'correcto', '03/12/2024', NULL, NULL, NULL, NULL),
(148, 4, 'quimioterapia', 'Baez Juana', 12570099, 12570099, '2024-11-29 11:30:57', '148_quimioterapia.pdf', 'correcto', '03/12/2024', NULL, NULL, NULL, NULL),
(149, 23, '', 'Mancuello Olga', 0, 10516867, '2024-11-29 11:35:07', '149_oncohematologia.pdf', 'correcto', '03/12/2024', '10/12/2024', '17/12/2024', NULL, NULL),
(150, 4, 'quimioterapia', 'Cabrera Hugo', 11019900, 11019900, '2024-11-29 11:56:25', '150_quimioterapia.pdf', 'correcto', NULL, NULL, NULL, NULL, NULL),
(151, 4, 'quimioterapia', 'Diaz Mirta', 5735721, 5735721, '2024-11-29 14:38:15', '151_quimioterapia.pdf', 'en revision', '06/12/2024', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `sent_time` datetime NOT NULL,
  `seen_by_receiver` tinyint(1) NOT NULL DEFAULT 0,
  `seen_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `messages`
--

INSERT INTO `messages` (`message_id`, `conversation_id`, `sender_id`, `content`, `sent_time`, `seen_by_receiver`, `seen_time`) VALUES
(2, 1, 9, 'Hola', '2024-10-23 13:11:35', 1, '2024-10-23 13:11:54'),
(3, 1, 2, 'Hola00000000', '2024-10-23 13:12:02', 1, '2024-10-23 13:12:03'),
(4, 1, 9, 'todo bien ?', '2024-10-23 13:12:17', 1, '2024-10-23 13:12:18'),
(5, 1, 2, 'si bien', '2024-10-23 13:12:24', 1, '2024-10-23 13:12:24'),
(6, 2, 4, 'Hola', '2024-10-23 16:04:14', 1, '2024-10-24 11:41:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `patient_medications`
--

CREATE TABLE `patient_medications` (
  `id_medication` int(11) NOT NULL,
  `id_form` int(11) DEFAULT NULL,
  `medication_name` varchar(255) NOT NULL,
  `brought_medicine` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `patient_medications`
--

INSERT INTO `patient_medications` (`id_medication`, `id_form`, `medication_name`, `brought_medicine`) VALUES
(1, 20, 'ATROPINA UNA AMPOLLA SC', 0),
(2, 20, 'Irinotecán 340 mg en 500 ml de Dextrosa al 5% a pasar en 90 minutos', 0),
(3, 20, 'Bolo de 5 Fluorouracilo 760 mg', 0),
(4, 20, 'Leucovorina 760 mg en 250 ml de SF en 90 minutos', 0),
(5, 20, '5 Fluorouracilo 4560 mg en 1000 ml de SF a pasar en 48 horas', 0),
(6, 21, 'medicacion 1', 1),
(7, 21, 'MEDICACION 2', 1),
(8, 22, 'medicacion 3', 0),
(9, 22, 'Medicacion 4', 0),
(10, 23, 'Oxaliplatino 230 mg en 500 ml de SF a pasar en 2 horas ', 0),
(11, 24, 'Carboplatino 550 mg en 500 ml de Dextrosa al 5% en 60 minutos', 0),
(12, 24, 'Pemetrexed 900 mg en 100 ml de SF en 10 minutos ', 0),
(13, 25, 'paclitaxel 140 mg en 250 cc sf en 1 hora D1-8-15', 0),
(14, 26, 'ipilimumab 60 mg en 300 SF en 1 hora', 0),
(15, 26, 'nivolumab 180 mg en 300 SF en 1 hora', 0),
(16, 27, 'Pembrolizumab 200 mg en 50 ml de SF a pasar en 30 minutos ', 0),
(17, 44, 'FULVESTRANT 1 AMP EN CDA GLUTEO DE 250 MG CADA 28 DIAS', 0),
(18, 45, '-Paclitaxel 130 mg en 500 ml de SF a pasar en una hora ', 0),
(19, 46, 'Gemcitabina 1600 mg en 250 ml de SF a pasar en 30 minutos', 0),
(20, 47, 'Gemcitabina 1500 mg en 250 ml de SF a pasar en 30 min', 0),
(21, 48, 'Gemcitabina 1500 mg  en 250 ml de SF a pasar en 30 min', 0),
(22, 49, 'Paclitaxel 280 mg en 500 ml SF', 0),
(23, 50, 'Paclitaxel 280 mg en 500 ml SF', 0),
(25, 51, 'Docetaxel 90 mg en 250 ml SF (una hora)', 0),
(26, 51, '5-Fluorouracilo 4650 mg en 24 horas', 0),
(27, 51, 'Leucovorina 360 mg en 250 ml DXT5% (dos horas)Oxaliplatino 150 mg en 500 ml DXT5% (2horas)', 0),
(28, 52, 'Oxaliplatino 220 mg 500 ml Dxt 5% 2 horas', 0),
(29, 53, 'METOCLOPRAMIDA 10 MG ENDOVENOSO', 0),
(30, 53, 'Gemcitabina 1500 mg en 250 ml de SF a pasar en 30 minutos ', 0),
(31, 54, 'Pembrolizumab 200 mg 50 ml de SF en 30 minutos', 0),
(32, 55, 'Oxaliplatino 240 mg en 250 ml de Dextrosa al 5% a pasar en 2 horas ', 0),
(33, 56, '5 Fluorouracilo 3800 mg en 1000 ml de SF a pasar en  48 horas ', 0),
(34, 56, '-Bolo 5 Fluorouracilo  640 mg ', 0),
(35, 56, 'Leucovorina 640 mg en 250 ml de Dextrosa al 5% a pasar en 90 minutos(Administrar luego del bolo)', 0),
(36, 56, 'Irinotecán 280 mg en 500 ml de Dextrosa al 5%  a pasar en 90 minutos', 0),
(37, 57, 'Docetaxel 90 mg en 250 ml SF (una hora)', 0),
(38, 57, '5-Fluorouracilo 4650 mg en 24 horas', 0),
(39, 57, 'Leucovorina 360 mg en 250 ml DXT5% (dos horas)', 0),
(40, 57, 'Oxaliplatino 150 mg en 500 ml DXT5% (2horas)', 0),
(49, 60, 'METOCLOPRAMIDA 10 MG', 0),
(50, 60, 'Oxaliplatino 130 mg en 500 ml  de dextrosa 5%  pasar en 2 horas', 0),
(51, 60, 'Leucovorina 320 mg  250 ml de Dextrosa al 5% a pasar en  30 minutos', 0),
(52, 60, '5 Fluorouracilo infusión continua en 46 horas dosis total 4160 mg en 1000 ml de SF', 0),
(53, 60, 'Bolo 600 mg 5 Fluorouracilo', 0),
(54, 61, 'Carboplatino 250 mg en 250 ml de DXT 5% en 30 minutos', 0),
(55, 61, 'Paclitaxel 75 mg en 250 ml de SF en una hora ', 0),
(56, 62, 'Botezomib 2mg SC', 0),
(57, 62, 'Lernalidomida 25mg VO', 0),
(58, 62, 'Dexametasona 40mg VO', 0),
(59, 62, 'Pamidronato 90 mg en 200 mL', 0),
(60, 63, 'Ondansetrón 8 mg endovenoso', 0),
(61, 63, 'Ranitidina 50 mg endovenoso', 0),
(62, 63, 'dexametasona 8 mg ', 0),
(63, 63, ' Gemcitabina 1900 mg 500 ml SF una hora', 0),
(64, 64, 'Rituximab 600 mg', 0),
(65, 64, 'Vincristina 2 mg', 0),
(66, 64, 'Ciclofosfamida 1200 mg', 0),
(67, 64, 'Meprednisona 100 mg VO', 0),
(68, 65, 'Cisplatino 45 mg  en 500 SF', 0),
(69, 65, ' Etoposido 170 mg en 500 SF ', 0),
(70, 66, '70 mg de Cisplatino en 500 ml de SF (con 30 mg de Manitol)', 0),
(71, 66, 'Prehidratación con 1000 ml de SF (20 meq de CLK + 2 gramos de Sulfato de Magnesio)', 0),
(72, 67, 'paclitaxel 130 mg en 300 cc sf en 1 hora D1-8-15', 0),
(73, 68, 'paclitaxel 130 mg en 300 cc sf en 1 hora D1-8-15', 0),
(74, 69, 'Doxorrubicina 108 mg en bolo endovenoso', 0),
(75, 69, 'Ciclofosfamida 1000 mg en 250 ml de Dextrosa al 5% a pasar en una hora ', 0),
(76, 70, 'Carboplatino 250 mg en 250 ml de DXT 5% en 30 minutos', 0),
(77, 70, 'Paclitaxel 75 mg en 250 ml de SF en una hora ', 0),
(78, 71, 'Oxaliplatino 135 mg en 500 ml  de dextrosa 5%  pasar en 2 horas', 0),
(79, 71, 'Leucovorina 640 mg  250 ml de Dextrosa al 5% a pasar en  30 minutos', 0),
(80, 71, '5 Fluorouracilo en bolo  640 mg ', 0),
(81, 71, '5 Fluorouracilo infusión continua en 46 horas dosis total 3840 mg en 1000 ml de SF', 0),
(82, 72, 'Oxaliplatino 220 mg en 500 ml de Dextrosa al 5% en 2 horas ', 0),
(83, 73, 'Doxorrubicina 108 mg en bolo endovenoso', 0),
(84, 73, 'Ciclofosfamida 1000 mg en 250 ml de Dextrosa al 5% a pasar en una hora', 0),
(85, 74, 'cisplatino 40 mg d1-3 1 hora', 0),
(86, 74, 'etoposido 170 mg d1-3 en 1 h', 0),
(87, 75, 'zoledronico 3.2 mg en 200 sf en 30 min', 0),
(88, 76, 'cisplatino 40 mg d1-3 1 hora', 0),
(89, 76, 'etoposido 170 mg d1-3 en 1 h', 0),
(90, 77, 'zoledronico 3.2 mg en 200 sf en 30 min', 0),
(91, 78, 'oxaliplatino 200 mg en 400 dex 5% en 90 min ', 0),
(92, 78, 'bevacizumab 390 mg en 300 sf en 30 min', 1),
(93, 79, 'doxorrubicina liposomal 35 mg en 60 min', 0),
(94, 80, 'trastuzumab 600 mg sc', 1),
(95, 86, 'Acido Zoledronico SF 100cc a pasar en 15 min ', 0),
(97, 91, 'bevacizumab 1200 mg en 300 sf en 60 min', 0),
(98, 92, 'disponer  de 500 mg de hidrocortisona', 0),
(99, 92, 'Carboplatino 600 mg en 60 min EV', 0),
(100, 92, '- Paclitaxel 300 mg en 500 mL en 3 hs', 0),
(101, 93, 'paclitaxel 130 mg en 300 cc sf en 1 hora D1-8-15', 0),
(102, 94, 'Doxorrubicina 108 mg en bolo', 0),
(103, 94, 'Ciclofosfamida 1000 mg en 250 ml de SF en una hora ', 0),
(104, 95, 'Carboplatino 550 mg en 500 ml DXT 5% a pasar en una hora', 0),
(105, 95, 'Pemetrexed 900 mg en  100 ml de SF a pasar en 10 minutos ', 0),
(106, 96, ' Docetaxel 90 mg en 250 ml SF (una hora)', 0),
(107, 96, ' 5-Fluorouracilo 4650 mg en 1000 ml SF en 24 horas', 0),
(108, 96, ' Leucovorina 360 mg en 250 ml DXT5% (dos horas)', 0),
(109, 96, ' Oxaliplatino 150 mg en 500 ml DXT5% (2horas)', 0),
(110, 97, 'Carboplatino 550 mg en 500 ml DXT 5% a pasar en una hora', 0),
(111, 97, 'Pemetrexed 900 mg en  100 ml de SF a pasar en 10 minutos', 0),
(112, 98, ' Fulvestrant 500 mg intramuscular ', 0),
(113, 98, 'Ácido Zoledrónico 4 mg ', 0),
(114, 99, 'Rescenza una ampolla en 100 cc con sol fisiologica. ', 0),
(115, 100, 'Rescenza una ampolla en 100 cc con sol fisiologica. al 5% ', 0),
(116, 101, ' Rescenza una ampolla en 100 cc con sol fisiologica. al 5%', 0),
(117, 102, 'Rescenza una ampolla en 100 cc con sol fisiologica. al 5% ', 0),
(118, 103, '5 Fluorouracilo 4560 en 1000 ml de Dextrosa al 5%  46 hs', 0),
(119, 103, 'Bolo 5 Fluorouracilo  760 mg', 0),
(120, 103, 'Leucovorina 760 mg en 250 ml de Dextrosa al 5% a pasar en 90 minutos', 0),
(121, 103, 'Irinotecan 340 mg en 500 ml de Dextrosa al 5% a pasar en 90 minutos ', 0),
(122, 103, 'Una ampolla Atropina SC previo Irinotecan', 0),
(123, 104, 'Oxaliplatino 220 mg en 500 ml de Dextrosa al 5% a pasar en 2 horas ', 0),
(124, 106, 'Doxorrubicina 108 mg en bolo', 0),
(125, 106, 'Ciclofosfamida 1000 mg en 250 ml de SF en una hora', 0),
(126, 107, 'docetaxel 140 mg en 400 Dex 5% en 1 hora', 0),
(127, 107, 'ciclofosfamida 1 g FA en 500 SF en 1 hora', 0),
(128, 108, 'bevacizumab 1200 mg en 300 sf', 0),
(129, 109, 'crboplatino 550 mg en 400 sf en 1 hora', 0),
(130, 109, 'paclitaxel 300 mg en 400 sf en 3 hs ', 0),
(131, 111, 'carboplatino 500 mg en 1 hora', 0),
(132, 111, 'gemcitabine 1800 mg en 1 hora', 0),
(133, 112, 'carboplatino 400 mg en 1 hora', 0),
(134, 112, 'paclitaxel 110 mg en 300 sf en 1hs ', 0),
(135, 113, '5 fu  1500 mg en 500 mL en 24 hs d1-4', 0),
(136, 113, 'cisplatino 50 mg en 400 sf d1-2', 0),
(139, 116, 'docetaxel 140 mg en 400 dex 5% en 1 hora', 0),
(140, 116, 'ciclofosfasmida en 1 gramo en 500 sf en 1 hora', 0),
(141, 117, 'Paclitaxel 150 mg en 500 ML de SF a pasar en una hora ', 0),
(142, 118, 'Ácido Zoledronico 4 mg a pasar en 30 minutos ', 0),
(143, 119, 'Irinotecán 280 mg 500 ml Dextrosa5%  90 minutos', 0),
(144, 119, 'Leucovorina 640 mg 250 ml Dextrosa 5% 90 minutos', 0),
(145, 119, ' Fluorouracilo bolo 640 mg ', 0),
(146, 119, 'Flourouracilo 3800 mg 48 horas en 1000 ml de SF', 0),
(147, 120, 'Oxaliplatino  260 mg en 500 ml de Dextrosa al 5% en 2 horas ', 0),
(148, 121, 'Pemetrexed 750 mg en 100 ml de SF a pasar en una hora', 0),
(149, 121, 'Vitamina B12 1000 mcg IM (Solo el día 27/11/24)', 0),
(150, 122, 'Cisplatino 150 mg en 1000 ml de SF con 30 mg de Manigol y 10 meq de Cloruro de Potasio a pasar en 2 horas', 0),
(151, 122, ' Prehidratar y post-hidratar con al menos 1500 ml de SF ', 0),
(152, 123, 'docetaxel 140 mg en 400 dex 5% en 1 hora', 0),
(153, 123, 'ciclofosfasmida en 1 gramo en 500 sf en 1 hora', 0),
(154, 124, 'Cisplatino 150 mg en 1000 ml de SF con 30 mg de Manigol y 10 meq de Cloruro de Potasio a pasar en 2 horas', 0),
(155, 124, 'Prehidratar y post-hidratar con al menos 1500 ml de SF', 0),
(156, 125, 'ATROPINA 1 AMPOLLA SC', 0),
(157, 125, 'Irinotecán 280 mg 500 ml Dextrosa5%  90 minutos', 0),
(158, 125, 'Leucovorina 640 mg 250 ml Dextrosa 5% 90 minutos', 0),
(159, 125, '5 Fluorouracilo bolo 640 mg ', 0),
(160, 125, 'Flourouracilo 3800 mg 48 horas en 1000 ml de SF', 0),
(161, 126, 'paclitaxel 130 mg en 300 cc sf en 1 hora D1-8-15', 0),
(162, 127, 'Doxorrubicina 108 mg en bolo', 0),
(163, 127, 'Ciclofosfamida 1000 mg en 250 ml de SF en una hora', 0),
(164, 131, 'Oxaliplatino 120 mg en 500 ml de Dextrosa al 5% en 2 horas ', 0),
(165, 131, 'Leucovorina 560 mg en 250 ml de Solución fisiológica en 2 horas', 0),
(166, 131, '5 Fluorouracilo 560 mg en bolo ', 0),
(167, 131, '5 FLuorouracilo 3360 mg en 46 horas diluido en 1000 ml de Dextrosa a pasar en 46 horas ', 0),
(168, 137, 'Pembrolizumab 200 mg en 50 ml de SF a pasra en 30 minutos ', 0),
(169, 139, 'Paclitaxel 280 mg en 500 ml de SF a pasar en 3 horas ', 0),
(170, 139, 'Carboplatino 425 mg en 250 ml de Dextrosa al 5% a pasar en 30 minutos ', 0),
(171, 140, 'oxaliplatino 200 mg en 400 dex 5% en 90 min', 0),
(172, 140, 'bevacizumab 390 mg en 300 sf en 30 min', 0),
(173, 141, 'Ácido Zoledrónico 4 mg', 0),
(174, 146, 'Oxaliplatino 220 mg en 500 ml Dextrosa 5% a pasar en 2 horas ', 0),
(175, 147, 'Metoclopramida 10 mg endovenoso', 0),
(176, 147, 'Nivolumab 210 mg en 100 ml de SF a pasar en 30 minutos', 0),
(177, 147, 'Ipilimumab 70 mg en 50 ml de SF a pasar en 30 minutos', 0),
(178, 147, 'Denosumab 120 mg subcutaneo  ', 0),
(179, 148, 'Doxorrubicina 108 mg en bolo endovenoso', 0),
(180, 148, 'Ciclofosfamida 1000 mg en 250 ml de Dextrosa al 5% a pasar en una hora ', 0),
(181, 150, 'ATROPINA UNA AMPOLLA SC', 0),
(182, 150, 'Irinotecan 340 mg en 500 ml de Dextrosa al 5% a pasar en 90 minutos ', 0),
(183, 150, 'Leucovorina 760 mg en 250 ml de Dextrosa al 5% a pasar en 90 minutos', 0),
(184, 150, 'Bolo 5 Fluorouracilo  760 mg', 0),
(185, 150, '5 Fluorouracilo 4560 en 1000 ml de Dextrosa al 5%  46 hs', 0),
(186, 151, ' Pembrolizumab 200 mg en 50 ml de SF a pasar en 30 minutos ', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dni` int(11) NOT NULL,
  `role` enum('user','medico','admin','supervisor','moderador') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `name`, `dni`, `role`) VALUES
(2, 'lferreyra', '$2y$10$oyKYILJj4kOFaZVEmdgyu.o9PpWyFwWjRV7Als2zsXqLCewZmiBpq', 'Laura M. Ferreyra', 0, 'medico'),
(4, 'adiomedi', '$2y$10$tgRP5qU73koNx.rdD8pqM.yO58Vu2GJoT1xbXDFZShdEyIklY7qPm', 'Agustin E. Diomedi', 0, 'medico'),
(5, 'iarmocida', '$2y$10$T0PN5aUaWtJtsWl1EEmzdOXRiJEXePHLMQsoApUZM1gACMvP.R4Im', 'Ivanna Armocida', 0, 'medico'),
(6, 'mcarullo', '$2y$10$NMRzhXsqBa2kkZpqqU1Ty..Me7tJdp8aDzXm2G/aeXPVsVtpioKGK', 'Mariana Carullo', 0, 'medico'),
(7, 'mdecarli', '$2y$10$y90lIYRlbgYpOjdwxtdxdu08Fl4UjVYRcwB0G2WytW.9A8B7hDFF2', 'Maria Eugenia De Carli', 0, 'medico'),
(8, 'vgerlero', '$2y$10$WLH4.sA6xW2U3xqVyD2GNe2aWYxcW5v5gblK58UyVoKBHE1oDwSnG', 'Valeria Gerlero', 0, 'medico'),
(9, 'vvillafañe', '$2a$12$J4ORBuPuMGhPCLjDNxatkuaJ898mlNbflTFHxi.uyXg6lCEIGwgGW', 'Vanina Villafañe', 0, 'admin'),
(10, 'root', '$2a$12$Q9BchEpe/S/BG2.aQ0MKFuq5W2XxBJ7d0AS.DJlCQD0WN1kz6iwii', 'root', 0, 'admin'),
(11, 'ylarrarte', '$2a$12$QHZwf9SNM4aQs9adqU9eN..gO0D.6hwe4QUvFJ87iF6ImLo3DYmnm', 'Yanina Larrarte', 0, 'supervisor'),
(12, 'agonzales', '$2a$12$VTBgW49YJI6WUJZXPNBB8uSBLSciJw.EHGC1dAXaAsi2qDpV0.uOe', 'Gonzalez Ayelen', 0, 'moderador'),
(13, 'mgomez', '$2a$12$L2BSyzW2qMHc.4MKYZsCZOp9eyl3PeQfRUB3f0JeaQYLlYMcnNiFi', 'Gomez Maria Luz', 0, 'moderador'),
(14, 'yjuarez', '$2a$12$rkfhk3.IAYEdJRn0fegxbOwfSdCXpHLNau9G18YFHY41vksz099yC', 'Juarez Yamila', 0, 'moderador'),
(15, 'gesposito', '$2a$12$zx.kpOB4kqiUO25r3o//geiYrO.d9yOIB.qHAr8rM.CHLXj8YrFVG', 'Esposito Genaro', 0, 'moderador'),
(16, 'cguevara', '$2a$12$Q5INuRlv.uRLnVednZhJlO.NF8iU3qbAHOONO.DwUpS.LxnWaO/72', 'Guevara Cintia', 0, 'moderador'),
(17, 'dgarcia', '$2a$12$fBPOIurc8z9TMHe6Z5wXDeQc46XIEGikVzZXX.XDQyl.0W/CnOjqG', 'Garcia Daniela', 0, 'moderador'),
(18, 'solcese', '$2a$12$kfov3fqKk.sE1cEEYDYT2eY9ek45zZDfEWyj0JFnlyHFK.imwQw6u', 'Olcese Silvina', 0, 'moderador'),
(19, 'eameri', '$2a$12$zu7HIULVcqJg0HFBLDljkuyj8CUIvyXw.BgDTAcLPMkcyMgrGIRsO', 'Emilia Ameri', 0, 'moderador'),
(20, 'ralberti', '$2a$12$ZTMl9YQq4O.6LOVhWIwCDuw2eywneYTepjyqw1DEYLhj/l/aiyeTG', 'Roxana Alberti', 0, 'moderador'),
(21, 'mdepoian', '$2a$12$.ATP2xNcdEMv59Enm.578uX85igLKgGj1V.abUoOaLQNg69jW2J4y', 'Micaela Depoian', 0, 'medico'),
(22, 'vvallejo', '$2a$12$XPomSlBczv0gNZR.NxO3Nu/Q4YYyeivNex4EHtewJ3W2shYF7qgfm', 'Veronica Vallejo', 0, 'medico'),
(23, 'dsalinas', '$2a$12$s6VeDkVkmBQoDm.rl2J0EePtCQNgeji6dKLHJ1d7ElQkQuJ10HLS6', 'Daniela Salinas', 0, 'medico');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD KEY `idx_user1_id` (`user1_id`),
  ADD KEY `idx_user2_id` (`user2_id`);

--
-- Indices de la tabla `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`form_id`),
  ADD KEY `creator_id` (`creator_id`);

--
-- Indices de la tabla `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `idx_conversation_id` (`conversation_id`);

--
-- Indices de la tabla `patient_medications`
--
ALTER TABLE `patient_medications`
  ADD PRIMARY KEY (`id_medication`),
  ADD KEY `fk_patient_medications_forms` (`id_form`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `forms`
--
ALTER TABLE `forms`
  MODIFY `form_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT de la tabla `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `patient_medications`
--
ALTER TABLE `patient_medications`
  MODIFY `id_medication` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`user_id`);

--
-- Filtros para la tabla `forms`
--
ALTER TABLE `forms`
  ADD CONSTRAINT `forms_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`);

--
-- Filtros para la tabla `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`);

--
-- Filtros para la tabla `patient_medications`
--
ALTER TABLE `patient_medications`
  ADD CONSTRAINT `fk_patient_medications_forms` FOREIGN KEY (`id_form`) REFERENCES `forms` (`form_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_medications_ibfk_1` FOREIGN KEY (`id_form`) REFERENCES `forms` (`form_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
