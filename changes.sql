ALTER TABLE `repository_rss_feed` 
ADD COLUMN `number_of_items` INT(11) NOT NULL DEFAULT 0 COMMENT '' AFTER `url`;

INSERT IGNORE INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Core\\Repository\\ContentObject\\File\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\Assignment','Chamilo\\Core\\Repository\\ContentObject\\File\\Integration',NULL,'assignment_integration',1,'5.0.0');
INSERT IGNORE INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Core\\Repository\\ContentObject\\Page\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\Assignment','Chamilo\\Core\\Repository\\ContentObject\\Page\\Integration',NULL,'assignment_integration',1,'5.0.0');
INSERT IGNORE INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Core\\Repository\\ContentObject\\Webpage\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\Assignment','Chamilo\\Core\\Repository\\ContentObject\\Webpage\\Integration',NULL,'assignment_integration',1,'5.0.0');
INSERT IGNORE INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Core\\Repository\\ContentObject\\Link\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\Assignment','Chamilo\\Core\\Repository\\ContentObject\\Link\\Integration',NULL,'assignment_integration',1,'5.0.0');
INSERT IGNORE INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Core\\Repository\\ContentObject\\Youtube\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\Assignment','Chamilo\\Core\\Repository\\ContentObject\\Youtube\\Integration',NULL,'assignment_integration',1,'5.0.0');
INSERT IGNORE INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Core\\Repository\\ContentObject\\Vimeo\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\Assignment','Chamilo\\Core\\Repository\\ContentObject\\Vimeo\\Integration',NULL,'assignment_integration',1,'5.0.0');
INSERT IGNORE INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Core\\Repository\\ContentObject\\Slideshare\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\Assignment','Chamilo\\Core\\Repository\\ContentObject\\Slideshare\\Integration',NULL,'assignment_integration',1,'5.0.0');
INSERT IGNORE INTO `configuration_registration` (`id`,`context`,`type`,`category`,`name`,`status`,`version`) VALUES (NULL,'Chamilo\\Core\\Repository\\ContentObject\\Soundcloud\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\Assignment','Chamilo\\Core\\Repository\\ContentObject\\Soundcloud\\Integration',NULL,'assignment_integration',1,'5.0.0');

CREATE TABLE `portfolio_user_favourite` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `source_user_id` INT NOT NULL DEFAULT 0,
  `favourite_user_id` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `user_favourite` (`source_user_id` ASC, `favourite_user_id` ASC));


INSERT IGNORE INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`) VALUES(NULL, 'Chamilo\\Application\\Portfolio\\Integration\\Chamilo\\Core\\Home', 'Chamilo\\Application\\Portfolio\\Integration', NULL, 'Home', 1, '5.0.0');

ALTER TABLE `weblcms_course_group_user_relation`
ADD COLUMN `subscription_time` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `user_id`;

ALTER TABLE `repository_workspace_content_object_relation` CHANGE COLUMN `content_object_id` `content_object_id` VARCHAR(36) NOT NULL COMMENT '' ;

UPDATE `repository_workspace_content_object_relation` SET content_object_id = (SELECT object_number FROM `repository_content_object` WHERE `repository_content_object`.id = `repository_workspace_content_object_relation`.content_object_id)

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

ALTER TABLE `menu_application_item`
  ADD COLUMN `component` VARCHAR(255) NULL DEFAULT NULL AFTER `application`,
  ADD COLUMN `extra_parameters` VARCHAR(255) NULL DEFAULT NULL AFTER `component`;

/* HoGent */
/* Perspectief */

INSERT INTO `configuration_setting` (`id`, `context`, `variable`, `value`, `user_setting`) VALUES(NULL, 'Chamilo\\Core\\Admin', 'google_analytics_tracking_id', '', 0);
INSERT INTO `configuration_setting` (`id`, `context`, `variable`, `value`, `user_setting`) VALUES(NULL, 'Chamilo\\Core\\Menu', 'brand_image', '', 0);

ALTER TABLE `repository_file`
  ADD COLUMN `show_inline` INT(3) NOT NULL DEFAULT 1 AFTER `hash`;

CREATE TABLE `rights_structure_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `context` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rsl_location` (`context`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `rights_structure_location_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `structure_location_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rslr_structure_location_role` (`structure_location_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ur_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_role_relation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `urr_user_role` (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `configuration_registration` VALUES (NULL, 'Chamilo\\Core\\Rights\\Structure', 'Chamilo\\Core\\Rights', 'Core', 'StructureRights', '1', '5.0.0');
INSERT INTO `configuration_registration` VALUES (NULL, 'Chamilo\\Core\\User\\Roles', 'Chamilo\\Core\\User', 'Core', 'UserRoles', '1', '5.0.0');
INSERT INTO `configuration_registration` VALUES (NULL, 'Chamilo\\Application\\Weblcms\\Course\\OpenCourse', 'Chamilo\\Application\\Weblcms\\Course', 'Application', 'OpenCourses', '1', '5.0.0');
INSERT INTO `configuration_setting` VALUES (NULL, 'Chamilo\\Core\\Admin', 'enableAnonymousAuthentication', '0', '0');
INSERT INTO `configuration_setting` VALUES (NULL, 'Chamilo\\Core\\Admin', 'anonymous_authentication_url', '', '0');
INSERT INTO `configuration_setting` VALUES (NULL, 'Chamilo\\Core\\Admin', 'recaptcha_site_key', '', '0');
INSERT INTO `configuration_setting` VALUES (NULL, 'Chamilo\\Core\\Admin', 'recaptcha_secret_key', '', '0');
INSERT INTO `configuration_setting` VALUES (NULL, 'Chamilo\\Core\\Admin', 'page_after_anonymous_access', '', '0');
INSERT INTO `configuration_setting` (`id`, `context`, `variable`, `value`, `user_setting`) VALUES (NULL, 'Chamilo\\Core\\Repository\\Form', 'omit_content_object_title_check', '0', '0');

INSERT INTO `configuration_setting` (`id`, `context`, `variable`, `value`, `user_setting`) VALUES (NULL, 'Chamilo\\Core\\Admin', 'cas_version', 'SAML_VERSION_1_1', '0');
INSERT INTO `configuration_setting` (`id`, `context`, `variable`, `value`, `user_setting`) VALUES (NULL, 'Chamilo\\Core\\Admin', 'cas_check_certificate', '1', '0');
INSERT INTO `configuration_setting` (`id`, `context`, `variable`, `value`, `user_setting`) VALUES (NULL, 'Chamilo\\Core\\Admin', 'cas_user_login', 'login', '0');
INSERT INTO `configuration_setting` (`id`, `context`, `variable`, `value`, `user_setting`) VALUES (NULL, 'Chamilo\\Core\\Admin', 'cas_validation_string', 'EXT', '0');