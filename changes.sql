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

INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (120,'Chamilo\\Core\\Reporting','paper_orientation','Portrait',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (121,'Chamilo\\Core\\Reporting','template_header_text_color','0, 0, 0',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (122,'Chamilo\\Core\\Reporting','template_header_font_family','Arial',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (123,'Chamilo\\Core\\Reporting','template_header_font_style','B',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (124,'Chamilo\\Core\\Reporting','template_header_font_size','11',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (125,'Chamilo\\Core\\Reporting','template_header_line_color','0, 0, 0',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (126,'Chamilo\\Core\\Reporting','template_footer_text_color','0, 0, 0',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (127,'Chamilo\\Core\\Reporting','template_footer_font_family','Arial',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (128,'Chamilo\\Core\\Reporting','template_footer_font_style','B',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (129,'Chamilo\\Core\\Reporting','template_footer_font_size','10',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (130,'Chamilo\\Core\\Reporting','block_title_text_color','0, 0, 0',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (131,'Chamilo\\Core\\Reporting','block_title_font_family','Arial',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (132,'Chamilo\\Core\\Reporting','block_title_font_style','B',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (133,'Chamilo\\Core\\Reporting','block_title_font_size','11',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (134,'Chamilo\\Core\\Reporting','min_relative_width','0.1',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (135,'Chamilo\\Core\\Reporting','heading_cell_alignment','Left',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (136,'Chamilo\\Core\\Reporting','heading_cell_text_color','255, 255, 255',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (137,'Chamilo\\Core\\Reporting','heading_cell_background_color','150, 150, 150',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (138,'Chamilo\\Core\\Reporting','heading_cell_border_color','0, 0, 0',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (139,'Chamilo\\Core\\Reporting','heading_cell_font_family','Arial',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (140,'Chamilo\\Core\\Reporting','heading_cell_font_style','',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (141,'Chamilo\\Core\\Reporting','heading_cell_font_size','10',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (142,'Chamilo\\Core\\Reporting','data_cell_alignment','Left',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (143,'Chamilo\\Core\\Reporting','data_cell_text_color','0, 0, 0',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (144,'Chamilo\\Core\\Reporting','data_cell_background_color','255, 255, 255',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (145,'Chamilo\\Core\\Reporting','data_cell_border_color','0, 0, 0',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (146,'Chamilo\\Core\\Reporting','data_cell_font_family','Arial',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (147,'Chamilo\\Core\\Reporting','data_cell_font_style','',0);
INSERT IGNORE INTO `configuration_setting` (`id`,`context`,`variable`,`value`,`user_setting`) VALUES (148,'Chamilo\\Core\\Reporting','data_cell_font_size','10',0);

/* Learning Path Refactoring */

CREATE TABLE IF NOT EXISTS `repository_learning_path_tree_node_data` (
  `id` int(10) unsigned NOT NULL,
  `learning_path_id` int(10) unsigned NOT NULL,
  `parent_tree_node_data_id` int(10) unsigned NOT NULL,
  `content_object_id` int(10) unsigned NOT NULL,
  `max_attempts` int(10) unsigned NOT NULL DEFAULT '0',
  `mastery_score` int(10) unsigned NOT NULL DEFAULT '0',
  `allow_hints` tinyint(1) NOT NULL DEFAULT '1',
  `show_score` tinyint(1) NOT NULL DEFAULT '1',
  `show_correction` tinyint(1) NOT NULL DEFAULT '1',
  `show_solution` tinyint(1) NOT NULL DEFAULT '1',
  `show_answer_feedback` int(10) unsigned NOT NULL DEFAULT '7',
  `feedback_location` int(10) unsigned NOT NULL DEFAULT '3',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  `display_order` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `added_date` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `repository_learning_path_tree_node_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `learning_path_id` (`learning_path_id`),
  ADD KEY `content_object_id` (`content_object_id`) USING BTREE,
  ADD KEY `section_content_object_id` (`parent_tree_node_data_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `repository_learning_path_tree_node_data` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;

DELETE FROM configuration_registration WHERE context = 'Chamilo\\Core\\Repository\\ContentObject\\Note\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\LearningPath';
DELETE FROM configuration_registration WHERE context = 'Chamilo\\Core\\Repository\\ContentObject\\Description\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\LearningPath';
DELETE FROM configuration_registration WHERE context = 'Chamilo\\Core\\Repository\\ContentObject\\Blog\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\LearningPath';
DELETE FROM configuration_registration WHERE context = 'Chamilo\\Core\\Repository\\ContentObject\\Webpage\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\LearningPath';
DELETE FROM configuration_registration WHERE context = 'Chamilo\\Core\\Repository\\ContentObject\\Announcement\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\LearningPath';

ALTER TABLE `tracking_weblcms_learning_path_item_attempt` RENAME `tracking_weblcms_learning_path_tree_node_attempt`;
ALTER TABLE `tracking_weblcms_learning_path_question_attempt` RENAME `tracking_weblcms_learning_path_tree_node_question_attempt`;

ALTER TABLE `tracking_weblcms_learning_path_tree_node_attempt`
  ADD `user_id` INT(10) UNSIGNED NOT NULL AFTER `id`,
  ADD `learning_path_id` INT(10) UNSIGNED NOT NULL AFTER `user_id`,
  ADD `publication_id` INT(10) UNSIGNED NOT NULL AFTER `learning_path_id`,
  DROP `lesson_location`,
  DROP `suspend_data`,
  DROP `min_score`,
  DROP `max_score`;

ALTER TABLE `tracking_weblcms_learning_path_tree_node_attempt` CHANGE `learning_path_item_id` `tree_node_data_id` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `tracking_weblcms_learning_path_tree_node_question_attempt` CHANGE `item_attempt_id` `tree_node_attempt_id` INT(10) UNSIGNED NOT NULL;

ALTER TABLE `tracking_weblcms_learning_path_tree_node_attempt`
  ADD INDEX(`user_id`),
  ADD INDEX(`learning_path_id`),
  ADD INDEX(`publication_id`);

# ALTER TABLE `tracking_weblcms_learning_path_attempt` ADD `publication_id` INT(10) UNSIGNED NOT NULL AFTER `learning_path_id`;
# UPDATE `tracking_weblcms_learning_path_attempt` SET publication_id = learning_path_id;

UPDATE `tracking_weblcms_learning_path_tree_node_attempt` TN_ATT
  JOIN tracking_weblcms_learning_path_attempt LP_ATT on TN_ATT.learning_path_attempt_id = LP_ATT.id
SET TN_ATT.user_id = LP_ATT.user_id, TN_ATT.learning_path_id = LP_ATT.learning_path_id;

UPDATE `tracking_weblcms_learning_path_tree_node_attempt` SET publication_id = learning_path_id;

UPDATE `tracking_weblcms_learning_path_tree_node_attempt` AS ATT
JOIN weblcms_content_object_publication PUB on PUB.id = ATT.publication_id
SET ATT.learning_path_id = PUB.content_object_id;

DROP TABLE tracking_weblcms_learning_path_attempt;
ALTER TABLE `tracking_weblcms_learning_path_tree_node_attempt` DROP `learning_path_attempt_id`;

UPDATE `tracking_weblcms_learning_path_tree_node_attempt`
SET status = 1 WHERE status IN ('passed', 'failed', 'completed');

UPDATE `tracking_weblcms_learning_path_tree_node_attempt`
SET status = 0 WHERE status IN ('not attempted');

ALTER TABLE `tracking_weblcms_learning_path_tree_node_attempt` CHANGE `status` `completed` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `repository_learning_path`
  DROP `control_mode`,
  DROP `version`,
  DROP `path`;

ALTER TABLE `repository_learning_path` ADD `automatic_numbering` VARCHAR(15) NOT NULL DEFAULT 'none' AFTER `id`, ADD `enforce_default_traversing_order` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `automatic_numbering`;

DELETE FROM `configuration_registration` WHERE `context` LIKE '%LearningPathItem%';
DELETE FROM `configuration_registration` WHERE `context` LIKE 'Chamilo\\\\Core\\\\Repository\\\\ContentObject\\\\LearningPath\\\\Integration\\\\Chamilo\\\\Core\\\\Repository\\\\ContentObject\\\\LearningPath';

ALTER TABLE `configuration_registration` ADD `priority` INT(3) UNSIGNED NOT NULL DEFAULT '1';
UPDATE configuration_registration SET priority = 20 WHERE `context` LIKE 'Chamilo\\\\Core\\\\Repository\\\\ContentObject\\\\Assessment\\\\Integration\\\\Chamilo\\\\Core\\\\Repository\\\\ContentObject\\\\LearningPath';
UPDATE configuration_registration SET priority = 50 WHERE `context` LIKE 'Chamilo\\\\Core\\\\Repository\\\\ContentObject\\\\Page\\\\Integration\\\\Chamilo\\\\Core\\\\Repository\\\\ContentObject\\\\LearningPath';

INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Core\\Repository\\ContentObject\\File\\Integration\\Chamilo\\Core\\Repository\\HtmlEditor', 'Chamilo\\Core\\Repository\\ContentObject\\File\\Integration', NULL, 'Repository', '1', '5.0.0', '1');
INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Integration\\Chamilo\\Core\\Repository', 'Chamilo\\Core\\Repository\\ContentObject\\earningPath\\Integration', NULL, 'Repository', '1', '5.0.0', '1');

/**
Section content object install! (lynx package manager)
 */

/** Migratie uitvoeren via code
 * Post migratie SQL
**/

DELETE FROM `repository_complex_content_object_item` WHERE parent_id IN
(SELECT id FROM repository_content_object CO WHERE CO.type = 'Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Storage\\DataClass\\LearningPath');

DELETE FROM `repository_complex_learning_path_item`;
DELETE FROM `repository_content_object` WHERE `type` LIKE '%LearningPathItem%';

/** NEW **/

INSERT INTO `configuration_setting` (`id`, `context`, `variable`, `value`, `user_setting`) VALUES (NULL, 'Chamilo\\Core\\Admin', 'maintenance_warning_show', '0', '0'),
  (NULL, 'Chamilo\\Core\\Admin', 'maintenance_warning_message', NULL, '0'),
  (NULL, 'Chamilo\\Core\\Admin', 'maintenance_block_access', 0, '0');

DELETE FROM `configuration_setting` WHERE `variable` LIKE 'maintenance_mode' AND context LIKE 'Chamilo\\\\Core\\\\Admin';

ALTER TABLE `repository_learning_path_tree_node_data` ADD `enforce_default_traversing_order` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `added_date`;

/** 16 OCT 2017 **/
INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\CourseGroup\\Extension\\Office365', 'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\CourseGroup\\Extension', 'Extension', 'Office365', '1', '1.0.0', '1');
INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\CourseGroup\\Extension\\Office365\\Integration\\Chamilo\\Application\\Weblcms\\Tool\\Implementation\\CourseGroup', 'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\CourseGroup\\Extension\\Office365\\Integration', 'Integration', 'Office365', '1', '1.0.0', '1');

INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\Document\\Integration\\Chamilo\\Application\\Weblcms\\Tool\\Implementation\\CourseGroup', 'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\Document\\Integration', 'Integration', 'Document', '1', '1.0.0', '1');
INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\Forum\\Integration\\Chamilo\\Application\\Weblcms\\Tool\\Implementation\\CourseGroup', 'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\Forum\\Integration', 'Integration', 'Forum', '1', '1.0.0', '1');
INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Libraries\\Protocol\\Microsoft\\Graph', 'Chamilo\\Libraries', NULL, 'MicrosoftGraph', '1', '1.0.0', '1');

CREATE TABLE `weblcms_course_group_publication_category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_group_id` int(10) UNSIGNED NOT NULL,
  `publication_category_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `weblcms_course_group_office365_reference` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_group_id` int(10) UNSIGNED NOT NULL,
  `office365_group_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `office365_plan_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `linked` TINYINT(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `configuration_setting` (`id`, `context`, `variable`, `value`, `user_setting`) VALUES
  (NULL, 'Chamilo\\Libraries\\Protocol\\Microsoft\\Graph', 'client_id', NULL, '0'),
  (NULL, 'Chamilo\\Libraries\\Protocol\\Microsoft\\Graph', 'client_secret', NULL, '0'),
  (NULL, 'Chamilo\\Libraries\\Protocol\\Microsoft\\Graph', 'access_token', NULL, '1'),
  (NULL, 'Chamilo\\Libraries\\Protocol\\Microsoft\\Graph', 'external_user_id', NULL, '1'),
  (NULL, 'Chamilo\\Libraries\\Protocol\\Microsoft\\Graph', 'tenant_id', NULL, '0'),
  (NULL, 'Chamilo\\Libraries\\Protocol\\Microsoft\\Graph', 'cosnics_prefix', NULL, 'cosnics_'),
  (NULL, 'Chamilo\\Libraries\\Protocol\\Microsoft\\Graph', 'planner_base_uri', 'https://tasks.office.com/tenantName.onmicrosoft.com/nl-NL/Home/Planner', '0'),
  (NULL, 'Chamilo\\Libraries\\Protocol\\Microsoft\\Graph', 'group_base_uri', 'https://outlook.office.com/owa/?realm=realm&amp;exsvurl=1&amp;ll-cc=1043&amp;modurl=0&amp;path=/group/{GROUP_ID}@tenant.onmicrosoft.com/people', '0');

INSERT INTO weblcms_course_group_publication_category
    SELECT NULL, CG.id, CG.document_category_id FROM weblcms_course_group CG WHERE CG.document_category_id > 0;

INSERT INTO weblcms_course_group_publication_category
  SELECT NULL, CG.id, CG.forum_category_id FROM weblcms_course_group CG WHERE CG.forum_category_id > 0;


/** Learning Path Assigments **/

INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Core\\Repository\\ContentObject\\Assignment\\Integration\\Chamilo\\Core\\Repository\\ContentObject\\LearningPath', 'Chamilo\\Core\\Repository\\ContentObject\\Assignment\\Integration', NULL, 'Repository', '1', '1.0.0', '1');


CREATE TABLE `tracking_weblcms_learning_path_assignment_entry` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tree_node_attempt_id` int(10) UNSIGNED NOT NULL,
  `tree_node_data_id` int(10) UNSIGNED NOT NULL,
  `content_object_id` int(10) UNSIGNED NOT NULL,
  `entity_id` int(10) UNSIGNED NOT NULL,
  `entity_type` tinyint(2) UNSIGNED NOT NULL,
  `submitted` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `twlpae_tree_node_attempt_id` (`tree_node_attempt_id`),
  KEY `twlpae_tree_node_data_id` (`tree_node_data_id`),
  KEY `twlpae_content_object_id` (`content_object_id`),
  KEY `twlpae_entity` (`entity_id`, `entity_type`),
  KEY `twlpae_user_id` (`user_id`)
);

CREATE TABLE `tracking_weblcms_learning_path_assignment_feedback` ( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT , `entry_id` INT(10) UNSIGNED NOT NULL , `creation_date` INT(10) UNSIGNED NOT NULL , `modification_date` INT(10) UNSIGNED NOT NULL , `user_id` INT(10) UNSIGNED NOT NULL , `comment` TEXT NULL , PRIMARY KEY (`id`), KEY `twlpaf_entry_id` (`entry_id`)) ENGINE = InnoDB;
CREATE TABLE `tracking_weblcms_learning_path_assignment_note` ( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT , `entry_id` INT(10) UNSIGNED NOT NULL , `created` INT(10) UNSIGNED NOT NULL , `modified` INT(10) UNSIGNED NOT NULL , `user_id` INT(10) UNSIGNED NOT NULL , `note` TEXT NULL , PRIMARY KEY (`id`), KEY `twlpan_entry_id` (`entry_id`)) ENGINE = InnoDB;
CREATE TABLE `tracking_weblcms_learning_path_assignment_score` ( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT , `entry_id` INT(10) UNSIGNED NOT NULL , `created` INT(10) UNSIGNED NOT NULL , `modified` INT(10) UNSIGNED NOT NULL , `user_id` INT(10) UNSIGNED NOT NULL , `score` INT(10) UNSIGNED NOT NULL , PRIMARY KEY (`id`), KEY `twlpas_entry_id` (`entry_id`)) ENGINE = InnoDB;

CREATE TABLE `tracking_weblcms_assignment_entry` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content_object_publication_id` int(10) UNSIGNED NOT NULL,
  `content_object_id` int(10) UNSIGNED NOT NULL,
  `entity_id` int(10) UNSIGNED NOT NULL,
  `entity_type` tinyint(2) UNSIGNED NOT NULL,
  `submitted` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `twae_content_object_publication_id` (`content_object_publication_id`),
  KEY `twae_content_object_id` (`content_object_id`),
  KEY `twae_entity` (`entity_id`, `entity_type`),
  KEY `twae_user_id` (`user_id`)
);

CREATE TABLE `tracking_weblcms_assignment_feedback` ( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT , `entry_id` INT(10) UNSIGNED NOT NULL , `creation_date` INT(10) UNSIGNED NOT NULL , `modification_date` INT(10) UNSIGNED NOT NULL , `user_id` INT(10) UNSIGNED NOT NULL , `comment` TEXT NULL , PRIMARY KEY (`id`), KEY `twaf_entry_id` (`entry_id`)) ENGINE = InnoDB;
CREATE TABLE `tracking_weblcms_assignment_note` ( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT , `entry_id` INT(10) UNSIGNED NOT NULL , `created` INT(10) UNSIGNED NOT NULL , `modified` INT(10) UNSIGNED NOT NULL , `user_id` INT(10) UNSIGNED NOT NULL , `note` TEXT NULL , PRIMARY KEY (`id`), KEY `twan_entry_id` (`entry_id`)) ENGINE = InnoDB;
CREATE TABLE `tracking_weblcms_assignment_score` ( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT , `entry_id` INT(10) UNSIGNED NOT NULL , `created` INT(10) UNSIGNED NOT NULL , `modified` INT(10) UNSIGNED NOT NULL , `user_id` INT(10) UNSIGNED NOT NULL , `score` INT(10) UNSIGNED NOT NULL , PRIMARY KEY (`id`), KEY `twas_entry_id` (`entry_id`)) ENGINE = InnoDB;


/** Assignment tool refactoring **/

/** EXECUTE WHEN THE INDEXES OF ASSIGNMENT HAS NOT BEEN SET **/

/**ALTER TABLE `tracking_weblcms_assignment_entry` ADD INDEX( `entity_id`, `entity_type`);
ALTER TABLE `tracking_weblcms_assignment_entry` ADD INDEX( `content_object_publication_id`);
ALTER TABLE `tracking_weblcms_assignment_entry` ADD INDEX( `content_object_id`);
ALTER TABLE `tracking_weblcms_assignment_entry` ADD INDEX( `user_id`);

ALTER TABLE `tracking_weblcms_assignment_feedback` ADD INDEX( `user_id`);
ALTER TABLE `tracking_weblcms_assignment_note` ADD INDEX( `user_id`);
ALTER TABLE `tracking_weblcms_assignment_score` ADD INDEX( `user_id`);

ALTER TABLE `tracking_weblcms_assignment_feedback` ADD INDEX( `entry_id`);
ALTER TABLE `tracking_weblcms_assignment_note` ADD INDEX( `entry_id`);
ALTER TABLE `tracking_weblcms_assignment_score` ADD INDEX( `entry_id`);**/

CREATE TABLE `weblcms_assignment_publication` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `publication_id` int(10) UNSIGNED NOT NULL,
  `entity_type` int(3) UNSIGNED NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `wap_publication_id` (`publication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT IGNORE INTO weblcms_assignment_publication
SELECT NULL, COP.id, 0 FROM weblcms_content_object_publication COP
JOIN repository_content_object RCO on RCO.id = COP.content_object_id
WHERE COP.tool = 'Assignment' AND RCO.type LIKE '%Assignment%';

UPDATE weblcms_assignment_publication WAP
JOIN weblcms_content_object_publication COP on WAP.publication_id = COP.id
JOIN repository_assignment COA on COP.content_object_id = COA.id
SET WAP.entity_type = 1 WHERE COA.allow_group_submissions = 1;

ALTER TABLE `repository_assignment` DROP `allow_group_submissions`;

CREATE TABLE `tracking_weblcms_assignment_entry_attachment` ( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT , `entry_id` INT(10) UNSIGNED NOT NULL , `attachment_id` INT(10) UNSIGNED NOT NULL , PRIMARY KEY (`id`), KEY `twaea_attachment_id` (`attachment_id`), KEY `twaea_entry_id` (`entry_id`)) ENGINE = InnoDB;
CREATE TABLE `tracking_weblcms_learning_path_assignment_entry_attachment` ( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT , `entry_id` INT(10) UNSIGNED NOT NULL , `attachment_id` INT(10) UNSIGNED NOT NULL , PRIMARY KEY (`id`), KEY `twlpaea_attachment_id` (`attachment_id`), KEY `twlpaea_entry_id` (`entry_id`)) ENGINE = InnoDB;

INSERT INTO tracking_weblcms_assignment_entry
    SELECT id, publication_id, content_object_id, submitter_id, submitter_type, date_submitted, user_id, ip_address
    FROM tracking_weblcms_assignment_submission;

INSERT INTO tracking_weblcms_assignment_feedback
    SELECT NULL, SF.submission_id, SF.created, SF.modified, SF.user_id, CO.description
    FROM tracking_weblcms_submission_feedback SF
    JOIN repository_content_object CO on CO.id = SF.content_object_id
    WHERE CO.type NOT IN (
      'Chamilo\\Core\\Repository\\ContentObject\\File\\Storage\\DataClass\\File'
    );

INSERT INTO tracking_weblcms_assignment_entry_attachment
    SELECT NULL, SF.submission_id, SF.content_object_id
    FROM tracking_weblcms_submission_feedback SF
      JOIN repository_content_object CO on CO.id = SF.content_object_id
    WHERE CO.type IN (
      'Chamilo\\Core\\Repository\\ContentObject\\File\\Storage\\DataClass\\File'
    );

INSERT INTO tracking_weblcms_assignment_score
    SELECT NULL, submission_id, created, modified, user_id, score
    FROM tracking_weblcms_submission_score;

# INSERT INTO tracking_weblcms_assignment_feedback
#     SELECT NULL, submission_id, created, modified, user_id, note
#     FROM tracking_weblcms_submission_note;

DROP TABLE tracking_weblcms_assignment_note;
DROP TABLE tracking_weblcms_learning_path_assignment_note;

# DROP TABLE tracking_weblcms_assignment_submission;
# DROP TABLE tracking_weblcms_submission_feedback;
# DROP TABLE tracking_weblcms_submission_score;
# DROP TABLE tracking_weblcms_submission_note;

/** AUGUST 2018 **/

INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Core\\Repository\\Workspace\\Extension\\Office365', 'Chamilo\\Core\\Repository\\Workspace\\Extension', 'Extension', 'Office365', '1', '1.0.0', '1');

CREATE TABLE `repository_workspace_office365_reference` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `workspace_id` int(10) UNSIGNED NOT NULL,
  `office365_group_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `office365_plan_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `linked` TINYINT(1) NOT NULL,
  PRIMARY KEY (id),
  KEY `rwor.workspace_id` (`workspace_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `menu_item` ADD `icon_class` VARCHAR(255) NULL AFTER `type`;

INSERT INTO `configuration_setting` (`context`, `variable`, `value`, `user_setting`) VALUES ('Chamilo\\Core\\Repository\\ContentObject\\Assignment', 'show_compact_feedback', '1', '0');

INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Libraries\\Authentication\\Anonymous', 'Chamilo\\Libraries\\Authentication', 'Authentication', 'AnonymousAuthentication', '1', '1.0.0', '1');
INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Libraries\\Authentication\\Cas', 'Chamilo\\Libraries\\Authentication', 'Authentication', 'CasAuthentication', '1', '1.0.0', '1');
INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Libraries\\Authentication\\Ldap', 'Chamilo\\Libraries\\Authentication', 'Authentication', 'LdapAuthentication', '1', '1.0.0', '1');
INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Libraries\\Authentication\\Platform', 'Chamilo\\Libraries\\Authentication', 'Authentication', 'PlatformAuthentication', '1', '1.0.0', '1');
INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Libraries\\Authentication\\SecurityToken', 'Chamilo\\Libraries\\Authentication', 'Authentication', 'SecurityTokenAuthentication', '1', '1.0.0', '1');

ALTER TABLE `tracking_weblcms_learning_path_assignment_entry` ADD `content_object_publication_id` INT(10) NOT NULL AFTER `id`;
ALTER TABLE `tracking_weblcms_learning_path_assignment_entry` ADD INDEX(`content_object_publication_id`);

UPDATE tracking_weblcms_learning_path_assignment_entry LPAE
  JOIN tracking_weblcms_learning_path_tree_node_attempt TNA on LPAE.tree_node_attempt_id = TNA.id
SET LPAE.content_object_publication_id = TNA.publication_id;

INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Application\\Weblcms\\Bridge\\LearningPath\\Assignment', 'Chamilo\\Application\\Weblcms\\Bridge\\LearningPath', 'LearningPathBridge', 'AssignmentLearningPathBridge', '1', '1.0.0', '1');
INSERT INTO `configuration_registration` (`id`, `context`, `type`, `category`, `name`, `status`, `version`, `priority`) VALUES (NULL, 'Chamilo\\Application\\Weblcms\\Bridge\\Assignment', 'Chamilo\\Application\\Weblcms\\Bridge', 'Bridge', 'AssignmentBridge', '1', '1.0.0', '1');

ALTER TABLE `tracking_weblcms_assignment_entry_attachment` ADD `created` INT(10) UNSIGNED NOT NULL AFTER `attachment_id`;
ALTER TABLE `tracking_weblcms_learning_path_assignment_entry_attachment` ADD `created` INT(10) UNSIGNED NOT NULL AFTER `attachment_id`;