CREATE TABLE `chamilo`.`portfolio_user_favourite` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `source_user_id` INT NOT NULL DEFAULT 0,
  `favourite_user_id` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `user_favourite` (`source_user_id` ASC, `favourite_user_id` ASC));


INSERT IGNORE INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`) VALUES(NULL, 'Chamilo\\Application\\Portfolio\\Integration\\Chamilo\\Core\\Home', 'Chamilo\\Application\\Portfolio\\Integration', NULL, 'Home', 1, '5.0.0');

ALTER TABLE `chamilo`.`weblcms_course_group_user_relation`
ADD COLUMN `subscription_time` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `user_id`;

ALTER TABLE `chamilo`.`repository_workspace_content_object_relation` CHANGE COLUMN `content_object_id` `content_object_id` VARCHAR(36) NOT NULL COMMENT '' ;

UPDATE `chamilo`.`repository_workspace_content_object_relation` SET content_object_id = (SELECT object_number FROM `chamilo`.`repository_content_object` WHERE `chamilo`.`repository_content_object`.id = `chamilo`.`repository_workspace_content_object_relation`.content_object_id)