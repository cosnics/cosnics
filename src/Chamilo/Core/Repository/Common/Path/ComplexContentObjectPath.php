<?php
namespace Chamilo\Core\Repository\Common\Path;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectDisclosure;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Architecture\Interfaces\HelperContentObjectSupport;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @package repository
 */
abstract class ComplexContentObjectPath
{
    use ClassContext;

    /**
     *
     * @var multitype:ComplexContentObjectPathNode
     */
    private $nodes = array();

    /**
     *
     * @var multitype:ComplexContentObjectPathNode
     */
    private $children;

    /**
     *
     * @var multitype:ComplexContentObjectPathNode
     */
    private $parents;

    /**
     *
     * @param ContentObject $content_object
     */
    public function __construct(ContentObject $content_object)
    {
        $this->initialize($content_object);
    }

    /**
     *
     * @return ComplexContentObjectPathNode[]
     */
    public function get_nodes()
    {
        return $this->nodes;
    }

    /**
     *
     * @param int $parent_id
     * @param int $previous_id
     * @param ComplexContentObjectItem $complex_content_object_item
     * @param ContentObject $content_object
     * @param multitype:mixed $propeties
     * @return int
     */
    private function add($parent_id, $previous_sibling_node_id, ComplexContentObjectItem $complex_content_object_item, 
        ContentObject $content_object)
    {
        $properties = $this->get_properties($parent_id, $complex_content_object_item, $content_object);
        
        $node_id = $this->get_next_id();
        $node = ComplexContentObjectPathNode::factory(
            self::context(), 
            $this, 
            $node_id, 
            $parent_id, 
            $previous_sibling_node_id, 
            null, 
            $complex_content_object_item, 
            $content_object, 
            $properties);
        
        $this->nodes[$node_id] = $node;
        
        return $node;
    }

    /**
     *
     * @return int
     */
    public function get_next_id()
    {
        return count($this->nodes) + 1;
    }

    /**
     *
     * @param int $node_id
     * @throws Exception
     * @return ComplexContentObjectPathNode
     */
    public function get_node($node_id)
    {
        if (! isset($this->nodes[$node_id]))
        {
            throw new Exception(Translation::get('NodeDoesntExist'));
        }
        return $this->nodes[$node_id];
    }

    /**
     *
     * @param int $node_id
     * @param boolean $include_self
     * @return array<ComplexContentObjectPathNode>
     */
    public function get_parents_by_id($node_id, $include_self = false, $reverse = false)
    {
        if (! isset($this->parents[$node_id][$include_self][$reverse]))
        {
            $this->parents[$node_id][$include_self][$reverse] = ComplexContentObjectPathNode::get_node_parents(
                $this->get_node($node_id), 
                $include_self, 
                $reverse);
        }
        
        return $this->parents[$node_id][$include_self][$reverse];
    }

    /**
     *
     * @param int $node_id
     */
    public function get_children_by_id($node_id)
    {
        if (! isset($this->children[$node_id]))
        {
            $this->children[$node_id] = ComplexContentObjectPathNode::get_node_children($this->get_node($node_id));
        }
        
        return $this->children[$node_id];
    }

    /**
     *
     * @throws Exception
     * @return ComplexContentObjectPathNode
     */
    public function get_root()
    {
        foreach ($this->nodes as $node)
        {
            if ($node->is_root())
            {
                return $node;
            }
        }
        
        throw new Exception(Translation::get('NoRootNode'));
    }

    /**
     *
     * @return int
     */
    public function count_nodes()
    {
        return count($this->nodes);
    }

    /**
     *
     * @param int $id
     * @return NULL ComplexContentObjectPathNode
     */
    public function get_next_node_by_id($id)
    {
        if ($id >= $this->count_nodes())
        {
            return null;
        }
        else
        {
            return $this->get_node($id + 1);
        }
    }

    /**
     *
     * @param int $id
     * @return NULL ComplexContentObjectPathNode
     */
    public function get_previous_node_by_id($id)
    {
        if ($id <= 1)
        {
            return null;
        }
        else
        {
            return $this->get_node($id - 1);
        }
    }

    /**
     *
     * @param ContentObject $content_object
     */
    public function initialize(ContentObject $content_object)
    {
        $dummy_complex_content_object_item = new ComplexContentObjectItem();
        $dummy_complex_content_object_item->set_ref($content_object->get_id());
        $dummy_complex_content_object_item->set_ref_object($content_object);
        $dummy_complex_content_object_item->set_parent(0);
        $dummy_complex_content_object_item->set_user_id($content_object->get_owner_id());
        $dummy_complex_content_object_item->set_display_order(0);
        $dummy_complex_content_object_item->set_id(0);
        $root_node = $this->add(0, null, $dummy_complex_content_object_item, $content_object);
        $this->add_items($root_node, $content_object);
    }

    public function reset()
    {
        $root = $this->get_root()->get_content_object();
        
        $this->nodes = array();
        $this->children = array();
        $this->parents = array();
        
        DataClassCache::truncate(ComplexContentObjectItem::class_name());
        
        $this->initialize($root);
    }

    /**
     *
     * @param int $parent_id
     * @param ContentObject $root_content_object
     */
    private function add_items(ComplexContentObjectPathNode $parent_node, ContentObject $root_content_object)
    {
        if ($root_content_object instanceof ComplexContentObjectSupport)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class_name(), 
                    ComplexContentObjectItem::PROPERTY_PARENT), 
                new StaticConditionVariable($root_content_object->get_id()));
            $order = new OrderBy(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class_name(), 
                    ComplexContentObjectItem::PROPERTY_DISPLAY_ORDER), 
                SORT_ASC);
            $parameters = new DataClassRetrievesParameters($condition, null, null, array($order));
            
            $complex_content_object_items = DataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class_name(), 
                $parameters);
            
            $previous_sibling_node = null;
            
            while ($complex_content_object_item = $complex_content_object_items->next_result())
            {
                
                $content_object = $complex_content_object_item->get_ref_object();
                
                if ($content_object instanceof HelperContentObjectSupport)
                {
                    $content_object = DataManager::retrieve_by_id(
                        ContentObject::class_name(), 
                        $content_object->get_reference());
                }
                
                if ($content_object instanceof ComplexContentObjectSupport)
                {
                    $node = $this->add(
                        $parent_node->get_id(), 
                        $previous_sibling_node ? $previous_sibling_node->get_id() : null, 
                        $complex_content_object_item, 
                        $content_object);
                    
                    if ($content_object instanceof ComplexContentObjectDisclosure)
                    {
                        $this->add_items($node, $content_object);
                    }
                }
                elseif ($root_content_object instanceof ComplexContentObjectDisclosure)
                {
                    $node = $this->add(
                        $parent_node->get_id(), 
                        $previous_sibling_node ? $previous_sibling_node->get_id() : null, 
                        $complex_content_object_item, 
                        $content_object);
                }
                
                if ($previous_sibling_node instanceof ComplexContentObjectPathNode)
                {
                    $previous_sibling_node->set_next_sibling_id($node->get_id());
                }
                
                $previous_sibling_node = $node;
            }
        }
    }

    /**
     *
     * @param int $parent_id
     * @param ComplexContentObjectItem $complex_content_object_item
     * @param ContentObject $content_object
     * @return multitype:mixed
     */
    public function get_properties($parent_id, $complex_content_object_item, $content_object)
    {
        return array();
    }

    /**
     * Follow a route through the ComplexContentObjectPath based on a set a sequential content object ids
     * 
     * @param multitype:int $content_object_ids
     * @return ComplexContentObjectPathNode
     */
    public function follow_path_by_content_object_ids($content_object_ids)
    {
        $root_content_object_id = array_shift($content_object_ids);
        $root_node = $this->get_root();
        
        if ($root_content_object_id != $root_node->get_content_object()->get_id())
        {
            throw new Exception('RootsNoLongerMatching');
        }
        
        foreach ($content_object_ids as $content_object_id)
        {
            $children = $root_node->get_children();
            $child_found = false;
            
            foreach ($children as $child_node)
            {
                if ($child_node->get_content_object()->get_id() == $content_object_id)
                {
                    $root_node = $child_node;
                    $child_found = true;
                    continue;
                }
            }
            
            if (! $child_found)
            {
                throw new Exception('NoMatchingPathFound');
            }
        }
        
        return $root_node;
    }

    /**
     *
     * @param string $type
     * @param ContentObject $content_object
     *
     * @return ComplexContentObjectPath
     */
    public static function factory($type, ContentObject $content_object)
    {
        $class = $content_object->package() . '\ComplexContentObjectPath';
        return new $class($content_object);
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static::context();
    }
}
