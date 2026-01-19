<?php
namespace Chamilo\Core\Group\UserInterface\Menu;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuDataProvider;
use Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuItem;

class GroupTreeMenuDataProvider extends TreeMenuDataProvider
{
    public const PARAM_ID = 'group_id';

    public function getGroupService(): GroupService
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(GroupService::class);
    }

    public function getIdParameterName(): string
    {
        return self::PARAM_ID;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    private function getMenuItems(TreeMenuItem $parent_menu_item, string $parentId = '0'): void
    {
        $groups = $this->getGroupService()->findGroupsForParentIdentifier($parentId);

        foreach ($groups as $group)
        {
            $menu_item = new TreeMenuItem();
            $menu_item->setTitle($group->get_name());
            $menu_item->setId($group->getId());
            $menu_item->setUrl($this->formatUrl($group->getId()));

            if ($group->hasChildren())
            {
                $this->getMenuItems($menu_item, $group->getId());
            }

            $parent_menu_item->addChild($menu_item);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getTreeMenuData(): TreeMenuItem
    {
        $group = $this->getGroupService()->findRootGroup();

        $menu_item = new TreeMenuItem();
        $menu_item->setTitle($group->get_name());
        $menu_item->setId($group->getId());
        $menu_item->setUrl($this->getUrl());

        if ($group->hasChildren())
        {
            $this->getMenuItems($menu_item, $group->getId());
        }

        $menu_item->setClass('home');

        return $menu_item;
    }
}
