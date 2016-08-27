/*
 * This schema is only used temporarily until the past logs are imported into files on disk.
 * See https://github.com/indieweb/chat.indieweb.org/issues/8
 */

CREATE TABLE `irclog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel` varchar(100) DEFAULT NULL,
  `day` date DEFAULT NULL,
  `nick` varchar(40) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `timestamp` bigint(20) DEFAULT NULL,
  `line` text,
  `spam` tinyint(1) DEFAULT '0',
  `hide` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `channel_timestamp` (`channel`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
