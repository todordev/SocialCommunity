ALTER TABLE `#__itpsc_profiles` DROP `phone`;
ALTER TABLE `#__itpsc_profiles` DROP `address`;
ALTER TABLE `#__itpsc_locations` DROP `state_code`;
ALTER TABLE `#__itpsc_locations` DROP `published`;

ALTER TABLE `#__itpsc_profiles` CHANGE `country_id` `country_code` CHAR(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE `#__itpsc_socialprofiles` CHANGE `type` `service` ENUM('facebook','twitter','googleplus') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__itpsc_socialprofiles` ADD `access_token` MEDIUMBLOB NULL DEFAULT NULL AFTER `user_id`, ADD `expires_at` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `access_token`;
ALTER TABLE `#__itpsc_socialprofiles` ADD `secret_key` VARCHAR(1024) NULL DEFAULT NULL AFTER `expires_at`;

ALTER TABLE `#__itpsc_socialprofiles` ADD `service_user_id` BIGINT UNSIGNED NOT NULL DEFAULT '0' AFTER `alias`;
ALTER TABLE `#__itpsc_socialprofiles` CHANGE `alias` `link` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `#__itpsc_socialprofiles` ADD INDEX `idx_itpsp_sprofiles_uid_service` (`user_id`, `service`);
ALTER TABLE `#__itpsc_socialprofiles` ADD `image_square` VARCHAR(1024) NULL DEFAULT NULL AFTER `link`;

RENAME TABLE `#__itpsc_userwalls` TO `#__itpsc_posts`;
ALTER TABLE `#__itpsc_posts` CHANGE `created` `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `#__itpsc_countries` DROP `currency`;
ALTER TABLE `#__itpsc_countries` DROP `locale`;

CREATE TABLE IF NOT EXISTS `#__itpsc_profilecontacts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone` mediumblob,
  `address` mediumblob,
  `secret_key` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_scpc_uid` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__itpsc_socialfriends` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `friend_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service` enum('facebook','twitter','googleplus') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;