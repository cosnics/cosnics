<?php
namespace Chamilo\Core\Group\UserInterface\Menu;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\UserInterface\Tree\Service\TreeMenuDataProvider;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Group\UserInterface\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupTreeMenuDataProvider extends TreeMenuDataProvider
{
    protected GroupService $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getChildDataClasses(string $parentIdentifier): ArrayCollection
    {
        return $this->getGroupService()->findGroupsForParentIdentifier($parentIdentifier);
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode[]
     */
    public function getData(string $uriFormat, ?string $identifier): array
    {
        $getIdentifier = function (Group $group) {
            return $group->getId();
        };

        $getText = function (Group $group) {
            return $group->getName();
        };

        $hasChildren = function (Group $group) {
            return $group->hasChildren();
        };

        return $this->__getData($uriFormat, $identifier, $getIdentifier, $getText, $hasChildren);
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getRootDataClass(): Group
    {
        return $this->getGroupService()->findRootGroup();
    }

}
