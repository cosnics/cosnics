<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Table\Entity;

use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

class EntityTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        $this->addColumn(new StaticTableColumn(Translation::get('Type')));
        $this->addColumn(new StaticTableColumn(Translation::get('Entity')));
        $this->addColumn(new StaticTableColumn(Translation::get('Group')));
        $this->addColumn(new StaticTableColumn(Translation::get('Path')));
    }
}