<?php
namespace Chamilo\Core\MetadataOld\Element\Table\Entity;

use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\Rights\Editor\Table\LocationEntity\LocationEntityTableColumnModel;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

/**
 * Cell renderer for the user entity browser
 * 
 * @author Sven Vanpoucke
 */
class ElementEntityTableColumnModel extends LocationEntityTableColumnModel
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Element :: CLASS_NAME, Element :: PROPERTY_NAME));
        parent :: initialize_columns();
    }
}
