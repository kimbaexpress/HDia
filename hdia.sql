-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 10-12-2024 a las 18:01:22
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

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
(1, 'root', '$2y$10$xRWQLOJrtVdr1hq8gd47y.tyIpMHgBWuHJPVWRWY5ho3NWbsoF1hW', 'root', 0, 'admin'),
(2, 'moderador', '$2y$10$4t1EQtMeEbN.G8EWEDkkVO/svSb9EbP8t3HLez7aHlN6IWPhOdOLm', 'moderador', 0, 'moderador'),
(3, 'supervisor', '$2y$10$/1o026HqKFCTWDasnhZhv.BDq9vVC2d4X/FN3fV0Mq2kgybQbkygm', 'supervisor', 0, 'supervisor'),
(4, 'medico', '$2y$10$Sm6plvoaQxRwGmYRCK4Sx.Ta6W1WIXM80tknmdsFtJefDwuqxQ7f2', 'medico', 0, 'medico');

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
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `forms`
--
ALTER TABLE `forms`
  MODIFY `form_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `patient_medications`
--
ALTER TABLE `patient_medications`
  MODIFY `id_medication` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
