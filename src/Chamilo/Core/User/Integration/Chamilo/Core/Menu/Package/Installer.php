<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Package;

use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass\WidgetItem;

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
        $my_account = new WidgetItem();
        $my_account->set_display($this->getItemDisplay());

        if (! $my_account->create())
        {
            return false;
        }
        else
        {
            $item_title = new ItemTitle();
            $item_title->set_title(Translation :: get('MyAccount'));
            $item_title->set_isocode(Translation :: getInstance()->getLanguageIsocode());
            $item_title->set_item_id($my_account->get_id());
            if (! $item_title->create())
            {
                return false;
            }
        }

        return true;
    }
}
