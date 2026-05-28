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
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Menu\UserInterface\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class ItemOptionsTreeDataProvider extends OptionsTreeDataProvider
{
    public function __construct(
        protected ItemService $itemService, protected CategoryItemRenderer $categoryItemRenderer,
        protected LanguageConsulter $languageConsulter, protected Translator $translator
    )
    {
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getChildDataClasses(string|Uuid $parentIdentifier): ArrayCollection
    {
        if ($parentIdentifier === DataClass::EMPTY_UUID) {
            return $this->itemService->findRootCategoryItems();
        }

        return new ArrayCollection();
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode[]
     */
    public function getData(string|Uuid|null $identifier, array $excludedIdentifiers = []): array
    {
        $getIdentifier = function (Item $item) {
            return $item->getId();
        };

        $getText = function (Item $item) {
            return $this->categoryItemRenderer->renderTitleForCurrentLanguage($item);
        };

        return [$this->__getData($getIdentifier, $getText, $identifier)];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getDataClassByIdentifier(string|Uuid $identifier): Item
    {
        return $this->itemService->findItemByIdentifier($identifier);
    }

    protected function getRootDataClass(): Item
    {
        $rootItem = new Item();
        $rootItem->setId(DataClass::EMPTY_UUID);

        foreach ($this->languageConsulter->getLanguages() as $isoCode => $languageName) {
            $rootItem->setTitleForIsoCode(
                $isoCode, $this->translator->trans('Home', [], Manager::CONTEXT, $isoCode)
            );
        }

        return $rootItem;
    }
}
