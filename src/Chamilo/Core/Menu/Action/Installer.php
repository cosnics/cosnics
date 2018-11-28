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
 *
 * @package Chamilo\Core\Menu\Action
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     *
     * @var integer
     */
    private $itemDisplay;

    /**
     *
     * @var boolean
     */
    private $needsCategory;

    /**
     *
     * @param string[] $formValues
     * @param integer $itemDisplay
     * @param boolean $needsCategory
     */
    public function __construct($formValues, $itemDisplay = Item::DISPLAY_BOTH, $needsCategory = true)
    {
        parent::__construct($formValues);
        $this->itemDisplay = $itemDisplay;
        $this->needsCategory = $needsCategory;
    }

    /**
     *
     * @return integer
     */
    public function getItemDisplay()
    {
        return $this->itemDisplay;
    }

    /**
     *
     * @param integer $itemDisplay
     */
    public function setItemDisplay($itemDisplay)
    {
        $this->itemDisplay = $itemDisplay;
    }

    /**
     *
     * @return boolean
     */
    public function getNeedsCategory()
    {
        return $this->needsCategory;
    }

    /**
     *
     * @param boolean $needsCategory
     */
    public function setNeedsCategory($needsCategory)
    {
        $this->needsCategory = $needsCategory;
    }

    /**
     * @return boolean
     * @throws \Exception
     */
    public function extra()
    {
        $translator = $this->getTranslator();
        $context = $this->getClassnameUtilities()->getNamespaceParent($this->context(), 5);

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
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
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
     *
     * @return boolean
     */
    public function isAvailableForEveryone()
    {
        return true;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer();
    }

    /**
     * @return \Chamilo\Core\Menu\Service\RightsService
     */
    protected function getRightsService()
    {
        return $this->getContainer()->get(RightsService::class);
    }

    /**
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    protected function getItemService()
    {
        return $this->getContainer()->get(ItemService::class);
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    protected function getTranslator()
    {
        return $this->getContainer()->get(Translator::class);
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    protected function getClassnameUtilities()
    {
        return $this->getContainer()->get(ClassnameUtilities::class);
    }
}
