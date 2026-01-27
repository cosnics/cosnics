<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\EventDispatcher\Event\BeforeUserLeavePageEvent;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class LeaveComponent extends Manager implements NoVisitTraceComponentInterface
{

    public function run(): Response
    {
        $this->getEventDispatcher()->dispatch(
            new BeforeUserLeavePageEvent($this->getUser(), $this->getRequest()->request->get('tracker'))
        );

        return JsonAjaxResult::success();
    }
}