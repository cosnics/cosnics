<?php
namespace Chamilo\Core\Group\UserInterface\Menu;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuDataProvider;
use stdClass;

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
    public function getData(string $urlFormat, ?string $itemIdentifier): array
    {
        $items = [];

        if (!$itemIdentifier)
        {
            $group = $this->getGroupService()->findRootGroup();
            $items[] = $this->getItem($group, $urlFormat);
        }
        else
        {
            $groups = $this->getGroupService()->findGroupsForParentIdentifier($itemIdentifier);

            foreach ($groups as $group)
            {
                $items[] = $this->getItem($group, $urlFormat);
            }
        }

        return $items;
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    protected function getItem(Group $group, string $urlFormat): stdClass
    {
        $item = new stdClass();
        $item->text = $group->getName();
        $item->id = $group->getId();

        $item->a_attr = new stdClass();
        $item->a_attr->href = html_entity_decode($this->formatUrl($urlFormat, $group->getId()));

        if ($group->hasChildren())
        {
            $item->children = true;
            //$this->processChildren($urlFormat, $item, $group->getId());
        }

        return $item;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function processChildren(string $urlFormat, stdClass $parentItem, string $parentIdentifier): void
    {
        $groups = $this->getGroupService()->findGroupsForParentIdentifier($parentIdentifier);

        foreach ($groups as $group)
        {
            $parentItem->children[] = $this->getItem($group, $urlFormat);
        }
    }

}
