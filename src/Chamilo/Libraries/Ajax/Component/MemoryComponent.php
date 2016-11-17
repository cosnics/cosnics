<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MemoryComponent extends \Chamilo\Libraries\Ajax\Manager
{

    public function run()
    {
        $variable = Request::post('variable');
        $value = Request::post('value');
        $action = Request::post('action');
        
        switch ($action)
        {
            case 'set' :
                $_SESSION[$variable] = $value;
                break;
            case 'get' :
                echo $_SESSION[$variable];
                break;
            case 'clear' :
                unset($_SESSION[$variable]);
                break;
            default :
                echo $_SESSION[$variable];
                break;
        }
    }
}