<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionGroupsBrowser;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionBrowserTableColumnModel;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;

/**
 * Extends SubmissionBrowserTableColumnModel to include the group members column.
 * 
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class SubmissionGroupsBrowserTableColumnModel extends SubmissionBrowserTableColumnModel
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_group_name_column();
        $this->add_column(new TableColumn(Manager :: PROPERTY_GROUP_MEMBERS, null, false));
        
        parent :: initialize_columns();
    }

    /**
     * Adds a column for the group name
     */
    protected function add_group_name_column()
    {
        $this->add_column(new DataClassPropertyTableColumn(Group :: class_name(), Group :: PROPERTY_NAME));
    }
}
