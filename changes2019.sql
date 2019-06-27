/** Plagiarism PART 2**/
ALTER TABLE `weblcms_assignment_publication`
    ADD `check_for_plagiarism` INT(3) UNSIGNED NULL DEFAULT '0' AFTER `entity_type`;

/** If all should fail **/
INSERT INTO weblcms_assignment_publication (
    SELECT NULL, PUB.id, 0, 0
    FROM weblcms_content_object_publication PUB
             LEFT JOIN weblcms_assignment_publication ASSPUB on ASSPUB.publication_id = PUB.id
    WHERE PUB.tool = 'Assignment'
      AND ASSPUB.id IS NULL);
/** endif **/

INSERT INTO `configuration_setting` (`id`, `context`, `variable`, `value`, `user_setting`)
VALUES (NULL, 'Chamilo\\Core\\Menu', 'favicon', NULL, '0');

CREATE TABLE `tracking_user_admin_user_visit`
(
    `id`            int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_user_id` int(10) UNSIGNED NOT NULL,
    `user_visit_id` int(10) UNSIGNED NOT NULL,
    `visit_date`    int(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `admin_user_id` (`admin_user_id`, `user_visit_id`, `visit_date`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_invite` (
`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
 `user_id` INT(10) UNSIGNED NOT NULL ,
 `invited_by_user_id` INT(10) UNSIGNED NOT NULL ,
 `valid_until` INT(10) UNSIGNED NOT NULL ,
 `secret_key` VARCHAR(100) NOT NULL ,
 PRIMARY KEY (`id`),
 INDEX (`user_id`),
 INDEX (`secret_key`)
) ENGINE = InnoDB;