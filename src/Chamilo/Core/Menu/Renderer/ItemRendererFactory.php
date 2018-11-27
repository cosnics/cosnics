<?php
namespace Chamilo\Core\Menu\Renderer;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

/**
 * @package Chamilo\Core\Menu\Renderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemRendererFactory
{
    use DependencyInjectionContainerTrait;

    /**
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilities;

    /**
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function __construct(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;

        $this->initializeContainer();
    }

    /**
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities): void
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     * @param string $menuRendererClassname
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return \Chamilo\Core\Menu\Renderer\ItemRenderer
     */
    public function getItemRenderer(string $menuRendererClassname, Item $item)
    {
        return $this->getService($this->determineItemRendererServiceName($menuRendererClassname, $item));
    }

    /**
     * @param string $menuRendererClassname
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function determineItemRendererServiceName(string $menuRendererClassname, Item $item)
    {
        $menuRendererType = $this->getClassnameUtilities()->getClassnameFromNamespace($menuRendererClassname);

        $itemNamespace = $this->getClassnameUtilities()->getNamespaceFromObject($item);
        $itemPackage = $this->getClassnameUtilities()->getNamespaceParent($itemNamespace, 2);
        $itemType = $this->getClassnameUtilities()->getClassnameFromObject($item);

        return $itemPackage . '\Renderer\\' . $menuRendererType . '\\' . $itemType . 'Renderer';
    }
}