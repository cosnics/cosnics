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
  `use_scores` tinyint(1) NOT NULL,
  `use_feedback` tinyint(1) NOT NULL,
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