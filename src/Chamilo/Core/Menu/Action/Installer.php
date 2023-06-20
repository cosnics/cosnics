<?php
namespace Chamilo\Core\Menu\Action;

use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Service\RightsService;
use Chamilo\Core\Menu\Storage\DataClass\ApplicationItem;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Action
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * @var int
     */
    private $itemDisplay;

    /**
     * @var bool
     */
    private $needsCategory;

    /**
     * @param string[] $formValues
     * @param int $itemDisplay
     * @param bool $needsCategory
     */
    public function __construct($formValues, $itemDisplay = Item::DISPLAY_BOTH, $needsCategory = true)
    {
        parent::__construct($formValues);
        $this->itemDisplay = $itemDisplay;
        $this->needsCategory = $needsCategory;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function extra(): bool
    {
        $translator = $this->getTranslator();
        $context = $this->getClassnameUtilities()->getNamespaceParent(static::CONTEXT, 5);

        $item = new ApplicationItem();
        $item->setApplication($context);
        $item->setDisplay($this->getItemDisplay());
        $item->setParentId(0);
        $item->setUseTranslation(1);

        if (!$this->getItemService()->createItem($item))
        {
            return false;
        }

        $itemTitle = new ItemTitle();
        $itemTitle->setTitle($translator->trans('TypeName', null, $context));
        $itemTitle->setIsocode($translator->getLocale());
        $itemTitle->setItemId($item->getId());

        if (!$this->getItemService()->createItemTitle($itemTitle))
        {
            return false;
        }

        if (!$this->setDefaultRights($item))
        {
            return false;
        }

        return true;
    }

    /**
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    protected function getClassnameUtilities()
    {
        return $this->getContainer()->get(ClassnameUtilities::class);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer();
    }

    /**
     * @return int
     */
    public function getItemDisplay()
    {
        return $this->itemDisplay;
    }

    /**
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    protected function getItemService()
    {
        return $this->getContainer()->get(ItemService::class);
    }

    /**
     * @return bool
     */
    public function getNeedsCategory()
    {
        return $this->needsCategory;
    }

    /**
     * @return \Chamilo\Core\Menu\Service\RightsService
     */
    protected function getRightsService()
    {
        return $this->getContainer()->get(RightsService::class);
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    protected function getTranslator()
    {
        return $this->getContainer()->get(Translator::class);
    }

    /**
     * @return bool
     */
    public function isAvailableForEveryone()
    {
        return true;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return bool
     */
    public function setDefaultRights(Item $item)
    {
        $rightsService = $this->getRightsService();
        $rightsLocation = $rightsService->findRightsLocationForItem($item);

        if (!$this->isAvailableForEveryone())
        {
            if (!$rightsService->deleteViewRightForRightsLocationForEveryone($rightsLocation))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $itemDisplay
     */
    public function setItemDisplay($itemDisplay)
    {
        $this->itemDisplay = $itemDisplay;
    }

    /**
     * @param bool $needsCategory
     */
    public function setNeedsCategory($needsCategory)
    {
        $this->needsCategory = $needsCategory;
    }
}
