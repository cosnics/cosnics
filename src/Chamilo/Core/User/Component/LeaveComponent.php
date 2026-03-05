<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\EventDispatcher\Event\BeforeUserLeavePageEvent;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class LeaveComponent extends Manager implements NoVisitTraceComponentInterface
{
    public function run(?User $currentUser = null): Response
    {
        if ($currentUser instanceof User) {
            $this->getEventDispatcher()->dispatch(
                new BeforeUserLeavePageEvent($currentUser, $this->getRequest()->request->get('tracker'))
            );

            return JsonAjaxResult::success();
        }
        else {
            return JsonAjaxResult::badRequest();
        }
    }
}