CREATE TABLE `repository_rubric_result_target_user` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `rubric_result_id` CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
  `target_user_id` INT NOT NULL,
  PRIMARY KEY(`id`)
) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;

ALTER TABLE `repository_rubric_result` CHANGE `target_user_id` `target_user_id` INT(11) NULL;

ALTER TABLE `weblcms_exam_assignment_publication` ADD COLUMN `feedback_from_date` int(10) UNSIGNED NOT NULL, ADD COLUMN `feedback_to_date` int(10) UNSIGNED NOT NULL;

CREATE TABLE `repository_evaluation` (
  `id` int(10) NOT NULL,
  `rubric_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `repository_evaluation` ADD PRIMARY KEY (`id`);

CREATE TABLE `weblcms_evaluation_publication` (
  `id` int(10) UNSIGNED NOT NULL,
  `publication_id` int(10) UNSIGNED NOT NULL,
  `entity_type` int(3) UNSIGNED NOT NULL,
  `release_scores` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `weblcms_evaluation_publication`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wap_publication_id` (`publication_id`);

ALTER TABLE `weblcms_evaluation_publication`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
  
/*

CREATE TABLE `repository_evaluation_entry` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `evaluation_id` int(10) UNSIGNED NOT NULL,
  `context_class` VARCHAR(255) NOT NULL,
  `context_id` int(10) UNSIGNED NOT NULL,
  `entity_type` int(3) UNSIGNED NOT NULL,
  `entity_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `repository_evaluation_entry_score` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `evaluator_id` int(10) UNSIGNED NOT NULL,
  `score` VARCHAR(20) NOT NULL DEFAULT '',
  `is_absent` tinyint(1) NOT NULL DEFAULT 0,
  `created_time` int(10) UNSIGNED NOT NULL,
  `entry_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `repository_evaluation_entry_score_target_user` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `score_id` int(10) UNSIGNED NOT NULL,
  `target_user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `repository_evaluation_entry_feedback` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entry_id` int(10) UNSIGNED NOT NULL,
  `creation_date` int(10) UNSIGNED NOT NULL,
  `modification_date` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `feedback_content_object_id` int(10) UNSIGNED NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
  
*/

