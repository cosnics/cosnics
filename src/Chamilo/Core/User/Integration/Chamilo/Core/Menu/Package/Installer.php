<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Package;

use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass\AccountItem;
use Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass\LogoutItem;
use Chamilo\Core\Menu\Storage\DataClass\Item;

class Installer extends \Chamilo\Core\Menu\Action\Installer
{

    /**
     *
     * @param string[] $formValues
     */
    public function __construct($formValues)
    {
        parent :: __construct($formValues, Item :: DISPLAY_ICON, false);
    }

    public function extra()
    {
        $my_account = new AccountItem();
        $my_account->set_display($this->getItemDisplay());

        if (! $my_account->create())
        {
            return false;
        }
        else
        {
            $item_title = new ItemTitle();
            $item_title->set_title(Translation :: get('MyAccount'));
            $item_title->set_isocode(Translation :: get_instance()->get_language());
            $item_title->set_item_id($my_account->get_id());
            if (! $item_title->create())
            {
                return false;
            }
        }

        $logout = new LogoutItem();
        $logout->set_display($this->getItemDisplay());

        if (! $logout->create())
        {
            return false;
        }
        else
        {
            $item_title = new ItemTitle();
            $item_title->set_title(Translation :: get('Logout'));
            $item_title->set_isocode(Translation :: get_instance()->get_language());
            $item_title->set_item_id($logout->get_id());
            if (! $item_title->create())
            {
                return false;
            }
        }

        return true;
    }
}
