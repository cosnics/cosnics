<?php
namespace Chamilo\Core\Rights\Entity\Group;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupSubscriptionService;
use Chamilo\Core\Rights\Editor\Table\LocationEntity\LocationEntityTable;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Libraries\Architecture\Application\Application;

class PlatformGroupEntityTable extends LocationEntityTable
{
    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * @var GroupSubscriptionService
     */
    protected $groupSubscriptionService;

    /**
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
        parent::__construct($parentComponent, PlatformGroupEntity::ENTITY_TYPE);

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
