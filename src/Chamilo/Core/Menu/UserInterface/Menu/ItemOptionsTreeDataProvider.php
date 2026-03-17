<?php
namespace Chamilo\Core\Menu\UserInterface\Menu;

use Chamilo\Core\Admin\Service\Consulter\LanguageConsulter;
use Chamilo\Core\Menu\Implementation\Menu\CategoryItemRenderer;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeDataProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\UserInterface\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemOptionsTreeDataProvider extends OptionsTreeDataProvider
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
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getChildDataClasses(string $parentIdentifier): ArrayCollection
    {
        if ($parentIdentifier === DataClass::EMPTY_UUID)
        {
            return $this->getItemService()->findRootCategoryItems();
        }

        return new ArrayCollection();
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode[]
     */
    public function getData(?string $identifier, array $excludedIdentifiers = []): array
    {
        $getIdentifier = function (Item $item) {
            return $item->getId();
        };

        $getText = function (Item $item) {
            return $this->getCategoryItemRenderer()->renderTitleForCurrentLanguage($item);
        };

        return [$this->__getData($getIdentifier, $getText, $identifier)];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getDataClassByIdentifier(string $identifier): Item
    {
        return $this->getItemService()->findItemByIdentifier($identifier);
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
        $rootItem->setId(DataClass::EMPTY_UUID);

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
