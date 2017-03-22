<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class XmlImageFeedComponent extends \Chamilo\Core\Repository\Ajax\Manager
{

    function run()
    {
        $conditions = array();
        
        $query_condition = Utilities::query_to_condition(
            Request::get('query'), 
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE));
        
        if (isset($query_condition))
        {
            $conditions[] = $query_condition;
        }
        
        $owner_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID), 
            new StaticConditionVariable($this->get_user_id()));
        
        $conditions[] = $owner_condition;
        
        if (is_array(Request::get('exclude')))
        {
            $c = array();
            foreach (Request::get('exclude') as $id)
            {
                $c[] = new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID), 
                    new StaticConditionVariable($id));
            }
            $conditions[] = new NotCondition(new OrCondition($c));
        }
        
        $image_types = File::get_image_types();
        $image_type_conditions = array();
        
        foreach ($image_types as $type)
        {
            $image_type_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(File::class_name(), File::PROPERTY_FILENAME), 
                '*.' . $type);
        }
        
        $conditions[] = new OrCondition($image_type_conditions);
        
        $condition = new AndCondition($conditions);
        
        $objects = DataManager::retrieve_active_content_objects(
            File::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        $objects_by_cat = array();
        
        while ($lo = $objects->next_result())
        {
            $cid = $lo->get_parent_id();
            
            if (is_array($objects_by_cat[$cid]))
            {
                array_push($objects_by_cat[$cid], $lo);
            }
            else
            {
                $objects_by_cat[$cid] = array($lo);
            }
        }
        
        $categories = array();
        $root = new RepositoryCategory();
        $root->set_id(0);
        $root->set_name(Translation::get('MyRepository'));
        $root->set_parent(- 1);
        $categories[- 1] = array($root);
        
        $cats = DataManager::retrieve_categories(
            new EqualityCondition(
                new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE_ID), 
                new StaticConditionVariable(Session::get_user_id())));
        while ($cat = $cats->next_result())
        {
            $parent = $cat->get_parent();
            if (is_array($categories[$parent]))
            {
                array_push($categories[$parent], $cat);
            }
            else
            {
                $categories[$parent] = array($cat);
            }
        }
        
        $tree = $this->get_tree(- 1, $categories);
        
        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="iso-8859-1"?>', "\n", '<tree>', "\n";
        if (isset($tree))
        {
            $this->dump_tree($tree, $objects_by_cat);
        }
        
        echo '</tree>';
    }

    public function get_tree($index, $flat_tree)
    {
        $tree = array();
        
        foreach ($flat_tree[$index] as $child)
        {
            $tree[] = array('obj' => $child, 'sub' => $this->get_tree($child->get_id(), $flat_tree));
        }
        
        return $tree;
    }

    public function dump_tree($tree, $objects)
    {
        if (! count($tree))
        {
            return;
        }
        
        foreach ($tree as $node)
        {
            if (! $this->contains_results($node, $objects))
            {
                continue;
            }
            
            $id = $node['obj']->get_id();
            
            if ($node['obj'] instanceof RepositoryCategory)
            {
                $title = $node['obj']->get_name();
            }
            else
            {
                $title = $node['obj']->get_title();
            }
            
            echo '<node id="category_', $id, '" classes="category unlinked"
	title="', htmlentities($title), '">', "\n";
            
            foreach ($objects[$id] as $lo)
            {
                $id = $lo->get_id();
                $value = Utilities::content_object_for_element_finder($lo);
                
                echo '<leaf id="lo_' . $id . '"
	classes="' . $value['classes'] . '"
	title="' . htmlspecialchars($value['title']) . '"
	description="' . htmlspecialchars($value['description']) . '" />' .
                     "\n";
            }
            
            echo '</node>', "\n";
        }
    }

    public function contains_results($node, $objects)
    {
        if (count($objects[$node['obj']->get_id()]))
        {
            return true;
        }
        foreach ($node['sub'] as $child)
        {
            if ($this->contains_results($child, $objects))
            {
                return true;
            }
        }
        return false;
    }
}
