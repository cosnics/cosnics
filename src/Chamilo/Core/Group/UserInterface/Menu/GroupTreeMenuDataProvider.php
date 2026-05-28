<?php
namespace Chamilo\Core\Group\UserInterface\Menu;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Libraries\UserInterface\Tree\Service\TreeMenuDataProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\UserInterface\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class GroupTreeMenuDataProvider extends TreeMenuDataProvider
{
    public function __construct(protected GroupService $groupService, protected GroupsTreeTraverser $groupsTreeTraverser
    )
    {
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getChildDataClasses(string|Uuid $parentIdentifier): ArrayCollection
    {
        return $this->groupService->retrieveDescendantsByParentIdentifier($parentIdentifier);
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode[]
     */
    public function getData(string $uriFormat, string|Uuid|null $identifier): array
    {
        $getIdentifier = function (Group $group) {
            return $group->getIdentifier();
        };

        $getText = function (Group $group) {
            return $group->getName();
        };

        $hasChildren = function (Group $group) {
            return $this->groupsTreeTraverser->hasDescendantsByGroup($group);
        };

        return $this->__getData($uriFormat, $identifier, $getIdentifier, $getText, $hasChildren);
    }

    protected function getRootDataClass(): Group
    {
        return $this->groupService->retrieveRootGroup();
    }
}
