<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

/**
 *
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
 */
abstract class TreeMenuDataProvider
{

    /**
     *
     * @var integer
     */
    private $selected_tree_menu_item;

    /**
     *
     * @var string
     */
    private $url;

    /**
     *
     * @param string $url
     * @param integer $selectedTreeMenuItem
     */
    public function __construct($url, $selectedTreeMenuItem)
    {
        $this->set_url($url);
        $this->set_selected_tree_menu_item($selectedTreeMenuItem);
    }

    /**
     *
     * @param integer $id
     *
     * @return string
     */
    public function format_url($id)
    {
        return $this->get_url() . '&' . $this->get_id_param() . '=' . $id;
    }

    /**
     *
     * @return string
     */
    abstract public function get_id_param();

    /**
     *
     * @return integer
     */
    public function get_selected_tree_menu_item()
    {
        return $this->selected_tree_menu_item;
    }

    /**
     *
     * @param integer $selectedTreeMenuItem
     */
    public function set_selected_tree_menu_item($selectedTreeMenuItem)
    {
        $this->selected_tree_menu_item = $selectedTreeMenuItem;
    }

    /**
     *
     * @return string
     */
    public function get_selected_tree_menu_item_url()
    {
        return $this->format_url($this->get_selected_tree_menu_item());
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuItem
     */
    abstract public function get_tree_menu_data();

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
     * @param string $url
     */
    public function set_url($url)
    {
        $this->url = $url;
    }
}
