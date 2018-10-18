<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Entry extends \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry
{
    public static function get_table_name()
    {
        return 'tracking_weblcms_learning_path_assignment_entry';
    }
}