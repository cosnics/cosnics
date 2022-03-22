ALTER TABLE `repository_rubric_level` ADD COLUMN `criterium_id` int(11) DEFAULT NULL;
ALTER TABLE `repository_rubric_result` ADD COLUMN `level_id` int(11) DEFAULT NULL;
ALTER TABLE `repository_rubric_result` CHANGE `score` `score` INT(11) NULL;