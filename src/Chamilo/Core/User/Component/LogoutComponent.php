<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package user.lib.user_manager.component
 * @author Sven Vanpoucke
 */
class LogoutComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        \Chamilo\Core\User\Storage\DataManager :: logout();
        Event :: trigger('logout', Manager :: context(), array('server' => $_SERVER, 'user' => $this->get_user()));
        
        Redirect :: link(array(), array(self :: PARAM_CONTEXT));
    }
}
