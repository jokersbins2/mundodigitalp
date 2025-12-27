-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-12-2025 a las 03:38:37
-- Versión del servidor: 8.0.41
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mundo_digital_premium`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_actividad`
--

CREATE TABLE `logs_actividad` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `accion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detalles` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `logs_actividad`
--

INSERT INTO `logs_actividad` (`id`, `usuario_id`, `accion`, `detalles`, `ip_address`, `created_at`) VALUES
(1, 1, 'login', 'Inicio de sesión', '::1', '2025-12-26 23:44:36'),
(2, 1, 'actualizar_password', 'Usuario ID: 2', '::1', '2025-12-26 23:47:02'),
(3, 1, 'logout', 'Cierre de sesión', '::1', '2025-12-26 23:47:12'),
(4, 2, 'login', 'Inicio de sesión', '::1', '2025-12-26 23:47:18'),
(5, 2, 'logout', 'Cierre de sesión', '::1', '2025-12-26 23:47:54'),
(6, 1, 'login', 'Inicio de sesión', '::1', '2025-12-26 23:49:26'),
(7, 1, 'logout', 'Cierre de sesión', '::1', '2025-12-26 23:51:34'),
(8, 1, 'login', 'Inicio de sesión', '::1', '2025-12-26 23:51:46'),
(9, 1, 'logout', 'Cierre de sesión', '::1', '2025-12-26 23:51:57'),
(10, 2, 'login', 'Inicio de sesión', '::1', '2025-12-26 23:52:03'),
(11, 2, 'logout', 'Cierre de sesión', '::1', '2025-12-26 23:53:24'),
(12, 2, 'login', 'Inicio de sesión', '::1', '2025-12-26 23:53:32'),
(13, 2, 'logout', 'Cierre de sesión', '::1', '2025-12-26 23:53:40'),
(14, 1, 'login', 'Inicio de sesión', '::1', '2025-12-26 23:53:50'),
(15, 1, 'consultar_correos', 'Email: dis2525@crcambios.com', '::1', '2025-12-26 23:56:24'),
(16, 1, 'ver_correo', 'UID: 104966', '::1', '2025-12-26 23:56:29'),
(17, 1, 'consultar_correos', 'Email: rosagenova@xtreamvz.xyz', '::1', '2025-12-26 23:57:22'),
(18, 1, 'ver_correo', 'UID: 104964', '::1', '2025-12-26 23:57:31'),
(19, 1, 'ver_correo', 'UID: 104963', '::1', '2025-12-26 23:57:45'),
(20, 1, 'consultar_correos', 'Email: rosagenova@xtreamvz.xyz', '::1', '2025-12-26 23:58:03'),
(21, 1, 'ver_correo', 'UID: 104963', '::1', '2025-12-27 00:04:26'),
(22, 1, 'consultar_correos', 'Email: lucazioni@xtreamvz.xyz', '::1', '2025-12-27 00:16:56'),
(23, 1, 'ver_correo', 'UID: 104969', '::1', '2025-12-27 00:17:00'),
(24, 1, 'consultar_correos', 'Email: lucazioni@xtreamvz.xyz', '::1', '2025-12-27 00:24:05'),
(25, 1, 'ver_correo', 'UID: 104968', '::1', '2025-12-27 00:24:11'),
(26, 1, 'ver_correo', 'UID: 104968', '::1', '2025-12-27 00:25:09'),
(27, 1, 'ver_correo', 'UID: 104968', '::1', '2025-12-27 00:25:43'),
(28, 1, 'ver_correo', 'UID: 104968', '::1', '2025-12-27 00:26:48'),
(29, 1, 'ver_correo', 'UID: 104969', '::1', '2025-12-27 00:27:06'),
(30, 1, 'ver_correo', 'UID: 104969', '::1', '2025-12-27 00:27:24'),
(31, 1, 'ver_correo', 'UID: 104969', '::1', '2025-12-27 00:27:46'),
(32, 1, 'consultar_correos', 'Email: lucazioni@xtreamvz.xyz', '::1', '2025-12-27 00:32:39'),
(33, 1, 'consultar_correos', 'Email: lucazioni@xtreamvz.xyz', '::1', '2025-12-27 00:32:43'),
(34, 1, 'ver_correo', 'UID: 104969', '::1', '2025-12-27 00:32:48'),
(35, 1, 'agregar_cliente', 'Usuario: 3043780769', '::1', '2025-12-27 00:34:16'),
(36, 1, 'actualizar_password', 'Usuario ID: 4', '::1', '2025-12-27 00:49:52'),
(37, 1, 'eliminar_cliente', 'Usuario: 3043780769', '::1', '2025-12-27 00:50:06'),
(38, 1, 'logout', 'Cierre de sesión', '::1', '2025-12-27 01:28:31'),
(39, 1, 'login', 'Inicio de sesión', '::1', '2025-12-27 01:47:07'),
(40, 1, 'logout', 'Cierre de sesión', '::1', '2025-12-27 01:54:46'),
(41, 1, 'login', 'Inicio de sesión', '::1', '2025-12-27 01:54:56'),
(42, 1, 'logout', 'Cierre de sesión', '::1', '2025-12-27 02:15:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_plain` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','cliente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cliente',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `password_plain`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$526yBFZKyd9esc36Ib4MXuajw4hw3fu4NqU01/EzmZT6SV3xeSAii', '123456', 'admin', '2025-12-26 21:36:53', '2025-12-27 01:46:07'),
(2, 'cliente1@streamingplus.ef', '$2y$10$7YxLAQpgHalKEpeY4RrV4uJiIspY8BbrfoigsH2xAVP.a0oPHKVbi', 'admin123', 'cliente', '2025-12-26 21:36:53', '2025-12-27 00:48:15'),
(3, 'cliente2@streamingplus.ef', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin123', 'cliente', '2025-12-26 21:36:53', '2025-12-27 00:48:15');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha` (`created_at`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  ADD CONSTRAINT `logs_actividad_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
