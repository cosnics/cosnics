<?php
namespace Chamilo\Core\Menu\Package;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Service\RightsService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\DataClass\LanguageCategoryItem;
use Chamilo\Core\Menu\Storage\DataClass\RightsLocation;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * @return bool
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function extra()
    {
        $location = $this->getRightsService()->createRoot(true);

        if (!$location instanceof RightsLocation)
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, $this->getTranslator()->trans(
                'ObjectCreated', ['OBJECT' => $this->getTranslator()->trans('RightsTree', [], 'Chamilo\Core\Menu')],
                StringUtilities::LIBRARIES
            )
            );
        }

        $languageItem = new LanguageCategoryItem();
        $languageItem->setDisplay(Item::DISPLAY_BOTH);

        if (!$this->getItemService()->createItem($languageItem))
        {
            return false;
        }
        else
        {
            $itemTitle = new ItemTitle();
            $itemTitle->setTitle($this->getTranslator()->trans('ChangeLanguage', [], 'Chamilo\Core\Menu'));
            $itemTitle->setIsocode($this->getTranslator()->getLocale());
            $itemTitle->setItemId($languageItem->getId());

            if (!$this->getItemService()->createItemTitle($itemTitle))
            {
                return false;
            }
        }

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
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    protected function getItemService()
    {
        return $this->getContainer()->get(ItemService::class);
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
}
