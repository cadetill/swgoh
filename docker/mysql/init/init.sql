-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 03-12-2021 a las 18:08:15
-- Versión del servidor: 10.3.32-MariaDB
-- Versión de PHP: 7.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cadetill_swgoh`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `here`
--

CREATE TABLE `here` (
                        `refId` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                        `team` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                        `users` varchar(5000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `queue`
--

CREATE TABLE `queue` (
                         `insdate` timestamp NOT NULL DEFAULT current_timestamp(),
                         `message_id` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         `date` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `raids`
--

CREATE TABLE `raids` (
                         `guildRefId` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         `raid` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         `fase` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         `allyCode` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         `percen` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stats`
--

CREATE TABLE `stats` (
                         `guildRefId` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         `team` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         `units` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teams`
--

CREATE TABLE `teams` (
                         `guildRefId` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         `team` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         `units` varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         `command` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tw`
--

CREATE TABLE `tw` (
                      `guildRefId` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                      `allyCode` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                      `unit` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                      `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
                      `points` int(11) DEFAULT NULL,
                      `vs` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
                      `unittype` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
                      `datectrl` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `twconf`
--

CREATE TABLE `twconf` (
                          `guildRefId` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                          `noreg` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `twh`
--

CREATE TABLE `twh` (
                       `guildRefId` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                       `twDate` varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                       `allyCode` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                       `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                       `rogues` int(11) NOT NULL,
                       `battles` int(11) NOT NULL,
                       `points` int(11) NOT NULL,
                       `percent` float(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
                         `id` int(11) NOT NULL,
                         `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
                         `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
                         `allycode` int(11) NOT NULL,
                         `language` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `guild_requirements` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `guildRefId` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         `alias` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
                         `definition` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                         PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `here`
--
ALTER TABLE `here`
    ADD PRIMARY KEY (`refId`,`team`);

--
-- Indices de la tabla `queue`
--
ALTER TABLE `queue`
    ADD PRIMARY KEY (`insdate`,`message_id`,`date`);

--
-- Indices de la tabla `raids`
--
ALTER TABLE `raids`
    ADD PRIMARY KEY (`guildRefId`,`raid`,`fase`,`allyCode`);

--
-- Indices de la tabla `stats`
--
ALTER TABLE `stats`
    ADD PRIMARY KEY (`guildRefId`,`team`);

--
-- Indices de la tabla `teams`
--
ALTER TABLE `teams`
    ADD PRIMARY KEY (`guildRefId`,`team`);

--
-- Indices de la tabla `tw`
--
ALTER TABLE `tw`
    ADD PRIMARY KEY (`guildRefId`,`allyCode`,`unit`) USING BTREE;

--
-- Indices de la tabla `twconf`
--
ALTER TABLE `twconf`
    ADD PRIMARY KEY (`guildRefId`);

--
-- Indices de la tabla `twh`
--
ALTER TABLE `twh`
    ADD PRIMARY KEY (`guildRefId`,`twDate`,`allyCode`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`id`);
COMMIT;

--
-- Usuario de prueba
--
INSERT INTO `users` (`id`, `username`, `name`, `allycode`, `language`) VALUES
    (5006687,	'cadetill',	'cadetill',	336771469,	'SPA_XM');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
