CREATE TABLE IF NOT EXISTS `#__itpsc_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'It is a URL to small image.',
  `url` varchar(255) DEFAULT NULL COMMENT 'It is a URL to page.',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpsc_activities_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__itpsc_countries` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `code` char(2) NOT NULL,
  `code4` varchar(5) NOT NULL DEFAULT '' COMMENT 'A code with 4 letters.',
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `currency` char(3) DEFAULT NULL,
  `timezone` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__itpsc_locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `latitude` varchar(64) NOT NULL,
  `longitude` varchar(64) NOT NULL,
  `country_code` char(2) NOT NULL,
  `state_code` char(4) NOT NULL DEFAULT '',
  `timezone` varchar(40) NOT NULL,
  `published` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__itpsc_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'It is a URL to small image.',
  `url` varchar(255) DEFAULT NULL COMMENT 'It is a URL to page.',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
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
  `phone` varchar(64) DEFAULT NULL,
  `address` varchar(2048) DEFAULT NULL,
  `birthday` date NOT NULL DEFAULT '0000-00-00',
  `gender` enum('male','female') NOT NULL DEFAULT 'male',
  `location_id` int(11) unsigned NOT NULL DEFAULT '0',
  `country_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `website` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpsc_profiles_name` (`name`(16))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__itpsc_socialprofiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(64) NOT NULL,
  `type` enum('facebook','twitter','linkedin') NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_scsp_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;