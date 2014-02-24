ALTER TABLE `#__itpsc_profiles` ADD `phone` VARCHAR( 64 ) NULL DEFAULT NULL AFTER `bio` ;
ALTER TABLE `#__itpsc_profiles` ADD `address` VARCHAR( 2048 ) NULL DEFAULT NULL AFTER `phone` ;
ALTER TABLE `#__itpsc_profiles` ADD `location_id` INT UNSIGNED  NOT NULL DEFAULT '0' AFTER `address` ;
ALTER TABLE `#__itpsc_profiles` ADD `country_id` SMALLINT  UNSIGNED NOT NULL DEFAULT '0' AFTER `location_id` ;
ALTER TABLE `#__itpsc_profiles` ADD `website` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `country_id` ;
ALTER TABLE `#__itpsc_profiles` ADD `birthday` DATE NOT NULL DEFAULT '0000-00-00' AFTER `address` ;
ALTER TABLE `#__itpsc_profiles` ADD `gender` ENUM( "male", "female" ) NOT NULL DEFAULT 'male' AFTER `birthday` ;

ALTER TABLE `#__itpsc_activities` CHANGE `info` `content` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `#__itpsc_notifications` CHANGE `read` `status` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__itpsc_notifications` CHANGE `note` `content` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

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

CREATE TABLE IF NOT EXISTS `#__itpsc_socialprofiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(64) NOT NULL,
  `type` enum('facebook','twitter','linkedin') NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_scsp_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;