<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass;

/**
 *
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Feedback extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
{
    public static function getTableName(): string
    {
        return 'tracking_weblcms_assignment_feedback';
    }
}
