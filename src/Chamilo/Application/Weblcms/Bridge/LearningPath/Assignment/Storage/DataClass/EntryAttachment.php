<?php
namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass;

/**
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryAttachment extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
{
    public static function get_table_name()
    {
        return 'tracking_weblcms_learning_path_assignment_entry_attachment';
    }
}
