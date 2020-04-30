<?php
namespace Chamilo\Core\Metadata\Schema\Table\Schema;

use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for the schema
 * 
 * @package Chamilo\Core\Metadata\Schema\Table\Schema
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SchemaTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Schema::class, Schema::PROPERTY_NAMESPACE));
        $this->add_column(new DataClassPropertyTableColumn(Schema::class, Schema::PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Schema::class, Schema::PROPERTY_URL));
    }
}