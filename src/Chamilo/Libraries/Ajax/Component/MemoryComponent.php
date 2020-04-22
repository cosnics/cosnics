<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MemoryComponent extends Manager
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $request = $this->getRequest();
        $variable = $request->request->get('variable');
        $value = $request->request->get('value');
        $action = $request->request->get('action');

        switch ($action)
        {
            case 'set' :
                $_SESSION[$variable] = $value;
                break;
            case 'clear' :
                unset($_SESSION[$variable]);
                break;
            case 'get' :
            default :
                echo $_SESSION[$variable];
                break;
        }
    }
}