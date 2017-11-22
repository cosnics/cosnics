<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

/**
 *
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
 */
class TreeMenuItem
{

    /**
     *
     * @var string
     */
    private $title;

    /**
     *
     * @var string
     */
    private $url;

    /**
     *
     * @var string
     */
    private $id;

    /**
     *
     * @var string
     */
    private $class;

    /**
     *
     * @var \Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuItem[]
     */
    private $children = array();

    /**
     *
     * @var boolean
     */
    private $collapsed;

    /**
     *
     * @param string $title
     * @param string $url
     * @param string $id
     * @param string $class
     * @param boolean $collapsed
     */
    public function __construct($title = null, $url = null, $id = null, $class = 'category', $collapsed = false)
    {
        $this->set_title($title);
        $this->set_url($url);
        $this->set_id($id);
        $this->set_class($class);
        $this->set_children(array());
        $this->set_collapsed($collapsed);
    }

    /**
     *
     * @param string $title
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     *
     * @param string $url
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @return string
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     *
     * @param string $id
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     *
     * @param string $class
     */
    public function set_class($class)
    {
        $this->class = $class;
    }

    /**
     *
     * @return string
     */
    public function get_class()
    {
        return $this->class;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuItem[]
     */
    public function get_children()
    {
        return $this->children;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuItem[] $children
     */
    public function set_children($children)
    {
        $this->children = $children;
    }

    /**
     *
     * @return boolean
     */
    public function get_collapsed()
    {
        return $this->collapsed;
    }

    /**
     *
     * @param boolean $collapsed
     */
    public function set_collapsed($collapsed)
    {
        $this->collapsed = $collapsed;
    }

    /**
     *
     * @return boolean
     */
    public function has_children()
    {
        if ($this->get_children())
        {
            return true;
        }
        return false;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuItem $tree_menu_child
     */
    public function add_child($tree_menu_child)
    {
        $this->children[] = $tree_menu_child;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuItem $tree_menu_child
     */
    public function remove_child($tree_menu_child)
    {
        foreach ($this->children as $key => $value)
        {
            if ($value == $tree_menu_child)
            {
                unset($this->children[$key]);
            }
        }

        $this->children = array_values($this->children);
    }

    /**
     *
     * @return string[][]
     */
    public function to_array()
    {
        $array = array();
        $array['title'] = $this->get_title();
        $array['url'] = $this->get_url();
        $array['id'] = $this->get_id();
        $array['class'] = $this->get_class();
        $array['collapsed'] = $this->get_collapsed();

        $children = array();

        if ($this->has_children())
        {
            foreach ($this->get_children() as $child)
            {
                $children[] = $child->to_array();
            }

            $array['sub'] = $children;
        }

        return $array;
    }
}
