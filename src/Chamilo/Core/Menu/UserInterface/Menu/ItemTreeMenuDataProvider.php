<?php
namespace Chamilo\Core\Menu\UserInterface\Menu;

use Chamilo\Core\Admin\Service\Consulter\LanguageConsulter;
use Chamilo\Core\Menu\Implementation\Menu\CategoryItemRenderer;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Tree\Service\TreeMenuDataProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\UserInterface\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class ItemTreeMenuDataProvider extends TreeMenuDataProvider
{
    public function __construct(
        protected ItemService $itemService, protected CategoryItemRenderer $categoryItemRenderer,
        protected LanguageConsulter $languageConsulter, protected Translator $translator
    )
    {
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getChildDataClasses(string $parentIdentifier): ArrayCollection
    {
        if ($parentIdentifier === DataClass::EMPTY_UUID) {
            return $this->itemService->findRootCategoryItems();
        }

        return new ArrayCollection();
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode[]
     */
    public function getData(string $uriFormat, ?string $identifier): array
    {
        $getIdentifier = function (Item $item) {
            return $item->getId();
        };

        $getText = function (Item $item) {
            return $this->categoryItemRenderer->renderTitleForCurrentLanguage($item);
        };

        $hasChildren = function (Item $item) {
            return $this->itemService->countItemsByParentIdentifier($item->getId()) > 0;
        };

        return $this->__getData($uriFormat, $identifier, $getIdentifier, $getText, $hasChildren);
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
