<?php
namespace Chamilo\Core\Repository\Common\Path;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;

/**
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ComplexContentObjectPathNode
{

    /**
     * @var \core\repository\common\path\ComplexContentObjectPathNode[]
     */
    private $children;

    /**
     * @var \core\repository\storage\data_class\ComplexContentObjectItem
     */
    private $complex_content_object_item;

    /**
     * @var \core\repository\ContentObject
     */
    private $content_object;

    /**
     * @var \core\repository\common\path\ComplexContentObjectPathNode[]
     */
    private $descendants;

    /**
     * @var int[]
     */
    private $descendants_content_object_ids;

    /**
     * @var int
     */
    private $id;

    /**
     * @var \core\repository\common\path\ComplexContentObjectPathNode
     */
    private $next_sibling;

    /**
     * @var int
     */
    private $next_sibling_id;

    /**
     * @var int
     */
    private $parent_id;

    /**
     * @var \core\repository\common\path\ComplexContentObjectPathNode[]
     */
    private $parents;

    /**
     * @var int[]
     */
    private $parents_content_object_ids;

    /**
     * @var \core\repository\common\path\ComplexContentObjectPathNode
     */
    private $previous_sibling;

    /**
     * @var int
     */
    private $previous_sibling_id;

    private $properties;

    /**
     * @var \core\repository\common\path\ComplexContentObjectPathNode[]
     */
    private $siblings;

    /**
     * @var \core\repository\common\path\ComplexContentObjectPath
     */
    private $tree;

    /**
     * @param \core\repository\common\path\ComplexContentObjectPath $tree
     * @param int $id
     * @param int $parent
     * @param int $previous_sibling_id
     * @param int $next_sibling_id
     * @param \core\repository\storage\data_class\ComplexContentObjectItem $complex_content_object_item
     * @param \core\repository\ContentObject $content_object
     * @param $properties
     */
    public function __construct(
        $tree, $id, $parent_id, $previous_sibling_id, $next_sibling_id,
        ComplexContentObjectItem $complex_content_object_item, ContentObject $content_object, $properties = []
    )
    {
        $this->tree = $tree;
        $this->id = $id;
        $this->parent_id = $parent_id;
        $this->previous_sibling_id = $previous_sibling_id;
        $this->next_sibling_id = $next_sibling_id;
        $this->complex_content_object_item = $complex_content_object_item;
        $this->content_object = $content_object;
        $this->properties = $properties;
    }

    /**
     * @param string $type
     * @param \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath $tree
     * @param int $id
     * @param int $parent
     * @param int $previous_sibling_id
     * @param int $next_sibling_id
     * @param \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem $complex_content_object_item
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object
     * @param $properties
     *
     * @return ComplexContentObjectPathNode
     */
    public static function factory(
        $type, $tree, $id, $parent_id, $previous_sibling_id, $next_sibling_id,
        ComplexContentObjectItem $complex_content_object_item, ContentObject $content_object, $properties = []
    )
    {
        $class = $type . '\ComplexContentObjectPathNode';

        return new $class(
            $tree, $id, $parent_id, $previous_sibling_id, $next_sibling_id, $complex_content_object_item,
            $content_object, $properties
        );
    }

    /**
     * @param int $content_object_id
     *
     * @return bool
     */
    public function forms_cycle_with($content_object_id)
    {
        $content_object = DataManager::retrieve_by_id(
            ContentObject::class, $content_object_id
        );

        if ($content_object->is_complex_content_object())
        {
            $parent_ids = $this->get_parents_content_object_ids(true);
            $content_object_descendant_ids =
                $content_object->get_complex_content_object_path()->get_root()->get_descendants_content_object_ids();
            $content_object_descendant_ids[] =
                $content_object->get_complex_content_object_path()->get_root()->get_content_object()->getId();

            return count(array_intersect($content_object_descendant_ids, $parent_ids)) > 0;
        }
        else
        {
            return false;
        }
    }

    /**
     * @return \core\repository\common\path\ComplexContentObjectPathNode[]
     */
    public function get_children()
    {
        if (!isset($this->children))
        {
            $this->children = self::get_node_children($this);
        }

        return $this->children;
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem
     */
    public function get_complex_content_object_item()
    {
        return $this->complex_content_object_item;
    }

    /**
     * @return ContentObject
     */
    public function get_content_object()
    {
        return $this->content_object;
    }

    /**
     * @return \core\repository\common\path\ComplexContentObjectPathNode[]
     */
    public function get_descendants()
    {
        if (!isset($this->descendants))
        {
            $this->descendants = self::get_node_descendants($this);
        }

        return $this->descendants;
    }

    public function get_descendants_content_object_ids()
    {
        if (!isset($this->descendants_content_object_ids))
        {
            $descendants_content_object_ids = [];

            foreach ($this->get_descendants() as $descendant_node)
            {
                $descendants_content_object_ids[] = $descendant_node->get_content_object()->get_id();
            }

            $this->descendants_content_object_ids = $descendants_content_object_ids;
        }

        return $this->descendants_content_object_ids;
    }

    /**
     * @param bool $include_self
     * @param bool $reverse
     *
     * @return string
     */
    public function get_fully_qualified_name($include_self = true, $reverse = false)
    {
        $parent_nodes = $this->get_parents($include_self, $reverse);

        $node_names = [];

        foreach ($parent_nodes as $parent_node)
        {
            $node_names[] = $parent_node->get_content_object()->get_title();
        }

        return implode(' > ', $node_names);
    }

    /**
     * Get a unique hash based on the unique structural path of the node within the path
     *
     * @return string
     */
    public function get_hash()
    {
        return md5(serialize($this->get_parents_content_object_ids(true, true)));
    }

    /**
     * @return int
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @return NULL,\repository\ComplexContentObjectPathNode
     */
    public function get_next()
    {
        $next = null;

        if ($this->has_children())
        {
            $next = array_shift($this->get_children());
        }

        if (!$next instanceof ComplexContentObjectPathNode)
        {
            $next = $this->get_next_sibling();
        }

        if (!$next instanceof ComplexContentObjectPathNode && !$this->is_root())
        {
            $parent = $this->get_parent();

            while (!$parent->is_root() && !$parent->get_next_sibling() instanceof ComplexContentObjectPathNode)
            {
                $parent = $parent->get_parent();
            }

            $next = $parent->get_next_sibling();
        }

        return $next;
    }

    /**
     * @return NULL,\repository\ComplexContentObjectPathNode
     */
    public function get_next_sibling()
    {
        if (!$this->get_next_sibling_id())
        {
            return null;
        }
        else
        {
            return $this->get_tree()->get_node($this->get_next_sibling_id());
        }
    }

    /**
     * @return int
     */
    public function get_next_sibling_id()
    {
        return $this->next_sibling_id;
    }

    /**
     * **************************************************************************************************************
     * Properties functionality
     * *************************************************************************************************************
     */

    /**
     * @param \core\repository\common\path\ComplexContentObjectPathNode $parent
     *
     * @return \core\repository\common\path\ComplexContentObjectPathNode[]
     */
    public static function get_node_children(ComplexContentObjectPathNode $parent)
    {
        $children = [];

        foreach ($parent->get_tree()->get_nodes() as $node)
        {
            if ($node->get_parent_id() == $parent->get_id())
            {
                $children[] = $node;
            }
        }

        return $children;
    }

    /**
     * @param \core\repository\common\path\ComplexContentObjectPathNode $parent
     *
     * @return \core\repository\common\path\ComplexContentObjectPathNode[]
     */
    public static function get_node_descendants(ComplexContentObjectPathNode $parent)
    {
        $descendants = [];

        foreach ($parent->get_children() as $child_node)
        {
            $descendants[] = $child_node;
            if ($child_node->has_children())
            {
                $descendants = array_merge($descendants, self::get_node_descendants($child_node));
            }
        }

        return $descendants;
    }

    /**
     * @param \core\repository\common\path\ComplexContentObjectPathNode $node
     * @param bool $include_self
     * @param bool $reverse
     *
     * @return \core\repository\common\path\ComplexContentObjectPathNode[]
     */
    public static function get_node_parents(ComplexContentObjectPathNode $node, $include_self = false, $reverse = false)
    {
        $parents = [];

        if ($include_self)
        {
            $parents[] = $node;
        }

        while ($node->get_parent_id() !== 0)
        {
            $node = $node->get_tree()->get_node($node->get_parent_id());
            $parents[] = $node;
        }

        if ($reverse)
        {
            krsort($parents);
        }

        return $parents;
    }

    /**
     * @return NULL,\repository\ComplexContentObjectPathNode
     */
    public function get_parent()
    {
        if ($this->is_root())
        {
            return null;
        }
        else
        {
            return $this->get_tree()->get_node($this->get_parent_id());
        }
    }

    /**
     * @return int
     */
    public function get_parent_id()
    {
        return $this->parent_id;
    }

    /**
     * @param bool $include_self
     * @param bool $reverse
     *
     * @return \core\repository\common\path\ComplexContentObjectPathNode[]
     */
    public function get_parents($include_self = false, $reverse = false)
    {
        if (!isset($this->parents[$include_self][$reverse]))
        {
            $this->parents[$include_self][$reverse] = self::get_node_parents($this, $include_self, $reverse);
        }

        return $this->parents[$include_self][$reverse];
    }

    /**
     * @param bool $include_self
     * @param bool $reverse
     *
     * @return int[]
     */
    public function get_parents_content_object_ids($include_self = false, $reverse = false)
    {
        if (!isset($this->parents_content_object_ids[$include_self][$reverse]))
        {
            $parent_nodes = $this->get_parents($include_self, $reverse);
            $parents_content_object_ids = [];

            foreach ($parent_nodes as $parent_node)
            {
                $parents_content_object_ids[] = $parent_node->get_content_object()->get_id();
            }

            $this->parents_content_object_ids[$include_self][$reverse] = $parents_content_object_ids;
        }

        return $this->parents_content_object_ids[$include_self][$reverse];
    }

    /**
     * @return NULL,\repository\ComplexContentObjectPathNode
     */
    public function get_previous()
    {
        $previous = null;

        $previous_sibling = $this->get_previous_sibling();

        if ($previous_sibling instanceof ComplexContentObjectPathNode)
        {
            if ($previous_sibling->has_children())
            {
                while ($previous_sibling->has_children())
                {
                    $previous_sibling = array_pop($previous_sibling->get_children());
                }
            }

            $previous = $previous_sibling;
        }
        else
        {
            $previous = $this->get_parent();
        }

        return $previous;
    }

    /**
     * @return NULL,\repository\ComplexContentObjectPathNode
     */
    public function get_previous_sibling()
    {
        if (!$this->get_previous_sibling_id())
        {
            return null;
        }
        else
        {
            return $this->get_tree()->get_node($this->get_previous_sibling_id());
        }
    }

    /**
     * @return int
     */
    public function get_previous_sibling_id()
    {
        return $this->previous_sibling_id;
    }

    /**
     * Gets the additional properties of this ComplexContentObjectPathNode
     */
    public function get_properties()
    {
        return $this->properties;
    }

    /**
     * Gets a additional property of this ComplexContentObjectPathNode object by name.
     *
     * @param $name string The name of the property.
     */
    public function get_property($name)
    {
        return (isset($this->properties) && array_key_exists($name, $this->properties)) ? $this->properties[$name] :
            null;
    }

    /**
     * @return \core\repository\common\path\ComplexContentObjectPathNode[]
     */
    public function get_siblings()
    {
        if (!isset($this->siblings))
        {
            $children = $this->get_parent()->get_children();
            $this->siblings = [];

            foreach ($children as $child)
            {
                if ($child->get_id() != $this->get_id())
                {
                    $this->siblings[] = $child;
                }
            }
        }

        return $this->siblings;
    }

    /**
     * @return \core\repository\common\path\ComplexContentObjectPath
     */
    public function get_tree()
    {
        return $this->tree;
    }

    /**
     * Get the type of the node expressed as the namespace of the ContentObject it represents
     *
     * @return string
     */
    public function get_type()
    {
        return $this->get_content_object()::CONTEXT;
    }

    /**
     * @return bool
     */
    public function has_children()
    {
        return count($this->get_children()) > 0;
    }

    /**
     * @return bool
     */
    public function has_siblings()
    {
        return count($this->get_parent()->get_children()) > 1;
    }

    /**
     * @return bool
     */
    public function is_first_child()
    {
        return is_null($this->get_previous_sibling_id());
    }

    /**
     * @return bool
     */
    public function is_last_child()
    {
        return is_null($this->get_next_sibling_id());
    }

    /**
     * @return bool
     */
    public function is_root()
    {
        return $this->get_parent_id() == 0;
    }

    // /**
    // *
    // * @return NULL,\repository\ComplexContentObjectPathNode
    // */
    // public function get_next()
    // {
    // if ($this->get_id() >= $this->get_tree()->count_nodes())
    // {
    // return null;
    // }
    // else
    // {
    // return $this->get_tree()->get_node($this->get_id() + 1);
    // }
    // }

    // /**
    // *
    // * @return NULL,\repository\ComplexContentObjectPathNode
    // */
    // public function get_previous()
    // {
    // if ($this->get_id() <= 1)
    // {
    // return null;
    // }
    // else
    // {
    // return $this->get_tree()->get_node($this->get_id() - 1);
    // }
    // }

    /**
     * @param \core\repository\storage\data_class\ComplexContentObjectItem $complex_content_object_item
     */
    public function set_complex_content_object_item(ComplexContentObjectItem $complex_content_object_item)
    {
        $this->complex_content_object_item = $complex_content_object_item;
    }

    /**
     * @param \core\repository\ContentObject $content_object
     */
    public function set_content_object(ContentObject $content_object)
    {
        $this->content_object = $content_object;
    }

    /**
     * @param int $id
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $next_sibling_id
     */
    public function set_next_sibling_id($next_sibling_id)
    {
        $this->next_sibling_id = $next_sibling_id;
    }

    /**
     * @param int $parent
     */
    public function set_parent_id($parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @param int $previous_sibling_id
     */
    public function set_previous_sibling_id($previous_sibling_id)
    {
        $this->previous_sibling_id = $previous_sibling_id;
    }

    /**
     * Sets the additional properties of this ComplexContentObjectPathNode
     *
     * @param $properties
     */
    public function set_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * Sets a additional property of this ComplexContentObjectPathNode by name
     *
     * @param string $name
     * @param mixed $value
     */
    public function set_property($name, $value)
    {
        $this->properties[$name] = $value;
    }
}
