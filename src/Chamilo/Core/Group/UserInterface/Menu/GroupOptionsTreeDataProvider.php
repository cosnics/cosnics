<?php
namespace Chamilo\Core\Group\UserInterface\Menu;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Tree\Options\OptionsTreeDataProvider;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Group\UserInterface\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupOptionsTreeDataProvider extends OptionsTreeDataProvider
{
    protected GroupService $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getChildDataClasses(string $parentIdentifier): ArrayCollection
    {
        return $this->getGroupService()->findGroupsForParentIdentifier($parentIdentifier);
    }

    /**
     * @return \Chamilo\Libraries\Format\Tree\TreeNode[]
     */
    public function getData(?string $identifier): array
    {
        $getIdentifier = function (Group $group) {
            return $group->getId();
        };

        $getText = function (Group $group) {
            return $group->getName();
        };

        return [$this->__getData($getIdentifier, $getText, $identifier)];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getDataClassByIdentifier(string $identifier): Group
    {
        return $this->getGroupService()->findGroupByIdentifier($identifier);
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
