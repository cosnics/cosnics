CREATE TABLE `repository_rubric_result_target_user` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `rubric_result_id` CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
  `target_user_id` INT NOT NULL,
  PRIMARY KEY(`id`)
) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;

ALTER TABLE `repository_rubric_result` CHANGE `target_user_id` `target_user_id` INT(11) NULL;

ALTER TABLE `weblcms_exam_assignment_publication` ADD COLUMN `feedback_from_date` int(10) UNSIGNED NOT NULL, ADD COLUMN `feedback_to_date` int(10) UNSIGNED NOT NULL;


CREATE TABLE `repository_learning_path_step_context` (
 `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `learning_path_step_id` int(10) UNSIGNED NOT NULL,
 `context_class` VARCHAR(255) NOT NULL,
 `context_id` int(10) UNSIGNED NOT NULL,
 PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `repository_rubric_data` ADD COLUMN `use_relative_weights` tinyint(1) NOT NULL;

ALTER TABLE `repository_rubric_tree_node` ADD COLUMN `rel_weight` int(11) DEFAULT NULL;
