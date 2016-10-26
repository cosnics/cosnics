<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\User;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const COLUMN_PREFIX = 'prefix';

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_EMAIL));
    }
}