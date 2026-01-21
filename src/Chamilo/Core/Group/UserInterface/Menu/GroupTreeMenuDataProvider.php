<?php
namespace Chamilo\Core\Group\UserInterface\Menu;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuDataProvider;

class GroupTreeMenuDataProvider extends TreeMenuDataProvider
{
    protected GroupService $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getData(string $uriFormat, ?string $itemIdentifier): array
    {
        if (!$itemIdentifier)
        {
            $group = $this->getGroupService()->findRootGroup();

            return [
                $this->getTreeNode(
                    uriFormat: $uriFormat, identifier: $group->getId(), text: $group->getName(),
                    childNodes: $this->processChildren(
                        $uriFormat, $group->getId()
                    )
                )
            ];
        }
        else
        {
            return $this->processChildren($uriFormat, $itemIdentifier);
        }
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    /**
     * @return \Chamilo\Libraries\Format\Menu\TreeMenu\TreeNode[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function processChildren(string $uriFormat, string $parentIdentifier): array
    {
        $groups = $this->getGroupService()->findGroupsForParentIdentifier($parentIdentifier);
        $children = [];

        foreach ($groups as $group)
        {
            $children[] = $this->getTreeNode(
                uriFormat: $uriFormat, identifier: $group->getId(), text: $group->getName(),
                hasChildNodes: $group->hasChildren()
            );
        }

        return $children;
    }

}
