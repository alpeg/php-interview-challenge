/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;

CREATE TABLE IF NOT EXISTS `taskitem`
(
    `id`       int(11)                                       NOT NULL AUTO_INCREMENT,
    `username` varchar(200) COLLATE utf8mb4_unicode_nopad_ci NOT NULL,
    `email`    varchar(200) COLLATE utf8mb4_unicode_nopad_ci NOT NULL,
    `text`     varchar(200) COLLATE utf8mb4_unicode_nopad_ci NOT NULL,
    `complete` tinyint(4)                                    NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_nopad_ci;

CREATE TABLE IF NOT EXISTS `users`
(
    `id`       int(11)                                       NOT NULL AUTO_INCREMENT,
    `username` varchar(200) COLLATE utf8mb4_unicode_nopad_ci NOT NULL,
    `password` varchar(200) COLLATE utf8mb4_unicode_nopad_ci NOT NULL,
    `admin`    tinyint(4)                                    NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE KEY `username` (`username`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_nopad_ci;
/*!40000 ALTER TABLE `users`
    DISABLE KEYS */;
INSERT INTO `users` (`id`, `username`, `password`, `admin`)
VALUES (1, 'admin', '123', 1),
       (2, 'user', '123', 0);
/*!40000 ALTER TABLE `users`
    ENABLE KEYS */;

/*!40101 SET SQL_MODE = IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS = IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
