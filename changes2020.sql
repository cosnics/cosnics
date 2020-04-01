ALTER TABLE `repository_assignment` ADD `page_template` TEXT NULL AFTER `allowed_types`, ADD `last_entry_as_template` TINYINT(3) NOT NULL DEFAULT '0' AFTER `page_template`;
ALTER TABLE `weblcms_platform_group_team` ADD `active` tinyint(3) NOT NULL DEFAULT '1'
