<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;

/**
 * Description of student_submissions_browser_own_groups_table_column_model
 * 
 * @author Anthony Hurst (Hogeschool Gent)
 */
class StudentSubmissionsOwnGroupsTableColumnModel extends SubmitterUserSubmissionsTableColumnModel
{

    public function initialize_columns()
    {
        $this->add_column(
            new StaticTableColumn(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_SUBMITTER_ID));
        $this->add_column(new StaticTableColumn(Manager :: PROPERTY_NAME));
        $this->add_column(new StaticTableColumn(Manager :: PROPERTY_GROUP_MEMBERS));
        parent :: initialize_columns();
    }
}
