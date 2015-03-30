<?php
namespace Chamilo\Core\Metadata\ControlledVocabulary\Table\ControlledVocabulary;

use Chamilo\Core\Metadata\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for the controlled vocabulary
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ControlledVocabularyTableColumnModel extends DataClassTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(
                ControlledVocabulary :: class_name(), 
                ControlledVocabulary :: PROPERTY_VALUE));
    }
}