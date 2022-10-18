<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Unsubscribed;

use Chamilo\Configuration\Configuration;
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
    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_USERNAME));

        $showEmail = Configuration::getInstance()->get_setting(array('Chamilo\Core\User', 'show_email_addresses'));

        if($showEmail)
        {
            $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_EMAIL));
        }

        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_STATUS));
    }
}
