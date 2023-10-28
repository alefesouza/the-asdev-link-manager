SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `links` (
  `id` int(11) NOT NULL,
  `link` text COLLATE utf8_unicode_ci NOT NULL,
  `short_link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ios_link` text COLLATE utf8_unicode_ci,
  `android_link` text COLLATE utf8_unicode_ci,
  `clicks` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `short_link_index` (`short_link`);

ALTER TABLE `links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;
