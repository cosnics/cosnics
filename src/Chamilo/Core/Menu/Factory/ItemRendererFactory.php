<?php
namespace Chamilo\Core\Menu\Factory;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;

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
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return \Chamilo\Core\Menu\Renderer\ItemRenderer
     */
    public function getItemRenderer(Item $item)
    {
        return $this->getService($this->determineItemRendererServiceName($item));
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function determineItemRendererServiceName(Item $item)
    {
        $itemNamespace = $this->getClassnameUtilities()->getNamespaceFromObject($item);
        $itemPackage = $this->getClassnameUtilities()->getNamespaceParent($itemNamespace, 2);
        $itemType = $this->getClassnameUtilities()->getClassnameFromObject($item);

        return $itemPackage . '\Renderer\Item\\' . $itemType . 'Renderer';
    }
}