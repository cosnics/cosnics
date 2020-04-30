<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\AllSubscribedUserBrowser;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for an all course user browser table.
 * 
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 */
class AllSubscribedUserBrowserTableColumnModel extends RecordTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{
    const SUBSCRIPTION_STATUS = 'subscription_status';
    const SUBSCRIPTION_TYPE = 'subscription_type';
    const DEFAULT_ORDER_COLUMN_INDEX = 1;

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_USERNAME));

        $showEmail = Configuration::getInstance()->get_setting(array('Chamilo\Core\User', 'show_email_addresses'));

        if($showEmail)
        {
            $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_EMAIL));
        }

        $this->add_column(new SortableStaticTableColumn(self::SUBSCRIPTION_STATUS));
        $this->add_column(new StaticTableColumn(self::SUBSCRIPTION_TYPE));
    }
}
