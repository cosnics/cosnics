<?php
namespace Chamilo\Configuration\Form\Table\Element;

use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for the schema
 * 
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Element :: class_name(), Element :: PROPERTY_TYPE));
        $this->add_column(new DataClassPropertyTableColumn(Element :: class_name(), Element :: PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Element :: class_name(), Element :: PROPERTY_REQUIRED));
    }
}