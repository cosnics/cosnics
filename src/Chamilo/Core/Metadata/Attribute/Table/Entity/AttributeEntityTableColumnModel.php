<?php
namespace Chamilo\Core\Metadata\Attribute\Table\Entity;

use Chamilo\Core\Metadata\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\Rights\Editor\Table\LocationEntity\LocationEntityTableColumnModel;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

/**
 * Cell renderer for the user entity browser
 * 
 * @author Sven Vanpoucke
 */
class AttributeEntityTableColumnModel extends LocationEntityTableColumnModel
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Attribute :: class_name(), Attribute :: PROPERTY_NAME));
        parent :: initialize_columns();
    }
}
