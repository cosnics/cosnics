<?php
namespace Chamilo\Core\Group\Ajax\Component;

use Chamilo\Core\Group\Ajax\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Group\XmlFeeds
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class XmlGroupFeedComponent extends Manager
{

    public function run()
    {
        $query = Request::get('query');
        $exclude = Request::get('exclude');
        
        $group_conditions = array();
        
        if ($query)
        {
            $q = '*' . $query . '*';
            $group_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME), 
                $q);
        }
        
        if ($exclude)
        {
            if (! is_array($exclude))
            {
                $exclude = array($exclude);
            }
            
            $exclude_conditions = array();
            $exclude_conditions['group'] = array();
            
            foreach ($exclude as $id)
            {
                $id = explode('_', $id);
                
                if ($id[0] == 'group')
                {
                    $condition = new NotCondition(
                        new EqualityCondition(
                            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_GROUP_ID), 
                            new StaticConditionVariable($id[1])));
                }
                
                $exclude_conditions[$id[0]][] = $condition;
            }
            
            if (count($exclude_conditions['group']) > 0)
            {
                $group_conditions[] = new AndCondition($exclude_conditions['group']);
            }
        }
        
        $group_condition = null;
        if (count($group_conditions) > 1)
        {
            $group_condition = new AndCondition($group_conditions);
        }
        elseif (count($group_conditions) == 1)
        {
            $group_condition = $group_conditions[0];
        }
        
        $groups = array();
        
        $group_result_set = DataManager::retrieves(
            Group::class_name(), 
            new DataClassRetrievesParameters(
                $group_condition, 
                null, 
                null, 
                array(new OrderBy(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME)))));
        
        while ($group = $group_result_set->next_result())
        {
            $group_parent_id = $group->get_parent();
            
            if (! is_array($groups[$group_parent_id]))
            {
                $groups[$group_parent_id] = array();
            }
            
            if (! isset($groups[$group_parent_id][$group->get_id()]))
            {
                $groups[$group_parent_id][$group->get_id()] = $group;
            }
            
            if ($group_parent_id != 0)
            {
                $tree_parents = $group->get_parents(false);
                
                while ($tree_parent = $tree_parents->next_result())
                {
                    $tree_parent_parent_id = $tree_parent->get_parent();
                    
                    if (! is_array($groups[$tree_parent_parent_id]))
                    {
                        $groups[$tree_parent_parent_id] = array();
                    }
                    
                    if (! isset($groups[$tree_parent_parent_id][$tree_parent->get_id()]))
                    {
                        $groups[$tree_parent_parent_id][$tree_parent->get_id()] = $tree_parent;
                    }
                }
            }
        }
        
        $groups_tree = $this->get_group_tree(0, $groups);
        
        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n", '<tree>' . "\n";
        echo $this->dump_tree($groups_tree);
        echo '</tree>';
    }

    public function dump_tree($groups)
    {
        $html = array();
        
        if ($this->contains_results($groups))
        {
            $this->dump_groups_tree($groups);
        }
    }

    public function dump_groups_tree($groups)
    {
        global $group_ids;
        
        foreach ($groups as $group)
        {
            if ($this->contains_results($group['children']))
            {
                // echo '<node id="group_' . $group['group']->get_id() . '" classes="type type_group' .
                // ((isset($group_ids)
                // && ! in_array($group['group']->get_id(), $group_ids)) ? ' disabled' : '') . '" title="' .
                // htmlspecialchars($group['group']->get_name()) . '" description="' .
                // htmlspecialchars($group['group']->get_name()) . '">', "\n";
                echo '<node id="group_' . $group['group']->get_id() . '" classes="type type_group" title="' .
                     htmlspecialchars($group['group']->get_name()) . '" description="' .
                     htmlspecialchars($group['group']->get_name()) . '">', "\n";
                $this->dump_groups_tree($group['children']);
                echo '</node>', "\n";
            }
            else
            {
                // echo '<leaf id="group_' . $group['group']->get_id() . '" classes="type type_group' .
                // ((isset($group_ids)
                // && ! in_array($group['group']->get_id(), $group_ids)) ? ' disabled' : '') . '" title="' .
                // htmlspecialchars($group['group']->get_name()) . '" description="' .
                // htmlspecialchars($group['group']->get_name()) . '"/>' . "\n";
                echo '<leaf id="group_' . $group['group']->get_id() . '" classes="type type_group" title="' .
                     htmlspecialchars($group['group']->get_name()) . '" description="' .
                     htmlspecialchars($group['group']->get_name()) . '"/>' . "\n";
            }
        }
    }

    public function get_group_tree($index, $groups)
    {
        $tree = array();
        foreach ($groups[$index] as $child)
        {
            $tree[] = array('group' => $child, 'children' => $this->get_group_tree($child->get_id(), $groups));
        }
        return $tree;
    }

    public function contains_results($objects)
    {
        if (count($objects))
        {
            return true;
        }
        return false;
    }
}