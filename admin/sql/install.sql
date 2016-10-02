CREATE TABLE IF NOT EXISTS `#__itpsc_activities` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'It is a URL to small image.',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'It is a URL to page.',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpsc_activities_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__itpsc_countries` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code4` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'A code with 4 letters.',
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__itpsc_locations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitude` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_code` char(4) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timezone` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `published` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_itpsc_location_name` (`name`(16))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__itpsc_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'It is a URL to small image.',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'It is a URL to page.',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpsc_notifications_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__itpsc_profiles` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_icon` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_square` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_small` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` mediumblob,
  `address` mediumblob,
  `birthday` date NOT NULL DEFAULT '0000-00-00',
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'male',
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `params` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `user_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `country_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_itpsc_profile_alias` (`alias`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__itpsc_socialprofiles` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('facebook','twitter','linkedin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpsc_sprofiles_user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__itpsc_userwalls` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` varchar(140) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media` enum('website','picture','video') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpsc_userwalls_user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
