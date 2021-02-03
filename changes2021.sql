CREATE TABLE `repository_rubric_result_target_user` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `rubric_result_id` CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
  `target_user_id` INT NOT NULL,
  PRIMARY KEY(`id`)
) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;

ALTER TABLE `repository_rubric_result` CHANGE `target_user_id` `target_user_id` INT(11) NULL;

ALTER TABLE `weblcms_exam_assignment_publication` ADD COLUMN `feedback_from_date` int(10) UNSIGNED NOT NULL, ADD COLUMN `feedback_to_date` int(10) UNSIGNED NOT NULL;
