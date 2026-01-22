<?php
namespace Chamilo\Core\Menu\UserInterface\Menu;

use Chamilo\Configuration\Service\Consulter\LanguageConsulter;
use Chamilo\Core\Menu\Implementation\Menu\CategoryItemRenderer;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuDataProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\UserInterface\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTreeMenuDataProvider extends TreeMenuDataProvider
{
    protected CategoryItemRenderer $categoryItemRenderer;

    protected ItemService $itemService;

    protected LanguageConsulter $languageConsulter;

    protected Translator $translator;

    public function __construct(
        ItemService $itemService, CategoryItemRenderer $categoryItemRenderer, LanguageConsulter $languageConsulter,
        Translator $translator
    )
    {
        $this->itemService = $itemService;
        $this->categoryItemRenderer = $categoryItemRenderer;
        $this->languageConsulter = $languageConsulter;
        $this->translator = $translator;
    }

    public function getCategoryItemRenderer(): CategoryItemRenderer
    {
        return $this->categoryItemRenderer;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getChildDataClasses(string $parentIdentifier): ArrayCollection
    {
        if ($parentIdentifier === '0')
        {
            return $this->getItemService()->findRootCategoryItems();
        }

        return new ArrayCollection();
    }

    /**
     * @return \Chamilo\Libraries\Format\Menu\TreeMenu\TreeNode[]
     */
    public function getData(string $uriFormat, ?string $identifier): array
    {
        $getIdentifier = function (Item $item) {
            return $item->getId();
        };

        $getText = function (Item $item) {
            return $this->getCategoryItemRenderer()->renderTitleForCurrentLanguage($item);
        };

        $hasChildren = function (Item $item) {
            return $this->getItemService()->countItemsByParentIdentifier($item->getId()) > 0;
        };

        return $this->__getData($uriFormat, $identifier, $getIdentifier, $getText, $hasChildren);
    }

    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->languageConsulter;
    }

    protected function getRootDataClass(): Item
    {
        $rootItem = new Item();
        $rootItem->setId('0');

        foreach ($this->getLanguageConsulter()->getLanguages() as $isoCode => $languageName)
        {
            $rootItem->setTitleForIsoCode(
                $isoCode, $this->getTranslator()->trans('Home', [], Manager::CONTEXT, $isoCode)
            );
        }

        return $rootItem;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

}
