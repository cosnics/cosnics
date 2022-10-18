<?php
namespace Chamilo\Core\Admin\Table\WhoisOnline;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;

/**
 *
 * @package admin.lib.admin_manager.component.whois_online_table
 */
/**
 * Table column model for the user browser table
 */
class WhoisOnlineTableColumnModel extends DataClassTableColumnModel
{

    /**
     * Constructor
     */
    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));

        $showEmail = Configuration::getInstance()->get_setting(array('Chamilo\Core\User', 'show_email_addresses'));

        if($showEmail)
        {
            $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_EMAIL));
        }

        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_STATUS));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_PICTURE_URI));
    }
}
