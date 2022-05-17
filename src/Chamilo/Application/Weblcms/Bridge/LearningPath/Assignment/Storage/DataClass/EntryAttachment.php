<?php
namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass;

/**
 *
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryAttachment extends
    \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment
{
    public static function getTableName(): string
    {
        return 'tracking_weblcms_learning_path_assignment_entry_attachment';
    }
}
