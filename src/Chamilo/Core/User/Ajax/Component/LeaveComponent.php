<?php
namespace Chamilo\Core\User\Ajax\Component;

use Chamilo\Core\User\Ajax\Manager;
use Chamilo\Core\User\Service\UserEventNotifier;
use Chamilo\Libraries\Architecture\Interfaces\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @package Chamilo\Core\User\Ajax
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class LeaveComponent extends Manager implements NoVisitTraceComponentInterface
{

    public function run()
    {
        $this->getUserEventNotifier()->beforeLeavePage($this->getUser(), $this->getRequest()->request->get('tracker'));

        JsonAjaxResult::success();
    }

    public function getUserEventNotifier(): UserEventNotifier
    {
        return $this->getService(UserEventNotifier::class);
    }
}