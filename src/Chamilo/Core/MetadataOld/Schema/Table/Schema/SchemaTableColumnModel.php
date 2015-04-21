<?php
namespace Chamilo\Core\MetadataOld\Schema\Table\Schema;

use Chamilo\Core\MetadataOld\Schema\Storage\DataClass\Schema;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SchemaTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Schema :: class_name(), Schema :: PROPERTY_NAMESPACE));
        $this->add_column(new DataClassPropertyTableColumn(Schema :: class_name(), Schema :: PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Schema :: class_name(), Schema :: PROPERTY_URL));
    }
}