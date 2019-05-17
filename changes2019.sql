/** Plagiarism PART 2**/
ALTER TABLE `weblcms_assignment_publication` ADD `check_for_plagiarism` INT(3) UNSIGNED NULL DEFAULT '0' AFTER `entity_type`;

/** If all should fail **/
INSERT INTO weblcms_assignment_publication (
  SELECT NULL, PUB.id, 0, 0 FROM weblcms_content_object_publication PUB
                                   LEFT JOIN weblcms_assignment_publication ASSPUB on ASSPUB.publication_id = PUB.id
  WHERE PUB.tool = 'Assignment' AND ASSPUB.id IS NULL);
/** endif **/

INSERT INTO `configuration_setting` (`id`, `context`, `variable`, `value`, `user_setting`) VALUES (NULL, 'Chamilo\\Core\\Menu', 'favicon', NULL, '0');