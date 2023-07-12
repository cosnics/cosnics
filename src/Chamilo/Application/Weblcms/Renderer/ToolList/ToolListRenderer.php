<?php
namespace Chamilo\Application\Weblcms\Renderer\ToolList;

use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package Chamilo\Application\Weblcms\Renderer\ToolList
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
abstract class ToolListRenderer
{
    use DependencyInjectionContainerTrait;

    public const TYPE_FIXED = 'fixed_location';
    public const TYPE_MENU = 'menu';
    public const TYPE_SHORTCUT = 'shortcut';

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Manager
     */
    private $parent;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool[]
     */
    private $visible_tools;

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Manager $parent
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool[] $visible_tools
     *
     * @throws \Exception
     */
    public function __construct($parent, $visible_tools)
    {
        $this->parent = $parent;
        $this->visible_tools = $visible_tools;
    }

    /**
     * @param string $type
     * @param \Chamilo\Application\Weblcms\Tool\Manager $parent
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool[] $visible_tools
     *
     * @return \Chamilo\Application\Weblcms\Renderer\ToolList\ToolListRenderer
     * @throws Exception
     */
    public static function factory($type, $parent, $visible_tools = [])
    {
        $type = StringUtilities::getInstance()->createString($type)->upperCamelize()->__toString();
        $type .= 'ToolListRenderer';
        $class = __NAMESPACE__ . '\Type\\' . $type;

        if (!class_exists($class))
        {
            throw new Exception(Translation::get('CanNotLoadToolListRenderer'));
        }

        return new $class($parent, $visible_tools);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Manager
     */
    public function get_parent()
    {
        return $this->parent;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool[]
     */
    public function get_visible_tools()
    {
        return $this->visible_tools;
    }

    /**
     * Return the tool list as HTML
     */
    abstract public function toHtml();
}
