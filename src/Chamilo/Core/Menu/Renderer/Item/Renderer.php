<?php
namespace Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
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
    use DependencyInjectionContainerTrait;

    /**
     *
     * @var \Chamilo\Core\Menu\Renderer\Menu\Renderer
     */
    private $menuRenderer;

    /**
     *
     * @var \Chamilo\Core\Menu\Storage\DataClass\Item
     */
    private $item;

    /**
     *
     * @var \Chamilo\Core\Menu\Renderer\Item\Renderer
     */
    private $parentRenderer;

    /**
     *
     * @param \Chamilo\Core\Menu\Renderer\Menu\Renderer $menuRenderer
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\Menu\Renderer\Menu\Renderer $parentRenderer
     */
    public function __construct($menuRenderer, $item, Renderer $parentRenderer = null)
    {
        $this->item = $item;
        $this->menuRenderer = $menuRenderer;
        $this->parentRenderer = $parentRenderer;
        
        $this->initializeContainer();
    }

    /**
     *
     * @param \Chamilo\Core\Menu\Renderer\Menu\Renderer $menuRenderer
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param Renderer $parentRenderer
     * @return Renderer
     */
    public static function factory($menuRenderer, $item, $parentRenderer = null)
    {
        $namespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname($item->get_type());
        $namespace = ClassnameUtilities :: getInstance()->getNamespaceParent($namespace, 2);

        $class = $namespace . '\Renderer\Item\\' .
             (string) StringUtilities :: getInstance()->createString($menuRenderer :: TYPE)->upperCamelize() . '\Item\\' .
             ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($item->get_type());

        return new $class($menuRenderer, $item, $parentRenderer);
    }

    /**
     *
     * @param \Chamilo\Core\Menu\Renderer\Menu\Renderer $menuRenderer
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param Renderer $parentRenderer
     * @return string
     */
    public static function toHtml($menuRenderer, $item, $parentRenderer = null)
    {
        return self :: factory($menuRenderer, $item, $parentRenderer)->render();
    }

    /**
     * Renders the menu
     *
     * @return string
     */
    abstract public function render();

    public function getItem()
    {
        return $this->item;
    }

    public function getMenuRenderer()
    {
        return $this->menuRenderer;
    }

    /**
     *
     * @return \Chamilo\Core\Menu\Renderer\Item\Renderer
     */
    public function getParentRenderer()
    {
        return $this->parentRenderer;
    }
}
