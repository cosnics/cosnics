<?php
namespace Chamilo\Core\Repository\Quota\Table\Request;

use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

class RequestTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Request :: class_name(), Request :: PROPERTY_CREATION_DATE));

        if ($this->get_component()->get_table_type() != RequestTable :: TYPE_PERSONAL)
        {
            $this->add_column(new StaticTableColumn(Translation :: get('User')));
        }

        $this->add_column(new DataClassPropertyTableColumn(Request :: class_name(), Request :: PROPERTY_QUOTA));
        $this->add_column(new DataClassPropertyTableColumn(Request :: class_name(), Request :: PROPERTY_MOTIVATION));

        if ($this->get_component()->get_table_type() == RequestTable :: TYPE_PENDING)
        {
            $this->add_column(new StaticTableColumn(Translation :: get('CurrentlyUsedDiskSpace')));
        }

        if ($this->get_component()->get_table_type() != RequestTable :: TYPE_PERSONAL)
        {
            $this->add_column(new StaticTableColumn(Translation :: get('MaximumUsedDiskSpace')));
        }

        if ($this->get_component()->get_table_type() == RequestTable :: TYPE_PERSONAL)
        {
            $this->add_column(
                new DataClassPropertyTableColumn(Request :: class_name(), Request :: PROPERTY_DECISION, false));
        }
    }
}
