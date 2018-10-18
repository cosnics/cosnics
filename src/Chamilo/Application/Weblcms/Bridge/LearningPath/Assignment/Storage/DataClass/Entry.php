<?php
namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass;

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