<?php
namespace Chamilo\Application\Weblcms\Renderer\ToolList;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * $Id: tool_list_renderer.class.php 218 2009-11-13 14:21:26Z kariboe $
 *
 * @package application.lib.weblcms
 */
/**
 * Renderer to display a set of tools
 */
abstract class ToolListRenderer
{
    const TYPE_MENU = 'menu';
    const TYPE_SHORTCUT = 'shortcut';
    const TYPE_FIXED = 'fixed_location';

    /**
     * The parent application
     */
    private $parent;

    /**
     * The visible tools
     *
     * @var Array Of Strings
     */
    private $visible_tools;

    /**
     * Constructor
     *
     * @param $parent WebLcms The parent application
     */
    public function __construct($parent, $visible_tools)
    {
        $this->parent = $parent;
        $this->visible_tools = $visible_tools;
    }

    /**
     * Create a new tool list renderer
     *
     * @param $class string The implementation of this abstract class to load
     * @param $parent WebLcms The parent application
     */
    public static function factory($type, $parent, $visible_tools = array())
    {
        $type .= '_tool_list_renderer';
        $class = __NAMESPACE__ . '\Type\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize();

        if (! class_exists($class))
        {
            throw new Exception(Translation :: get('CanNotLoadToolListRenderer'));
        }

        return new $class($parent, $visible_tools);
    }

    /**
     * Gets the parent application
     *
     * @return WebLcms
     */
    public function get_parent()
    {
        return $this->parent;
    }

    public function get_visible_tools()
    {
        return $this->visible_tools;
    }

    /**
     * Return the tool list as HTML
     */
    abstract public function toHtml();
}
