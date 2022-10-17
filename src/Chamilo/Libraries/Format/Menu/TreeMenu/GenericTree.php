<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;

/**
 * This class provides a navigation menu to allow a user to browse through categories of courses.
 *
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
 * @author Pieterjan Broekaert - Original Author
 * @author Sven Vanpoucke - Refactoring + Comments
 */
abstract class GenericTree extends HtmlMenu implements GenericTreeInterface
{
    const TREE_NAME = __CLASS__;

    /**
     * The id of the selected node
     *
     * @var integer
     */
    protected $current_node_id;

    /**
     * The treemenu in an array of Strings
     *
     * @var string[]
     */
    protected $tree;

    /**
     * A helper node to build the tree from the bottom up
     *
     * @var string[]
     */
    protected $active_tree_node;

    /**
     * Array of root ids in case the root nodes do not have the same parents.
     *
     * @var integer[]
     */
    protected $root_ids;

    /**
     * @var boolean
     */
    protected $include_fake_root;

    /**
     * Builds the treemenu
     *
     * @param boolean $includeFakeRoot
     * @param integer[] $rootIds
     */
    public function __construct($includeFakeRoot = true, $rootIds = [])
    {
        $this->root_ids = $rootIds;
        $this->current_node_id = $this->get_current_node_id();
        $this->tree = [];
        $this->include_fake_root = $includeFakeRoot;

        $this->build_tree();

        parent::__construct($this->tree);
    }

    public function build_tree()
    {
        /**
         * If a node is selected then the tree will be built from the selected item to the root items Otherwise the root
         * nodes will be displayed.
         */
        if (!$this->current_node_id || $this->current_node_id == $this->get_root_node_id() ||
            !$this->create_tree_recursive(
                $this->current_node_id
            ))
        {
            $this->create_tree_root_nodes();
        }

        if (isset($this->current_node_id))
        {
            $this->forceCurrentUrl($this->get_node_url($this->current_node_id));
        }

        /**
         * Include a fake tree root (for which there is no record in the database)
         */

        if ($this->include_fake_root)
        {
            $root_node = $this->create_tree_fake_root_node();

            // We need to do this because the children need to be moved below the fake tree node and the fake tree
            // node now must become the only first element
            $this->tree = array($root_node);

            // if (! $this->current_node_id)
            // {
            // $this->forceCurrentUrl($this->get_node_url(0));
            // }
        }
    }

    /**
     * Builds a fake tree root (for which there is no record in the database)
     *
     * @return string[]
     */
    public function create_tree_fake_root_node()
    {
        $tree_item = [];
        $tree_item['title'] = $this->get_root_node_title();
        $tree_item['url'] = $this->get_node_url($this->get_root_node_id());

        if (count($this->tree) > 0)
        {
            $tree_item['children'] = 'collapse';
            $tree_item['sub'] = $this->tree;
        }

        $tree_item['class'] = $this->get_root_node_class();
        $tree_item['id'] = $this->get_root_node_id();

        return $tree_item;
    }

    /**
     * Creates a tree menu item for a given node.
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $node
     *
     * @return string[]
     */
    public function create_tree_item_for_node($node)
    {
        $id = $this->get_node_id($node);

        if ($id == $this->active_tree_node['id'])
        {
            return $this->active_tree_node;
        }

        $tree_item = [];
        $tree_item['title'] = $this->get_node_title($node);
        $tree_item['safe_title'] = $this->get_node_safe_title($node);
        $tree_item['url'] = $this->get_node_url($id);

        if ($this->node_has_children($id))
        {
            $tree_item['children'] = 'expand';
        }

        $tree_item['class'] = $this->get_node_class($node);
        $tree_item[OptionsMenuRenderer::KEY_ID] = $id;

        return $tree_item;
    }

    /**
     * Creates the tree recursively starting from the bottom of the tree (with a given treenode)
     *
     * @param integer $nodeId
     *
     * @return boolean
     */
    public function create_tree_recursive($nodeId)
    {
        $node = $this->get_node($nodeId);
        if (!$node)
        {
            return false;
        }

        $tree_item = $this->create_tree_item_for_node($node);

        if ($tree_item['children'] == 'expand')
        {
            $sub = $this->retrieve_child_tree_items($nodeId);
            $tree_item['sub'] = $sub;
            $tree_item['children'] = 'collapse';
        }

        $this->active_tree_node = $tree_item;

        $parent = $this->get_node_parent($node);

        $is_empty = empty($this->root_ids);
        $is_not_null = ($parent == $this->get_root_node_id()) || is_null($parent);
        if (($is_empty && $is_not_null) || in_array($nodeId, $this->root_ids))
        {
            $this->create_tree_root_nodes();
        }
        else
        {
            $this->create_tree_recursive($parent);
        }

        return true;
    }

    /**
     * Creates the root nodes of the tree (not the fake root) If there are no root nodes given then the tree menu will
     * retrieve all the nodes with parent id 0
     */
    public function create_tree_root_nodes()
    {
        $root_ids = $this->root_ids;

        if (empty($root_ids))
        {
            $this->tree = $this->retrieve_child_tree_items($this->get_root_node_id());
        }
        else
        {
            foreach ($root_ids as $root_id)
            {
                $node = $this->get_node($root_id);
                $this->tree[$root_id] = $this->create_tree_item_for_node($node);
            }
        }
    }

    /**
     *
     * @return integer
     */
    public function get_root_node_id()
    {
        return 0;
    }

    /**
     * Returns the name of the tree
     *
     * @return string
     */
    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(get_called_class(), true);
    }

    /**
     * Renders the menu as a tree
     *
     * @return string
     */
    public function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name(), $this->get_search_url(), $this->get_url_format());
        $this->render($renderer, 'sitemap');

        return $renderer->toHtml();
    }

    /**
     * Retrieves the child items for a given node
     *
     * @param integer $parent_node_id
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function retrieve_child_tree_items($parent_node_id)
    {
        $child_nodes = $this->get_node_children($parent_node_id);
        $sub_tree = [];

        foreach($child_nodes as $child_node)
        {
            $id = $this->get_node_id($child_node);
            $sub_tree[$id] = $this->create_tree_item_for_node($child_node);
        }

        return $sub_tree;
    }
}
