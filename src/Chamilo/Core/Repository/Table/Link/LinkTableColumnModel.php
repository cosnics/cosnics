<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class LinkTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initializeColumns()
    {
        $columns = [];
        
        if ($this->getTable()->getType() == LinkTable::TYPE_PUBLICATIONS)
        {
            $this->addColumn(
                new DataClassPropertyTableColumn(Attributes::class, Attributes::PROPERTY_APPLICATION));
            $this->addColumn(
                new DataClassPropertyTableColumn(Attributes::class, Attributes::PROPERTY_LOCATION));
            $this->addColumn(new DataClassPropertyTableColumn(Attributes::class, Attributes::PROPERTY_DATE));
        }
        else
        {
            $this->addColumn(
                new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TYPE, null, false));
            $this->addColumn(
                new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE, null, false));
            $this->addColumn(
                new DataClassPropertyTableColumn(
                    ContentObject::class,
                    ContentObject::PROPERTY_DESCRIPTION, 
                    null, 
                    false));
        }
    }
}
