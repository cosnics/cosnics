<?php
namespace Chamilo\Core\Group\Menu;

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

    /**
     * @see \Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuDataProvider::get_id_param()
     */
    public function get_id_param()
    {
        return self::PARAM_ID;
    }

    /**
     * @param \Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuItem $parent_menu_item
     * @param string $parentId
     */
    private function get_menu_items($parent_menu_item, $parentId = 0)
    {
        $groups = $this->getGroupService()->findGroupsForParentIdentifier($parentId);

        foreach ($groups as $group)
        {
            $menu_item = new TreeMenuItem();
            $menu_item->set_title($group->get_name());
            $menu_item->set_id($group->getId());
            $menu_item->set_url($this->format_url($group->getId()));

            if ($group->hasChildren())
            {
                $this->get_menu_items($menu_item, $group->getId());
            }

            $parent_menu_item->add_child($menu_item);
        }
    }

    /**
     * @see \Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuDataProvider::get_tree_menu_data()
     */
    public function get_tree_menu_data()
    {
        $group = $this->getGroupService()->findRootGroup();

        $menu_item = new TreeMenuItem();
        $menu_item->set_title($group->get_name());
        $menu_item->set_id($group->getId());
        $menu_item->set_url($this->get_url());

        if ($group->hasChildren())
        {
            $this->get_menu_items($menu_item, $group->getId());
        }

        $menu_item->set_class('home');

        return $menu_item;
    }
}
