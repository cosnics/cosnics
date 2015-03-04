<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\MenuItem;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LogoutItem extends MenuItem
{

    /**
     *
     * @return string
     */
    public function get_url()
    {
        return Redirect :: get_link(
            array(
                Application :: PARAM_CONTEXT => Manager :: context(),
                Application :: PARAM_ACTION => Manager :: ACTION_LOGOUT));
    }
}
