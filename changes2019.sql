/** Plagiarism PART 2**/
ALTER TABLE `weblcms_assignment_publication` ADD `check_for_plagiarism` INT(3) UNSIGNED NULL DEFAULT '0' AFTER `entity_type`;

INSERT INTO `configuration_setting` (`id`, `context`, `variable`, `value`, `user_setting`) VALUES (NULL, 'Chamilo\\Core\\Menu', 'favicon', NULL, '0');