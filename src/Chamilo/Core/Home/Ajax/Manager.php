<?php
namespace Chamilo\Core\Home\Ajax;

use Chamilo\Core\Group\Service\GroupSubscriptionService;
use Chamilo\Libraries\Architecture\AjaxManager;

/**
 *
 * @package Chamilo\Core\Home\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    /**
     * @return GroupSubscriptionService
     */
    protected function getGroupSubscriptionService()
    {
        return $this->getService(GroupSubscriptionService::class);
    }
}
