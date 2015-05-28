<?php
namespace Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Renderer
{

    private $menu_renderer;

    private $item;

    /**
     *
     * @param User|null $user
     */
    public function __construct($menu_renderer, $item)
    {
        $this->item = $item;
        $this->menu_renderer = $menu_renderer;
    }

    public static function factory($menu_renderer, $item)
    {
        $namespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname($item->get_type());
        $namespace = ClassnameUtilities :: getInstance()->getNamespaceParent($namespace, 2);

        $class = $namespace . '\Renderer\Item\\' .
             (string) StringUtilities :: getInstance()->createString($menu_renderer :: TYPE)->upperCamelize() . '\Item\\' .
             ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($item->get_type());

        return new $class($menu_renderer, $item);
    }

    public static function as_html($menu_renderer, $item)
    {
        return self :: factory($menu_renderer, $item)->render();
    }

    /**
     * Renders the menu
     *
     * @return string
     */
    abstract public function render();

    public function get_item()
    {
        return $this->item;
    }

    public function get_menu_renderer()
    {
        return $this->menu_renderer;
    }
}
