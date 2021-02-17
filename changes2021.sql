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

