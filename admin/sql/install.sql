CREATE TABLE IF NOT EXISTS `#__itpsc_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `info` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'It is a URL to small image.',
  `url` varchar(255) DEFAULT NULL COMMENT 'It is a URL to page.',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpsc_activities_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__itpsc_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `note` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'It is a URL to small image.',
  `url` varchar(255) DEFAULT NULL COMMENT 'It is a URL to page.',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpsc_notifications_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__itpsc_profiles` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `alias` varchar(128) NOT NULL,
  `image` varchar(64) DEFAULT NULL,
  `image_icon` varchar(64) DEFAULT NULL,
  `image_square` varchar(64) DEFAULT NULL,
  `image_small` varchar(64) DEFAULT NULL,
  `bio` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpsc_profiles_name` (`name`(16))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

