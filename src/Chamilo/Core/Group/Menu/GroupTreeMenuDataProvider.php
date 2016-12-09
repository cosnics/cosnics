<?php
namespace Chamilo\Core\Group\Menu;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuDataProvider;
use Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuItem;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class GroupTreeMenuDataProvider extends TreeMenuDataProvider
{
    const PARAM_ID = 'group_id';

    public function get_tree_menu_data()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID), 
            new StaticConditionVariable(0));
        $group = DataManager::retrieves(
            Group::class_name(), 
            new DataClassRetrievesParameters(
                $condition, 
                1, 
                null, 
                new OrderBy(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME))))->next_result();
        
        $menu_item = new TreeMenuItem();
        $menu_item->set_title($group->get_name());
        $menu_item->set_id($group->get_id());
        // $menu_item['url'] = $this->get_url($group->get_id());
        $menu_item->set_url($this->get_url());
        
        if ($group->has_children())
        {
            $this->get_menu_items($menu_item, $group->get_id());
        }
        
        $menu_item->set_class('home');
        return $menu_item;
    }

    private function get_menu_items($parent_menu_item, $parent_id = 0)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID), 
            new StaticConditionVariable($parent_id));
        $groups = DataManager::retrieves(
            Group::class_name(), 
            new DataClassRetrievesParameters(
                $condition, 
                null, 
                null, 
                new OrderBy(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME))));
        
        while ($group = $groups->next_result())
        {
            $group_id = $group->get_id();
            
            $menu_item = new TreeMenuItem();
            $menu_item->set_title($group->get_name());
            $menu_item->set_id($group->get_id());
            $menu_item->set_url($this->format_url($group->get_id()));
            
            if ($group->has_children())
            {
                $this->get_menu_items($menu_item, $group->get_id());
            }
            
            $parent_menu_item->add_child($menu_item);
        }
    }

    public function get_id_param()
    {
        return self::PARAM_ID;
    }
}
