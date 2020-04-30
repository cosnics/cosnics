<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\DirectSubscribedPlatformGroupBrowser;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for a direct subscribed course group browser table.
 * 
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 */
class DirectSubscribedPlatformGroupBrowserTableColumnModel extends RecordTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_CODE));
        $this->add_column(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_DESCRIPTION));
        
        $this->add_column(
            new DataClassPropertyTableColumn(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_STATUS));
    }
}
