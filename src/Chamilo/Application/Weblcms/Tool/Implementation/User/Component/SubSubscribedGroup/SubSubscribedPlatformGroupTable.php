<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\SubSubscribedGroup;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupSubscriptionService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

/**
 * * ***************************************************************************
 * Table to display a list of subgroups subscribed to a course.
 *
 * @author Stijn Van Hoecke
 *         ****************************************************************************
 */
class SubSubscribedPlatformGroupTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager::PARAM_OBJECT_ID;
    /**
     * @var GroupService
     */
    protected $groupService;
    /**
     * @var GroupSubscriptionService
     */
    protected $groupSubscriptionService;

    /**
     * SubSubscribedPlatformGroupTable constructor.
     *
     * @param Application $parentComponent
     * @param GroupService $groupService
     * @param GroupSubscriptionService $groupSubscriptionService
     *
     * @throws \Exception
     */
    public function __construct(
        Application $parentComponent, GroupService $groupService, GroupSubscriptionService $groupSubscriptionService
    )
    {
        parent::__construct($parentComponent);

        $this->groupService = $groupService;
        $this->groupSubscriptionService = $groupSubscriptionService;
    }

    /**
     * @return GroupService
     */
    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    /**
     * @return GroupSubscriptionService
     */
    public function getGroupSubscriptionService(): GroupSubscriptionService
    {
        return $this->groupSubscriptionService;
    }
}
