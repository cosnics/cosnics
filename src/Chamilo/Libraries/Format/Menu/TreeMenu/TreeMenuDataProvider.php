<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

abstract class TreeMenuDataProvider
{

    private $selected_tree_menu_item;

    private $url;

    public function __construct($url, $selected_tree_menu_item)
    {
        $this->set_url($url);
        $this->set_selected_tree_menu_item($selected_tree_menu_item);
    }

    public function get_selected_tree_menu_item()
    {
        return $this->selected_tree_menu_item;
    }

    public function set_selected_tree_menu_item($selected_tree_menu_item)
    {
        $this->selected_tree_menu_item = $selected_tree_menu_item;
    }

    public function get_selected_tree_menu_item_url()
    {
        return $this->format_url($this->get_selected_tree_menu_item());
    }

    public function get_url()
    {
        return $this->url;
    }

    public function set_url($url)
    {
        $this->url = $url;
    }

    public function format_url($id)
    {
        return $this->get_url() . '&' . $this->get_id_param() . '=' . $id;
    }

    abstract public function get_tree_menu_data();

    abstract public function get_id_param();
}
