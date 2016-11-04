<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Unsubscribed;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * * *************************************************************************** Table column model for an unsubscribed
 * course user browser table.
 * 
 * @author Stijn Van Hoecke ****************************************************************************
 */
class UnsubscribedUserTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const DEFAULT_ORDER_COLUMN_INDEX = 1;
    
    // **************************************************************************
    // CONSTRUCTOR
    // **************************************************************************
    /**
     * Constructor
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_USERNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_EMAIL));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_STATUS));
    }
}
