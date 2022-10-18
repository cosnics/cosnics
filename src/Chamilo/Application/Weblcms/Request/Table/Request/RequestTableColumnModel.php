<?php
namespace Chamilo\Application\Weblcms\Request\Table\Request;

use Chamilo\Application\Weblcms\Request\Storage\DataClass\Request;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

class RequestTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_CREATION_DATE));
        
        if ($this->get_component()->get_table_type() != RequestTable::TYPE_PERSONAL)
        {
            $this->addColumn(new StaticTableColumn(Translation::get('User')));
        }
        
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_NAME));
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_SUBJECT));
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_MOTIVATION));
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_CATEGORY_ID));
        
        if ($this->get_component()->get_table_type() == RequestTable::TYPE_PERSONAL)
        {
            $this->addColumn(
                new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_DECISION, false));
        }
    }
}