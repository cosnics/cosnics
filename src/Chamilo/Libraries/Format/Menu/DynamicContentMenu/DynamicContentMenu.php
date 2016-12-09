<?php
namespace Chamilo\Libraries\Format\Menu\DynamicContentMenu;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 * This class describes a dynamic content menu.
 * This menu can have several items with content attached to it. All the
 * items (content) are (is) loaded at the same time. Only the content of the selected menu item is visible.
 * 
 * @package \libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DynamicContentMenu
{

    /**
     * The name of the menu
     * 
     * @var String
     */
    private $name;

    /**
     * The menu items
     * 
     * @var DynamicContentMenuItem[]
     */
    private $menu_items;

    /**
     * Constructor
     * 
     * @param $name String
     * @param $menu_items DynamicContentMenuItem[]
     */
    public function __construct($name, $menu_items = array())
    {
        $this->set_name($name);
        $this->set_menu_items($menu_items);
    }

    /**
     * **************************************************************************************************************
     * Render Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the menu as html
     * 
     * @return String
     */
    public function as_html()
    {
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->render_menu();
        $html[] = $this->render_content();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Renders the menu
     * 
     * @return String
     */
    protected function render_menu()
    {
        $html = array();
        
        $html[] = $this->render_menu_header();
        $html[] = '<ul class="dynamic_content_menu_list">';
        
        foreach ($this->menu_items as $menu_item)
        {
            $html[] = $menu_item->render_menu_item();
        }
        
        $html[] = '</ul>';
        $html[] = $this->render_small_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the content
     * 
     * @return String
     */
    protected function render_content()
    {
        $html = array();
        
        $html[] = $this->render_content_header();
        
        foreach ($this->get_menu_items() as $menu_item)
        {
            $html[] = $menu_item->render_content();
        }
        
        $html[] = $this->render_small_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the container header
     * 
     * @return String
     */
    protected function render_header()
    {
        return '<div id="' . $this->get_name() . '" class="dynamic_content_menu_container">';
    }

    /**
     * Renders the container footer
     * 
     * @return String
     */
    protected function render_footer()
    {
        $html = array();
        
        $html[] = '<div class="clear"></div></div>';
        
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'DynamicContentMenu.js');
        
        $selected_item = $this->get_selected_item();
        if ($selected_item)
        {
            $selected_item_id = $selected_item->get_id();
        }
        
        $html[] = '<script type="text/javascript">';
        $html[] = '	$(\'#' . $this->get_name() . '.dynamic_content_menu_container\').dynamicContentMenu({
				    selectedItemId: \'' . $selected_item_id . '\'});';
        $html[] = '</script>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders a small footer for the menu and content headers
     * 
     * @return String
     */
    protected function render_small_footer()
    {
        return '</div>';
    }

    /**
     * Renders the menu container header
     * 
     * @return String
     */
    protected function render_menu_header()
    {
        return '<div class="dynamic_content_menu_menu_container">';
    }

    /**
     * Renders the content container header
     * 
     * @return String
     */
    protected function render_content_header()
    {
        return '<div class="dynamic_content_menu_content_container">';
    }

    /**
     * Returns the selected dynamic content menu item
     * 
     * @return DynamicContentMenuItem
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
     * **************************************************************************************************************
     * List Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Adds a menu item to the menu items list
     * 
     * @param $menu_item DynamicContentMenuItem
     */
    public function add_menu_item(DynamicContentMenuItem $menu_item)
    {
        $this->menu_items[] = $menu_item;
    }

    /**
     * Adds multiple menu items to the menu items list
     * 
     * @param $menu_items DynamicContentMenuItem[]
     */
    public function add_menu_items($menu_items)
    {
        foreach ($menu_items as $menu_item)
        {
            $this->add_menu_item($menu_item);
        }
    }

    /**
     * Removes a menu item form the menu items list
     * 
     * @param $index int
     */
    public function remove_menu_item($index)
    {
        array_splice($this->menu_items, $index, 1);
    }

    /**
     * Truncates the menu item list
     */
    public function truncate_menu_items()
    {
        $this->menu_items = array();
    }

    /**
     * **************************************************************************************************************
     * Getters / Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the name of this menu
     * 
     * @return String
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Returns the menu items
     * 
     * @return DynamicContentMenuItem[]
     */
    public function get_menu_items()
    {
        return $this->menu_items;
    }

    /**
     * Sets the name of this menu
     * 
     * @param $name String
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * Sets the menu items
     * 
     * @param $menu_items DynamicContentMenuItem[]
     */
    public function set_menu_items($menu_items)
    {
        $this->menu_items = $menu_items;
    }
}
