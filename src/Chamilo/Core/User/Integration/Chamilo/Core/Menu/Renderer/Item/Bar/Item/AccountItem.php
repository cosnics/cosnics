<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\MenuItem;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AccountItem extends MenuItem
{

    public function isItemSelected()
    {
        $currentContext = $this->getMenuRenderer()->getRequest()->get(Application :: PARAM_CONTEXT);
        $currentAction = $this->getMenuRenderer()->getRequest()->get(Manager :: PARAM_ACTION);
        return ($currentContext == Manager :: package() && $currentAction == Manager :: ACTION_VIEW_ACCOUNT);
    }

    public function get_url()
    {
        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => Manager :: context(),
                Application :: PARAM_ACTION => Manager :: ACTION_VIEW_ACCOUNT));
        return $redirect->getUrl();
    }
}
