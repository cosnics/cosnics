<?php

namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;

/**
 * @package Chamilo\Core\Group\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupClosureTableGenerator
{
    /**
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    protected $groupService;

    /**
     * GroupClosureTableGenerator constructor.
     *
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     */
    public function __construct(\Chamilo\Core\Group\Service\GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * Generates the closure tree for the existing tree structure
     */
    public function generate()
    {
        $rootGroup = $this->groupService->getRootGroup();
        $this->handleGroup($rootGroup);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     */
    protected function handleGroup(Group $group)
    {
        $this->groupService->addGroupToClosureTable($group);

        $children = $this->groupService->findDirectChildrenFromGroup($group);
        foreach($children as $child)
        {
            $this->handleGroup($child);
        }
    }

}