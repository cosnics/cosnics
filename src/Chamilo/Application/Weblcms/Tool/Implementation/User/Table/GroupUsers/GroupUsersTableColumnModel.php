<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Table\GroupUsers;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;

/**
 * Table to display a list of users subscribed to a group
 */
class GroupUsersTableColumnModel extends DataClassTableColumnModel
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_USERNAME));

        $showEmail = Configuration::getInstance()->get_setting(array('Chamilo\Core\User', 'show_email_addresses'));

        if($showEmail)
        {
            $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_EMAIL));
        }
    }
}
