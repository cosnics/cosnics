ALTER TABLE `repository_rubric_level` ADD COLUMN `criterium_id` int(11) DEFAULT NULL;
ALTER TABLE `repository_rubric_result` ADD COLUMN `level_id` int(11) DEFAULT NULL;
ALTER TABLE `repository_rubric_result` CHANGE `score` `score` INT(11) NULL;

CREATE TABLE `repository_gradebook` (
    `id` int(10) NOT NULL,
    `active_gradebook_data_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `repository_gradebook` ADD PRIMARY KEY (`id`);