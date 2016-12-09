<?php
namespace Chamilo\Core\Repository\Quota\Rights\Table\Entity;

use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

class EntityTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new StaticTableColumn(Translation::get('Type')));
        $this->add_column(new StaticTableColumn(Translation::get('Entity')));
        $this->add_column(new StaticTableColumn(Translation::get('Group')));
        $this->add_column(new StaticTableColumn(Translation::get('Path')));
    }
}
