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

CREATE TABLE `home_element_target_entity` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `element_id` INT(10) UNSIGNED NOT NULL,
  `entity_type` INT(10) UNSIGNED NOT NULL,
  `entity_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `element_id` (`element_id` ASC),
  INDEX `entity` (`entity_type` ASC, `entity_id` ASC));

CREATE TABLE `home_block_type_target_entity` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `block_type` VARCHAR(255) NOT NULL,
  `entity_type` INT(10) UNSIGNED NOT NULL,
  `entity_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `block_type` (`block_type` ASC),
  INDEX `entity` (`entity_type` ASC, `entity_id` ASC));

INSERT INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\Assignment\\Integration\\Chamilo\\Application\\Calendar','Chamilo\\Application\\Weblcms\\Tool\\Implementation\\Assignment\\Integration','core','Calendar',1,'5.0.0');
INSERT INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\Calendar\\Integration\\Chamilo\\Application\\Calendar','Chamilo\\Application\\Weblcms\\Tool\\Implementation\\Calendar\\Integration','core','Calendar',1,'5.0.0');
INSERT INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Core\\Home\\Integration\\Chamilo\\Core\\Admin','Chamilo\\Core\\Home\\Integration',NULL,'Admin',1,'5.0.0');

CREATE TABLE `home_content_object_publication` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `element_id` INT(10) UNSIGNED NOT NULL,
  `content_object_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `element_id` (`element_id` ASC),
  INDEX `content_object_id` (`content_object_id` ASC));

INSERT INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Core\\Home\\Integration\\Chamilo\\Core\\Repository','Chamilo\\Core\\Home\\Integration',NULL,'Repository',1,'5.0.0');
INSERT INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Libraries\\Mail\\Mailer\\Platform','Chamilo\\Libraries\\Mail\\Mailer',NULL,'Mailer',1,'5.0.0');
INSERT INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Libraries\\Mail\\Mailer\\PhpMailer','Chamilo\\Libraries\\Mail\\Mailer',NULL,'Mailer',1,'5.0.0');

INSERT INTO `configuration_setting` (`context`, `variable`, `value`, `user_setting`) VALUES ('Chamilo\\Core\\Admin', 'mailer', 'Chamilo\\Libraries\\Mail\\Mailer\\PhpMailer\\Mailer', '0');

CREATE TABLE `repository_wiki_page_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wiki_page_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `creation_date` int(10) unsigned NOT NULL,
  `modification_date` int(10) unsigned NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `rwpf_wiki_page_id` (`wiki_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/* HoGent */
/* Perspectief */