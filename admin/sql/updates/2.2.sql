ALTER TABLE `#__itpsc_profiles` DROP INDEX `idx_itpsc_profiles_name`;
ALTER TABLE `#__itpsc_profiles` CHANGE `id` `user_id` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `#__itpsc_profiles` DROP PRIMARY KEY;
ALTER TABLE `#__itpsc_profiles` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
ALTER TABLE `#__itpsc_profiles` ADD UNIQUE `idx_itpsc_profile_alias` (`alias`(128));
ALTER TABLE `#__itpsc_profiles` ADD `params` VARCHAR(2048) NULL DEFAULT NULL AFTER `website`;
ALTER TABLE `#__itpsc_profiles` ADD `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `params`;
ALTER TABLE `#__itpsc_profiles` CHANGE `phone` `phone` MEDIUMBLOB NULL DEFAULT NULL;
ALTER TABLE `#__itpsc_profiles` CHANGE `address` `address` MEDIUMBLOB NULL DEFAULT NULL;

ALTER TABLE `#__itpsc_countries` ENGINE = MYISAM;
ALTER TABLE `#__itpsc_locations` ENGINE = MYISAM;

ALTER TABLE `#__itpsc_locations` ADD INDEX `idx_itpsc_location_name` (`name`(16));

ALTER TABLE `#__itpsc_socialprofiles` DROP INDEX `idx_scsp_user_id`, ADD INDEX `idx_itpsc_sprofiles_user_id` (`user_id`) USING BTREE;

CREATE TABLE IF NOT EXISTS `#__itpsc_userwalls` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` varchar(140) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `media` enum('website','picture','video') DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpsc_userwalls_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;