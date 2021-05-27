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
     * @var string
     */
    private $id;

    /**
     * The name of the menu
     *
     * @var string
     */
    private $name;

    /**
     * The image of the menu
     *
     * @var string
     */
    private $image;

    /**
     * The function that renders the content
     *
     * @var string[]
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
     * @param string $id
     * @param string $name
     * @param string $image
     * @param string[] $content_function
     * @param boolean $selected
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
     * Returns the function that renders the content
     *
     * @return string[]
     */
    public function get_content_function()
    {
        return $this->content_function;
    }

    /**
     * Sets the content function that renders the content
     *
     * @param string[] $content_function
     */
    public function set_content_function($content_function)
    {
        $this->content_function = $content_function;
    }

    /**
     * Returns the id of this menu item
     *
     * @return string
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * **************************************************************************************************************
     * Getters / Setters *
     * **************************************************************************************************************
     */

    /**
     * Sets the id of this menu item
     *
     * @param string $id
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the image of this menu item
     *
     * @return string
     */
    public function get_image()
    {
        return $this->image;
    }

    /**
     * Sets the image of this menu item
     *
     * @param string $image
     */
    public function set_image($image)
    {
        $this->image = $image;
    }

    /**
     * Returns the name of this menu item
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Sets the name of this menu item
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
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
     * Sets the selected status of this menu item
     *
     * @param boolean $selected
     */
    public function set_selected($selected)
    {
        $this->selected = $selected;
    }

    /**
     * Renders the menu content
     *
     * @return string
     */
    public function render_content()
    {
        $html = [];

        $html[] = $this->render_content_header();
        $html[] = call_user_func($this->get_content_function(), $this);

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the header for the content box
     *
     * @return string
     */
    protected function render_content_header()
    {
        return '<h4>' . $this->get_name() . '</h4>';
    }
}
