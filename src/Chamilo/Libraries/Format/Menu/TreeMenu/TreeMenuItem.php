<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

class TreeMenuItem
{

    private $title;

    private $url;

    private $id;

    private $class;

    private $children = array();

    private $collapsed;

    public function __construct($title = null, $url = null, $id = null, $class = 'category', $collapsed = false)
    {
        $this->set_title($title);
        $this->set_url($url);
        $this->set_id($id);
        $this->set_class($class);
        $this->set_children(array());
        $this->set_collapsed($collapsed);
    }

    public function set_title($title)
    {
        $this->title = $title;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function set_url($url)
    {
        $this->url = $url;
    }

    public function get_url()
    {
        return $this->url;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_class($class)
    {
        $this->class = $class;
    }

    public function get_class()
    {
        return $this->class;
    }

    public function get_children()
    {
        return $this->children;
    }

    public function set_children($children)
    {
        $this->children = $children;
    }

    public function get_collapsed()
    {
        return $this->collapsed;
    }

    public function set_collapsed($collapsed)
    {
        $this->collapsed = $collapsed;
    }

    public function has_children()
    {
        if ($this->get_children())
        {
            return true;
        }
        return false;
    }

    public function add_child($tree_menu_child)
    {
        $this->children[] = $tree_menu_child;
    }

    public function remove_child($tree_menu_child)
    {
        foreach ($this->children as $key => $value)
        {
            if ($value == $tree_menu_child)
                unset($this->children[$key]);
        }
        $this->children = array_values($this->children);
    }

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
