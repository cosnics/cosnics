<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\Item;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Portfolio item table column model
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE));
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_CREATION_DATE));
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE));
    }
}