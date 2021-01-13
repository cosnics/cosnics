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
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


CREATE TABLE `user_invite`
(
    `id`                 INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`            INT(10) UNSIGNED NOT NULL,
    `invited_by_user_id` INT(10) UNSIGNED NOT NULL,
    `valid_until`        INT(10) UNSIGNED NOT NULL,
    `secret_key`         VARCHAR(100)     NOT NULL,
    `status`             INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    INDEX (`user_id`),
    INDEX (`secret_key`),
    INDEX (`invited_by_user_id`)
) ENGINE = InnoDB;

CREATE TABLE `weblcms_platform_group_team`
(
    `id`        int(10) UNSIGNED                    NOT NULL AUTO_INCREMENT,
    `course_id` int(10) UNSIGNED                    NOT NULL,
    `team_id`   varchar(47) COLLATE utf8_unicode_ci NOT NULL,
    `name`      varchar(47) COLLATE utf8_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `wpgt_course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `weblcms_platform_group_team_relation`
(
    `id`                     int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `platform_group_team_id` int(10) UNSIGNED NOT NULL,
    `group_id`               int(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `wpgtr_platform_group_team_id` (`platform_group_team_id`),
    KEY `wpgtr_group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tracking_weblcms_lp_attempt_rel_assignment_entry`
(
    `id`                       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tree_node_attempt_id` INT(10)          NOT NULL,
    `entry_id`                 INT(10)          NOT NULL,
    PRIMARY KEY (`id`),
    INDEX (`tree_node_attempt_id`),
    INDEX (`entry_id`)
) ENGINE = InnoDB;

ALTER TABLE `tracking_weblcms_assignment_feedback` ADD `feedback_content_object_id` INT(10) UNSIGNED NOT NULL AFTER `comment`, ADD INDEX (`feedback_content_object_id`);
ALTER TABLE `tracking_weblcms_learning_path_assignment_feedback` ADD `feedback_content_object_id` INT(10) UNSIGNED NOT NULL AFTER `comment`, ADD INDEX (`feedback_content_object_id`);
ALTER TABLE `portfolio_feedback` ADD `feedback_content_object_id` INT(10) UNSIGNED NOT NULL AFTER `comment`, ADD INDEX (`feedback_content_object_id`);
ALTER TABLE `repository_wiki_page_feedback` ADD `feedback_content_object_id` INT(10) UNSIGNED NOT NULL AFTER `comment`, ADD INDEX (`feedback_content_object_id`);
ALTER TABLE `weblcms_feedback` ADD `feedback_content_object_id` INT(10) UNSIGNED NOT NULL AFTER `comment`, ADD INDEX (`feedback_content_object_id`);

ALTER TABLE `repository_content_object`
    ADD `feedback_id` INT(10) UNSIGNED NOT NULL AFTER `content_hash`,
    ADD INDEX (`feedback_id`);


/** CHANGE 47 TO TEMPLATE ID FOR FEEDBACK CONTENT OBJECT **/
/** tracking_weblcms_assignment_feedback **/
INSERT INTO `repository_content_object`
    (SELECT NULL, FB.user_id, UUID(), 0, 47, FB.creation_date, FB.modification_date, 1, 1, NULL, 'Chamilo\\Core\\Repository\\ContentObject\\Feedback\\Storage\\DataClass\\Feedback', 'Feedback',
            FB.comment, NULL, FB.id FROM `tracking_weblcms_assignment_feedback` FB);

UPDATE `tracking_weblcms_assignment_feedback` FB
JOIN `repository_content_object` CO on CO.feedback_id = FB.id
SET FB.feedback_content_object_id = CO.id;

UPDATE `repository_content_object` SET feedback_id = 0 WHERE feedback_id > 0;

/** tracking_weblcms_learning_path_assignment_feedback **/
INSERT INTO `repository_content_object`
    (SELECT NULL, FB.user_id, UUID(), 0, 47, FB.creation_date, FB.modification_date, 1, 1, NULL, 'Chamilo\\Core\\Repository\\ContentObject\\Feedback\\Storage\\DataClass\\Feedback', 'Feedback',
            FB.comment, NULL, FB.id FROM `tracking_weblcms_learning_path_assignment_feedback` FB);

UPDATE `tracking_weblcms_learning_path_assignment_feedback` FB
    JOIN `repository_content_object` CO on CO.feedback_id = FB.id
SET FB.feedback_content_object_id = CO.id;

UPDATE `repository_content_object` SET feedback_id = 0 WHERE feedback_id > 0;

/** portfolio_feedback **/
INSERT INTO `repository_content_object`
    (SELECT NULL, FB.user_id, UUID(), 0, 47, FB.creation_date, FB.modification_date, 1, 1, NULL, 'Chamilo\\Core\\Repository\\ContentObject\\Feedback\\Storage\\DataClass\\Feedback', 'Feedback',
            FB.comment, NULL, FB.id FROM `portfolio_feedback` FB);

UPDATE `portfolio_feedback` FB
    JOIN `repository_content_object` CO on CO.feedback_id = FB.id
SET FB.feedback_content_object_id = CO.id;

UPDATE `repository_content_object` SET feedback_id = 0 WHERE feedback_id > 0;

/** repository_wiki_page_feedback **/
INSERT INTO `repository_content_object`
    (SELECT NULL, FB.user_id, UUID(), 0, 47, FB.creation_date, FB.modification_date, 1, 1, NULL, 'Chamilo\\Core\\Repository\\ContentObject\\Feedback\\Storage\\DataClass\\Feedback', 'Feedback',
            FB.comment, NULL, FB.id FROM `repository_wiki_page_feedback` FB);

UPDATE `repository_wiki_page_feedback` FB
    JOIN `repository_content_object` CO on CO.feedback_id = FB.id
SET FB.feedback_content_object_id = CO.id;

UPDATE `repository_content_object` SET feedback_id = 0 WHERE feedback_id > 0;

/** weblcms_feedback **/
INSERT INTO `repository_content_object`
    (SELECT NULL, FB.user_id, UUID(), 0, 47, FB.creation_date, FB.modification_date, 1, 1, NULL, 'Chamilo\\Core\\Repository\\ContentObject\\Feedback\\Storage\\DataClass\\Feedback', 'Feedback',
            FB.comment, NULL, FB.id FROM `weblcms_feedback` FB);

UPDATE `weblcms_feedback` FB
    JOIN `repository_content_object` CO on CO.feedback_id = FB.id
SET FB.feedback_content_object_id = CO.id;

UPDATE `repository_content_object` SET feedback_id = 0 WHERE feedback_id > 0;

ALTER TABLE repository_content_object DROP feedback_id;

/** run php console chamilo:repository:feedback_migration **/
