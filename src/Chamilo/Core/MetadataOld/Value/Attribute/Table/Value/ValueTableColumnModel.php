<?php
namespace Chamilo\Core\MetadataOld\Value\Attribute\Table\Value;

use Chamilo\Core\MetadataOld\Value\Attribute\Storage\DataClass\DefaultAttributeValue;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Table column model for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ValueTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(
                DefaultAttributeValue :: class_name(),
                DefaultAttributeValue :: PROPERTY_VALUE,
                Translation :: get('Value', null, 'Chamilo\Core\MetadataOld\Value')));
    }
}