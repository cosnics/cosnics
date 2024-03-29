ALTER TABLE `repository_rubric_level` ADD COLUMN `criterium_id` int(11) DEFAULT NULL;
ALTER TABLE `repository_rubric_result` ADD COLUMN `level_id` int(11) DEFAULT NULL;
ALTER TABLE `repository_rubric_result` CHANGE `score` `score` INT(11) NULL;
ALTER TABLE `repository_rubric_level` ADD `use_range_score` tinyint(1) NOT NULL DEFAULT 0 AFTER `score`;
ALTER TABLE `repository_rubric_level` ADD `minimum_score` int(11) DEFAULT NULL AFTER `use_range_score`;

ALTER TABLE `repository_presence` ADD `global_self_registration_disabled` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `repository_presence_result_period` ADD `period_self_registration_disabled` tinyint(1) NOT NULL DEFAULT '0';
