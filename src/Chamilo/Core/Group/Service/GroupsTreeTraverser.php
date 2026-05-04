<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Group\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupsTreeTraverser
{
    public function __construct(protected GroupRepository $groupRepository)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countAncestorsByGroup(Group $group, bool $includeSelf = true, ?ConditionInterface $condition = null
    ): int
    {
        return $this->groupRepository->countAncestorsByGroup($group, $includeSelf, $condition);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countDescendantsByGroup(Group $group, bool $recursiveDescendants = false): int
    {
        if ($group->getRightValue() == $group->getLeftValue() + 1) {
            return 0;
        }
        elseif ($group->getRightValue() == $group->getLeftValue() + 3) {
            return 1;
        }
        elseif ($recursiveDescendants) {
            return ($group->getRightValue() - $group->getLeftValue() - 1) / 2;
        }
        else {
            return $this->groupRepository->countDescendantsByGroup($group);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countSiblingsByGroup(Group $group, bool $includeSelf = false, ?ConditionInterface $condition = null
    ): int
    {
        return $this->groupRepository->countSiblingsByGroup($group, $includeSelf, $condition);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function determineFullyQualifiedNameByGroup(Group $group, bool $includeSelf = true): string
    {
        $ancestors = $this->retrieveAncestorsByGroup($group, $includeSelf);

        $names = [];

        foreach ($ancestors as $ancestor) {
            $names[] = $ancestor->getName();
        }

        return implode(' <span class="text-primary">></span> ', array_reverse($names));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function hasSiblings(Group $group, bool $includeSelf = false, ?ConditionInterface $condition = null): bool
    {
        return $this->countSiblingsByGroup($group, $includeSelf, $condition) > 0;
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveAncestorIdentifiersByGroup(Group $group, bool $includeSelf = true): array
    {
        return $this->groupRepository->retrieveAncestorsIdentifiersByGroup($group, $includeSelf);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveAncestorsByGroup(Group $group, bool $includeSelf = true): ArrayCollection
    {
        return $this->groupRepository->retrieveAncestorsByGroup($group, $includeSelf);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveDescendantIdentifiersByGroup(Group $group, bool $recursiveDescendants = false): array
    {
        return $this->groupRepository->retrieveDescendantsIdentifiersByGroup($group, $recursiveDescendants);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveDescendantsByGroup(Group $group, bool $recursiveDescendants = false): ArrayCollection
    {
        return $this->groupRepository->retrieveDescendantsByGroup($group, $recursiveDescendants);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveSiblingsByGroup(
        Group $group, bool $includeSelf = true, ?ConditionInterface $condition = null
    ): ArrayCollection
    {
        return $this->groupRepository->retrieveSiblingsByGroup($group, $includeSelf, $condition);
    }
}