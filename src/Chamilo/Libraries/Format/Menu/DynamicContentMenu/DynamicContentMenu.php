<?php
namespace Chamilo\Libraries\Format\Menu\DynamicContentMenu;

/**
 * This class describes a dynamic content menu.
 * This menu can have several items with content attached to it. All the
 * items (content) are (is) loaded at the same time. Only the content of the selected menu item is visible.
 *
 * @package Chamilo\Libraries\Format\Menu\DynamicContentMenu
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DynamicContentMenu
{

    /**
     * The name of the menu
     *
     * @var string
     */
    private $name;

    /**
     * The menu items
     *
     * @var \Chamilo\Libraries\Format\Menu\DynamicContentMenu\DynamicContentMenuItem[]
     */
    private $menu_items;

    /**
     * Constructor
     *
     * @param string $name
     * @param \Chamilo\Libraries\Format\Menu\DynamicContentMenu\DynamicContentMenuItem[] $menuItems
     */
    public function __construct($name, $menuItems = [])
    {
        $this->set_name($name);
        $this->set_menu_items($menuItems);
    }

    /**
     * Returns the menu as html
     *
     * @return string
     */
    public function render()
    {
        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->render_content();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Adds a menu item to the menu items list
     *
     * @param \Chamilo\Libraries\Format\Menu\DynamicContentMenu\DynamicContentMenuItem $menuItem
     */
    public function add_menu_item(DynamicContentMenuItem $menuItem)
    {
        $this->menu_items[] = $menuItem;
    }

    /**
     * Adds multiple menu items to the menu items list
     *
     * @param \Chamilo\Libraries\Format\Menu\DynamicContentMenu\DynamicContentMenuItem[] $menuItems
     */
    public function add_menu_items($menuItems)
    {
        foreach ($menuItems as $menuItem)
        {
            $this->add_menu_item($menuItem);
        }
    }

    /**
     *
     * @return string
     * @deprecated Use render() now
     */
    public function as_html()
    {
        return $this->render();
    }

    /**
     * Returns the menu items
     *
     * @return \Chamilo\Libraries\Format\Menu\DynamicContentMenu\DynamicContentMenuItem[]
     */
    public function get_menu_items()
    {
        return $this->menu_items;
    }

    /**
     * Sets the menu items
     *
     * @param \Chamilo\Libraries\Format\Menu\DynamicContentMenu\DynamicContentMenuItem[] $menuItems
     */
    public function set_menu_items($menuItems)
    {
        $this->menu_items = $menuItems;
    }

    /**
     * Returns the name of this menu
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Sets the name of this menu
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the selected dynamic content menu item
     *
     * @return \Chamilo\Libraries\Format\Menu\DynamicContentMenu\DynamicContentMenuItem
     */
    protected function get_selected_item()
    {
        foreach ($this->get_menu_items() as $menu_item)
        {
            if ($menu_item->is_selected())
            {
                return $menu_item;
            }
        }
    }

    /**
     * Removes a menu item form the menu items list
     *
     * @param integer $index
     */
    public function remove_menu_item($index)
    {
        array_splice($this->menu_items, $index, 1);
    }

    /**
     * Renders the content
     *
     * @return string
     */
    protected function render_content()
    {
        $html = [];

        foreach ($this->get_menu_items() as $menu_item)
        {
            $html[] = $menu_item->render_content();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the container footer
     *
     * @return string
     */
    protected function render_footer()
    {
        return '</div>';
    }

    /**
     * Renders the container header
     *
     * @return string
     */
    protected function render_header()
    {
        return '<div id="' . $this->get_name() . '" class="clearfix">';
    }

    /**
     * Truncates the menu item list
     */
    public function truncate_menu_items()
    {
        $this->menu_items = [];
    }
}
