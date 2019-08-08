<?php
namespace Chamilo\Core\User\Ajax\Component;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Core\User\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LeaveComponent extends \Chamilo\Core\User\Ajax\Manager implements NoVisitTraceComponentInterface
{

    public function run()
    {
        $tracker = Request::post('tracker');
        $user_id = \Chamilo\Libraries\Platform\Session\Session::get_user_id();
        
        Event::trigger(
            'Leave', 
            Manager::context(), 
            array(
                Visit::PROPERTY_ID => $tracker, 
                Visit::PROPERTY_LOCATION => null, 
                Visit::PROPERTY_USER_ID => $user_id));
        
        JsonAjaxResult::success();
    }
}