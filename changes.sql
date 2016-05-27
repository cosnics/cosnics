CREATE TABLE `chamilo`.`portfolio_user_favourite` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `source_user_id` INT NOT NULL DEFAULT 0,
  `favourite_user_id` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `user_favourite` (`source_user_id` ASC, `favourite_user_id` ASC));


INSERT IGNORE INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`) VALUES(NULL, 'Chamilo\\Application\\Portfolio\\Integration\\Chamilo\\Core\\Home', 'Chamilo\\Application\\Portfolio\\Integration', NULL, 'Home', 1, '5.0.0');

ALTER TABLE `chamilo`.`weblcms_course_group_user_relation`
ADD COLUMN `subscription_time` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `user_id`;