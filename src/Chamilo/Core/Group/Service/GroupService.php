<?php

namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use function array_reverse;
use function implode;

/**
 * Service to manage the groups of Chamilo
 *
 * @package Chamilo\Core\Group\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * TODO: Methods to refactor:
 *      - get_parents
 *      - get_ancestors
 *      - is_parent_of
 *      - get_children
 */
class GroupService
{
    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * This is temporarily used for BC issues
     *
     * @var \Chamilo\Core\Group\Service\GroupSubscriptionService
     */
    protected $groupSubscriptionService;

    /**
     * GroupService constructor.
     *
     * @param \Chamilo\Core\Group\Storage\Repository\GroupRepository $groupRepository
     */
    public function __construct(\Chamilo\Core\Group\Storage\Repository\GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param \Chamilo\Core\Group\Service\GroupSubscriptionService $groupSubscriptionService
     *
     * @return \Chamilo\Core\Group\Service\GroupService
     */
    public function setGroupSubscriptionService(
        \Chamilo\Core\Group\Service\GroupSubscriptionService $groupSubscriptionService
    ): \Chamilo\Core\Group\Service\GroupService
    {
        $this->groupSubscriptionService = $groupSubscriptionService;

        return $this;
    }

    /**
     * @return Group
     */
    public function getRootGroup()
    {
        return $this->groupRepository->getRootGroup();
    }

    /**
     * @param Group $group
     *
     * @return bool
     */
    public function isRootGroup(Group $group)
    {
        return $group->get_parent_id() == 0;
    }

    /**
     * @param string $groupCode
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     */
    public function findGroupByCode($groupCode)
    {
        if (empty($groupCode))
        {
            throw new \InvalidArgumentException('The given groupcode can not be empty');
        }

        $group = $this->groupRepository->findGroupByCode($groupCode);

        if (!$group instanceof Group)
        {
            throw new \RuntimeException('Could not find the group with groupcode ' . $groupCode);
        }

        return $group;
    }

    /**
     * @param int $groupIdentifier
     *
     * @return Group
     */
    public function getGroupByIdentifier($groupIdentifier)
    {
        $group = $this->groupRepository->findGroupByIdentifier($groupIdentifier);

        if (!$group instanceof Group)
        {
            throw new \RuntimeException('Could not find the group with identifier ' . $groupIdentifier);
        }

        return $group;
    }

    /**
     * @param string $groupCode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUserToGroupByCode($groupCode, User $user)
    {
        $this->groupSubscriptionService->subscribeUserToGroupByCode($groupCode, $user);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUserToGroup(Group $group, User $user)
    {
        $this->groupSubscriptionService->subscribeUserToGroup($group, $user);
    }

    /**
     * Returns all the child groups for a given group. Has the possibility to include the given group.
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Group[]
     */
    public function getAllChildrenForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->groupRepository->getAllChildrenForGroup($group, $includeSelf);
    }

    /**
     * Returns the identifiers of all the children for a given group. Has the possibility to include the given group.
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return int[]|string[]
     */
    public function getAllChildIdsForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->groupRepository->getAllChildIdsForGroup($group, $includeSelf);
    }

    /**
     * @param Group $group
     * @param bool $includeSelf
     *
     * @return int
     */
    public function countAllChildrenForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->groupRepository->countAllChildrenForGroup($group, $includeSelf);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return Group[]
     */
    public function findDirectChildrenFromGroup(Group $group)
    {
        return $this->groupRepository->getDirectChildrenOfGroup($group);
    }

    /**
     * @param Group $group
     *
     * @return int
     */
    public function countDirectChildrenForGroup(Group $group)
    {
        return $this->groupRepository->countDirectChildrenForGroup($group);
    }

    /**
     * @param Group $group
     *
     * @return bool
     */
    public function hasChildren(Group $group)
    {
        return $this->countDirectChildrenForGroup($group) > 0;
    }

    /**
     * Returns all the parent groups for a given group. Has the possibility to include the given group.
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Group[]
     */
    public function getAllParentsForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->groupRepository->getAllParentsForGroup($group, $includeSelf);
    }

    /**
     * Returns all the parent ids for a given group. Has the possibility to include the given group.
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return int[]|string[]
     */
    public function getAllParentIdsForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->groupRepository->getAllParentIdsForGroup($group, $includeSelf);
    }

    /**
     * @param Group $group
     * @param bool $includeSelf
     *
     * @return int
     */
    public function countAllParentsForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->groupRepository->countAllParentsForGroup($group, $includeSelf);
    }

    /**
     * Returns the direct parent group of a given group
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass|Group
     */
    public function getDirectParentOfGroup(Group $group)
    {
        return $this->groupRepository->getDirectParentOfGroup($group);
    }

    /**
     * @param Group $group
     * @param bool $includeSelf
     *
     * @return string
     */
    public function getFullyQualifiedNameForGroup(Group $group, bool $includeSelf = true)
    {
        $parents = $this->getAllParentsForGroup($group, $includeSelf);
        $names = array();

        foreach($parents as $parent)
        {
            $names[] = $parent->get_name();
        }

        return implode(' <span class="visible">></span> ', $names);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param int $newParentId
     *
     * @return bool|void
     */
    public function moveGroup(Group $group, int $newParentId)
    {
        // LEGACY CODE: make sure that the group is also moved using the move in the dataclass. We use this to
        // make sure that the nested sets are used as a backup until the refactoring is done.
        if(!$group->move($newParentId))
        {
            throw new \RuntimeException(sprintf('Could not move the group with id %s', $newParentId));
        }

        $group->set_parent_id($newParentId);
        $this->updateGroup($group);

        return $this->groupRepository->moveGroup($group, $newParentId);
    }

    /**
     * Adds an existing group to the closure table
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return bool
     */
    public function addGroupToClosureTable(Group $group)
    {
        return $this->groupRepository->addGroupToClosureTable($group);
    }

    /**
     * Creates a new group and adds the group to the closure table
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     */
    public function createGroup(Group $group)
    {
        // LEGACY CODE: make sure that the group is created using the create dataclass. We use
        // create from dataclass to make sure that the nested sets are used as backup until the refactoring is done.

        if (!$group->create())
        {
            throw new \RuntimeException(
                sprintf('Could not create the group with id %s in the database', $group->getId())
            );
        }

        if (!$this->groupRepository->createGroup($group))
        {
            throw new \RuntimeException(
                sprintf('Could not create the group with id %s in the database', $group->getId())
            );
        }

        return $group;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     */
    public function updateGroup(Group $group)
    {
        if (!$this->groupRepository->updateGroup($group))
        {
            throw new \RuntimeException(
                sprintf('Could not update the group with id %s in the database', $group->getId())
            );
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     */
    public function deleteGroup(Group $group)
    {
        // LEGACY CODE: make sure that the group is deleted using the delete dataclass. We use
        // delete from dataclass to make sure that the nested sets are used as backup until the refactoring is done.

        if (!$group->delete())
        {
            throw new \RuntimeException(
                sprintf('Could not delete the group with id %s in the database', $group->getId())
            );
        }

        if (!$this->groupRepository->deleteGroup($group))
        {
            throw new \RuntimeException(
                sprintf('Could not delete the group with id %s in the database', $group->getId())
            );
        }

        return $group;
    }
}