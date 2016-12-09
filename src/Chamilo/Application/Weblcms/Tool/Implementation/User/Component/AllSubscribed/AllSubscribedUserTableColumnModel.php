<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\AllSubscribed;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for an all course user browser table.
 * 
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 */
class AllSubscribedUserTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const SUBSCRIPTION_STATUS = 'subscription_status';
    const SUBSCRIPTION_TYPE = 'subscription_type';
    const DEFAULT_ORDER_COLUMN_INDEX = 1;

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_OFFICIAL_CODE));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_USERNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_EMAIL));
        $this->add_column(new SortableStaticTableColumn(self::SUBSCRIPTION_STATUS));
        $this->add_column(new StaticTableColumn(self::SUBSCRIPTION_TYPE));
    }
}
