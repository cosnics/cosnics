<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class LinkTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $columns = array();
        
        if ($this->get_table()->get_type() == LinkTable::TYPE_PUBLICATIONS)
        {
            $this->add_column(
                new DataClassPropertyTableColumn(Attributes::class_name(), Attributes::PROPERTY_APPLICATION));
            $this->add_column(
                new DataClassPropertyTableColumn(Attributes::class_name(), Attributes::PROPERTY_LOCATION));
            $this->add_column(new DataClassPropertyTableColumn(Attributes::class_name(), Attributes::PROPERTY_DATE));
        }
        else
        {
            $this->add_column(
                new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TYPE, null, false));
            $this->add_column(
                new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE, null, false));
            $this->add_column(
                new DataClassPropertyTableColumn(
                    ContentObject::class_name(), 
                    ContentObject::PROPERTY_DESCRIPTION, 
                    null, 
                    false));
        }
    }
}
