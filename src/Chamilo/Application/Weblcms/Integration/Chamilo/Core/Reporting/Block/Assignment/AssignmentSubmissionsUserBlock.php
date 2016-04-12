<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Libraries\Platform\Translation;

/**
 * Description of weblcms_assignment_submissions_group_reporting_block
 * 
 * @author Anthony Hurst (Hogeschool Gent)
 */
class AssignmentSubmissionsUserBlock extends AssignmentSubmissionsBlock
{

    /**
     * Defines the title of the submitter name column.
     * 
     * @return string The title of the submitter name column.
     */
    protected function define_column_submitter_name_title()
    {
        return Translation :: get('User');
    }
}
