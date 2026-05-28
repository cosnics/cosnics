<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Group\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupsTreeTraverser
{
    public function __construct(protected GroupRepository $groupEntityRepository)
    {
    }

    public function countDescendantsByGroup(Group $group, bool $recursiveDescendants = false): int
    {
        return $this->groupEntityRepository->countDescendantsByGroup($group, $recursiveDescendants);
    }

    public function countSiblingsByGroup(Group $group): int
    {
        return $this->groupEntityRepository->countSiblingsByGroup($group);
    }

    public function determineFullyQualifiedNameByGroup(
        Group $group, bool $includeSelf = true, string $separator = ' <span class="text-primary">></span> '
    ): string
    {
        return $this->groupEntityRepository->findAncestorsAsPathString(
            $group, $includeSelf, $separator
        );
    }

    public function hasDescendantsByGroup(Group $group, bool $recursive = true): bool
    {
        return $this->groupEntityRepository->hasDescendantsByGroup($group, $recursive);
    }

    public function hasSiblings(Group $group): bool
    {
        return $this->countSiblingsByGroup($group) > 0;
    }

    /**
     * @return string[]
     */
    public function retrieveAncestorIdentifiersByGroup(Group $group, bool $includeSelf = true): array
    {
        return $this->groupEntityRepository->findAncestorsIdentifiersByGroup($group, $includeSelf);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\Entity\Group $group
     * @param bool $includeSelf
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\Group>
     */
    public function retrieveAncestorsByGroup(Group $group, bool $includeSelf = true): ArrayCollection
    {
        return $this->groupEntityRepository->findAncestorsByGroup($group, $includeSelf);
    }

    /**
     * @return string[]
     */
    public function retrieveDescendantIdentifiersByGroup(Group $group, bool $recursiveDescendants = false): array
    {
        return $this->groupEntityRepository->findDescendantsIdentifiersByGroup($group, $recursiveDescendants);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\Group>
     */
    public function retrieveDescendantsByGroup(Group $group, bool $recursiveDescendants = false): ArrayCollection
    {
        return $this->groupEntityRepository->findDescendantsByGroup($group, $recursiveDescendants);
    }
}