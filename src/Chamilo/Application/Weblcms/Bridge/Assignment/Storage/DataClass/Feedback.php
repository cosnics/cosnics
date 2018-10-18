<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass;

/**
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Feedback extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
{
    public static function get_table_name()
    {
        return 'tracking_weblcms_assignment_feedback';
    }
}
