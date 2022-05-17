<?php
namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Feedback extends
    \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Feedback
{
    public static function getTableName(): string
    {
        return 'tracking_weblcms_learning_path_assignment_feedback';
    }
}
