<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass;

/**
 *
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryPlagiarismResult extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult
{
    public static function get_table_name()
    {
        return 'tracking_weblcms_assignment_entry_plagiarism_result';
    }
}
