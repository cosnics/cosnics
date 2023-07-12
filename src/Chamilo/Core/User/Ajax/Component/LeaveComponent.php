<?php
namespace Chamilo\Core\User\Ajax\Component;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * @package Chamilo\Core\User\Ajax
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class LeaveComponent extends \Chamilo\Core\User\Ajax\Manager
{

    public function run()
    {
        $tracker = $this->getRequest()->request->get('tracker');
        $user_id = $this->getSession()->get(Manager::SESSION_USER_ID);

        Event::trigger(
            'Leave', Manager::CONTEXT, [
                Visit::PROPERTY_ID => $tracker,
                Visit::PROPERTY_LOCATION => null,
                Visit::PROPERTY_USER_ID => $user_id
            ]
        );

        JsonAjaxResult::success();
    }
}