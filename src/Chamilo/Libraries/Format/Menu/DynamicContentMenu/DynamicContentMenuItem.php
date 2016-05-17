<?php
namespace Chamilo\Libraries\Format\Menu\DynamicContentMenu;

/**
 * This class describes a dynamic content menu item.
 * 
 * @package \libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DynamicContentMenuItem
{

    /**
     * The id of the menu
     * 
     * @var String
     */
    private $id;

    /**
     * The name of the menu
     * 
     * @var String
     */
    private $name;

    /**
     * The image of the menu
     * 
     * @var String
     */
    private $image;

    /**
     * The function that renders the content
     * 
     * @var String[]
     */
    private $content_function;

    /**
     * The selected variable
     * 
     * @var boolean
     */
    private $selected;

    /**
     * Constructor
     * 
     * @param $id String
     * @param $name String
     * @param $image String
     */
    public function __construct($id, $name, $image, $content_function, $selected = false)
    {
        $this->set_id($id);
        $this->set_name($name);
        $this->set_image($image);
        $this->set_content_function($content_function);
        $this->set_selected($selected);
    }

    /**
     * **************************************************************************************************************
     * Render Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Renders the menu item
     * 
     * @return String
     */
    public function render_menu_item()
    {
        $html = array();
        
        $html[] = '<li id="' . $this->get_id() . '" class="dynamic_content_menu_item">';
        $html[] = $this->get_name();
        $html[] = '</li>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the menu content
     * 
     * @return String
     */
    public function render_content()
    {
        $html = array();
        
        $html[] = $this->render_content_header();
        $html[] = call_user_func($this->get_content_function(), $this);
        $html[] = $this->render_content_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Renders the header for the content box
     * 
     * @return String
     */
    protected function render_content_header()
    {
        $html = array();
        
        $html[] = '<div id="' . $this->get_id() . '" class="dynamic_content_menu_item_content">';
        $html[] = '<div class="dynamic_content_menu_item_content_header"><h3>' . $this->get_name() . '</h3></div>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the footer for the content box
     * 
     * @return String
     */
    protected function render_content_footer()
    {
        return '</div>';
    }

    /**
     * **************************************************************************************************************
     * Getters / Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the id of this menu item
     * 
     * @return String
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Returns the name of this menu item
     * 
     * @return String
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Returns the image of this menu item
     * 
     * @return String
     */
    public function get_image()
    {
        return $this->image;
    }

    /**
     * Returns the function that renders the content
     * 
     * @return String[]
     */
    public function get_content_function()
    {
        return $this->content_function;
    }

    /**
     * Returns the selected of this menu item
     * 
     * @return boolean
     */
    public function is_selected()
    {
        return $this->selected;
    }

    /**
     * Sets the id of this menu item
     * 
     * @param $id String
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * Sets the name of this menu item
     * 
     * @param $name String
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * Sets the image of this menu item
     * 
     * @param $image String
     */
    public function set_image($image)
    {
        $this->image = $image;
    }

    /**
     * Sets the content function that renders the content
     * 
     * @param $content_function String[]
     */
    public function set_content_function($content_function)
    {
        $this->content_function = $content_function;
    }

    /**
     * Sets the selected status of this menu item
     * 
     * @param $selected boolean
     */
    public function set_selected($selected)
    {
        $this->selected = $selected;
    }
}
