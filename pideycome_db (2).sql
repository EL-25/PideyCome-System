-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 10-05-2026 a las 04:03:38
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pideycome_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

DROP TABLE IF EXISTS `caja`;
CREATE TABLE IF NOT EXISTS `caja` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `monto_apertura` decimal(10,2) NOT NULL DEFAULT '0.00',
  `monto_cierre` decimal(10,2) DEFAULT NULL,
  `ventas_total` decimal(10,2) DEFAULT '0.00',
  `estado` enum('abierta','cerrada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'abierta',
  `fecha_apertura` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_cierre` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `caja_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_03_20_173543_create_productos_table', 1),
(5, '2026_03_20_193623_create_pedidos_table', 1),
(6, '2026_03_20_193647_create_pedido_detalles_table', 1),
(7, '2026_03_22_003622_add_notificacion_leida_to_pedidos_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `cliente` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_orden` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mesa_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('ordenada','recibida','preparando','despachada','pagado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ordenada',
  `notificacion_leida` tinyint(1) NOT NULL DEFAULT '0',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `metodo_pago` enum('efectivo','tarjeta','transferencia') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cajera_id` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pedidos_user_id_foreign` (`user_id`),
  KEY `fk_pedidos_cajera` (`cajera_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `user_id`, `cliente`, `tipo_orden`, `mesa_id`, `estado`, `notificacion_leida`, `total`, `created_at`, `updated_at`, `metodo_pago`, `cajera_id`) VALUES
(1, 6, 'Anderson', 'comer_aqui', NULL, 'preparando', 1, 4.00, '2026-05-10 00:31:44', '2026-05-10 00:32:22', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalles`
--

DROP TABLE IF EXISTS `pedido_detalles`;
CREATE TABLE IF NOT EXISTS `pedido_detalles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint UNSIGNED NOT NULL,
  `producto_nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_detalles_pedido_id_foreign` (`pedido_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedido_detalles`
--

INSERT INTO `pedido_detalles` (`id`, `pedido_id`, `producto_nombre`, `cantidad`, `precio`, `created_at`, `updated_at`) VALUES
(1, 1, 'hamburguesa', 1, 4.00, '2026-05-10 00:31:44', '2026-05-10 00:31:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE IF NOT EXISTS `productos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(8,2) NOT NULL,
  `categoria` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock` int NOT NULL,
  `imagen` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `categoria`, `stock`, `imagen`, `created_at`, `updated_at`) VALUES
(1, 'hamburguesa', 4.00, 'Comida', 25, 'default.png', '2026-05-10 00:31:24', '2026-05-10 00:33:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `temp_passwd` tinyint(1) NOT NULL DEFAULT '0',
  `role` enum('admin','mesero','cocina','cajera') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mesero',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `temp_passwd`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador Sistema', 'admin', '$2y$12$Z/XCoV5E/4BrpI8yi5U5xuWWMWj8m2A.2ruyXJ1nQtmBTfWHLN/Ue', 0, 'admin', NULL, '2026-05-10 00:19:18', '2026-05-10 00:19:18'),
(2, 'Mesero Turno 1', 'mesero1', '$2y$12$V06qDUjI4p.L6HI4Dw8kb.Mlfd9scs50Uc.IfIu8hS4ow/f0IXfji', 0, 'mesero', NULL, '2026-05-10 00:19:18', '2026-05-10 00:19:18'),
(3, 'Cocinero Principal', 'cocina1', '$2y$12$bUekIH6RAVdNPdkP6/e0ZOn/raXXFnsD71mqXP09hQG9O7OlxyrVm', 0, 'cocina', NULL, '2026-05-10 00:19:18', '2026-05-10 00:19:18'),
(4, 'Cajera Central', 'cajera1', '$2y$12$IEKwvB3Abl/cfq0etgbKzeFvxPhynZsnWlamyKbupqjens6/cRZti', 0, 'cajera', NULL, '2026-05-10 00:19:18', '2026-05-10 00:19:18'),
(6, 'Anderson', 'usuario2', '$2y$12$4ExVnhhqtOWfUIXvgyEZwOIV2cUQa/cSRoFwlZEgXMcG8TPlmZW16', 0, 'mesero', NULL, '2026-05-10 00:28:38', '2026-05-10 00:29:13');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
