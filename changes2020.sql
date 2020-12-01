ALTER TABLE `repository_assignment` ADD `page_template` TEXT NULL AFTER `allowed_types`, ADD `last_entry_as_template` TINYINT(3) NOT NULL DEFAULT '0' AFTER `page_template`;
ALTER TABLE `weblcms_platform_group_team` ADD `active` tinyint(3) NOT NULL DEFAULT '1'
ALTER TABLE `weblcms_course_team_relation` ADD `active` tinyint(3) NOT NULL DEFAULT '1'

CREATE TABLE `repository_assignment_rubric` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_id` int(11) NOT NULL,
  `rubric_id` int(11) NOT NULL,
  `self_evaluation_allowed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `weblcms_exam_assignment_user_overtime` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`publication_id` int(10) UNSIGNED NOT NULL,
`user_id` int(10) UNSIGNED NOT NULL,
`extra_time` int(10) UNSIGNED NOT NULL,
PRIMARY KEY (`id`),
INDEX (`publication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
