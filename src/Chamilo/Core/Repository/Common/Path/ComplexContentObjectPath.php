<?php
namespace Chamilo\Core\Repository\Common\Path;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectDisclosureInterface;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupportInterface;
use Chamilo\Libraries\Architecture\Interfaces\HelperContentObjectSupportInterface;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @package repository
 */
abstract class ComplexContentObjectPath
{
    /**
     * @var \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode[]
     */
    private $children;

    /**
     * @var \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode[]
     */
    private $nodes = [];

    /**
     * @var \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode[]
     */
    private $parents;

    /**
     * @param ContentObject $content_object
     */
    public function __construct(ContentObject $content_object)
    {
        $this->initialize($content_object);
    }

    /**
     * @param int $parent_id
     * @param int $previous_id
     * @param ComplexContentObjectItem $complex_content_object_item
     * @param ContentObject $content_object
     * @param $propeties
     *
     * @return int
     */
    private function add(
        $parent_id, $previous_sibling_node_id, ComplexContentObjectItem $complex_content_object_item,
        ContentObject $content_object
    )
    {
        $properties = $this->get_properties($parent_id, $complex_content_object_item, $content_object);

        $node_id = $this->get_next_id();
        $node = ComplexContentObjectPathNode::factory(
            static::CONTEXT, $this, $node_id, $parent_id, $previous_sibling_node_id, null, $complex_content_object_item,
            $content_object, $properties
        );

        $this->nodes[$node_id] = $node;

        return $node;
    }

    /**
     * @param int $parent_id
     * @param ContentObject $root_content_object
     */
    private function add_items(ComplexContentObjectPathNode $parent_node, ContentObject $root_content_object)
    {
        if ($root_content_object instanceof ComplexContentObjectSupportInterface)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                ), new StaticConditionVariable($root_content_object->get_id())
            );
            $order = new OrderProperty(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_DISPLAY_ORDER
                ), SORT_ASC
            );
            $parameters = new RetrievesParameters(
                condition: $condition, orderBy: new OrderBy([$order])
            );

            $complex_content_object_items = DataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class, $parameters
            );

            $previous_sibling_node = null;

            foreach ($complex_content_object_items as $complex_content_object_item)
            {

                $content_object = $complex_content_object_item->get_ref_object();

                if ($content_object instanceof HelperContentObjectSupportInterface)
                {
                    $content_object = DataManager::retrieve_by_id(
                        ContentObject::class, $content_object->get_reference()
                    );
                }

                if ($content_object instanceof ComplexContentObjectSupportInterface)
                {
                    $node = $this->add(
                        $parent_node->get_id(), $previous_sibling_node ? $previous_sibling_node->get_id() : null,
                        $complex_content_object_item, $content_object
                    );

                    if ($content_object instanceof ComplexContentObjectDisclosureInterface)
                    {
                        $this->add_items($node, $content_object);
                    }
                }
                elseif ($root_content_object instanceof ComplexContentObjectDisclosureInterface)
                {
                    $node = $this->add(
                        $parent_node->get_id(), $previous_sibling_node ? $previous_sibling_node->get_id() : null,
                        $complex_content_object_item, $content_object
                    );
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
     * @return int
     */
    public function count_nodes()
    {
        return count($this->nodes);
    }

    /**
     * @param string $type
     * @param ContentObject $content_object
     *
     * @return ComplexContentObjectPath
     */
    public static function factory($type, ContentObject $content_object)
    {
        $class = $content_object::CONTEXT . '\ComplexContentObjectPath';

        return new $class($content_object);
    }

    /**
     * Follow a route through the ComplexContentObjectPath based on a set a sequential content object ids
     *
     * @param int $content_object_ids
     *
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
                }
            }

            if (!$child_found)
            {
                throw new Exception('NoMatchingPathFound');
            }
        }

        return $root_node;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     * @throws \Exception
     */
    protected function getDataClassRepositoryCache(): DataClassRepositoryCache
    {
        return $this->getService(DataClassRepositoryCache::class);
    }

    /**
     * @param string $serviceName
     *
     * @return object
     * @throws \Exception
     */
    protected function getService(string $serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    /**
     * @param int $node_id
     */
    public function get_children_by_id($node_id)
    {
        if (!isset($this->children[$node_id]))
        {
            $this->children[$node_id] = ComplexContentObjectPathNode::get_node_children($this->get_node($node_id));
        }

        return $this->children[$node_id];
    }

    /**
     * @return int
     */
    public function get_next_id()
    {
        return count($this->nodes) + 1;
    }

    /**
     * @param int $id
     *
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
     * @param int $node_id
     *
     * @return ComplexContentObjectPathNode
     * @throws Exception
     */
    public function get_node($node_id)
    {
        if (!isset($this->nodes[$node_id]))
        {
            throw new Exception(Translation::get('NodeDoesntExist'));
        }

        return $this->nodes[$node_id];
    }

    /**
     * @return ComplexContentObjectPathNode[]
     */
    public function get_nodes()
    {
        return $this->nodes;
    }

    /**
     * @param int $node_id
     * @param bool $include_self
     *
     * @return array<ComplexContentObjectPathNode>
     */
    public function get_parents_by_id($node_id, $include_self = false, $reverse = false)
    {
        if (!isset($this->parents[$node_id][$include_self][$reverse]))
        {
            $this->parents[$node_id][$include_self][$reverse] = ComplexContentObjectPathNode::get_node_parents(
                $this->get_node($node_id), $include_self, $reverse
            );
        }

        return $this->parents[$node_id][$include_self][$reverse];
    }

    /**
     * @param int $id
     *
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
     * @param int $parent_id
     * @param ComplexContentObjectItem $complex_content_object_item
     * @param ContentObject $content_object
     */
    public function get_properties($parent_id, $complex_content_object_item, $content_object)
    {
        return [];
    }

    /**
     * @return ComplexContentObjectPathNode
     * @throws Exception
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

        $this->nodes = [];
        $this->children = [];
        $this->parents = [];

        $this->getDataClassRepositoryCache()->truncateClass(ComplexContentObjectItem::class);

        $this->initialize($root);
    }
}
