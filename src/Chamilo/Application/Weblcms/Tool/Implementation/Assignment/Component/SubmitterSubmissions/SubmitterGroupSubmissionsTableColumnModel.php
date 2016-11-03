<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions;

use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;

/**
 * Defines the columns for the submitter group submissions browser table.
 * 
 * @author Anthony hurst (Hogeschool Gent)
 */
class SubmitterGroupSubmissionsTableColumnModel extends SubmitterUserSubmissionsTableColumnModel
{

    public function initialize_columns()
    {
        $this->add_column(
            new StaticTableColumn(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_SUBMITTER_ID, 
                false));
        parent :: initialize_columns();
    }
}
