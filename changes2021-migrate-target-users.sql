CREATE TABLE `repository_rubric_result_target_user` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `rubric_result_id` CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
  `target_user_id` INT NOT NULL,
  PRIMARY KEY(`id`)
) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;

ALTER TABLE `repository_rubric_result` DROP `target_user_id`;
