<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Architecture\Interfaces\ItemServiceInterface;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\Repository\ItemRepository;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Service\DisplayOrderHandler;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemService implements ItemServiceInterface
{
    public const PARAM_DIRECTION_DOWN = 2;
    public const PARAM_DIRECTION_UP = 1;

    /**
     * @var string[]
     */
    protected array $fallbackIsoCodes;

    private DisplayOrderHandler $displayOrderHandler;

    private ItemRepository $itemRepository;

    private PropertyMapper $propertyMapper;

    private RightsService $rightsService;

    private StringUtilities $stringUtilities;

    private Translator $translator;

    public function __construct(
        ItemRepository $itemRepository, RightsService $rightsService, StringUtilities $stringUtilities,
        PropertyMapper $propertyMapper, Translator $translator, DisplayOrderHandler $displayOrderHandler,
        array $fallbackIsoCodes
    )
    {
        $this->itemRepository = $itemRepository;
        $this->rightsService = $rightsService;
        $this->stringUtilities = $stringUtilities;
        $this->propertyMapper = $propertyMapper;
        $this->translator = $translator;
        $this->displayOrderHandler = $displayOrderHandler;
        $this->fallbackIsoCodes = $fallbackIsoCodes;
    }

    public function countItemsByParentIdentifier(string $parentIdentifier): int
    {
        return $this->getItemRepository()->countItemsByParentIdentifier($parentIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function createItem(Item $item): bool
    {
        if (!$this->getDisplayOrderHandler()->handleDisplayOrderBeforeCreate($item))
        {
            return false;
        }

        if (!$this->getItemRepository()->createItem($item))
        {
            return false;
        }

        if (!$this->getRightsService()->createItemRightsLocationWithViewRightForEveryone($item))
        {
            return false;
        }

        return true;
    }

    /**
     * @param string[][] $values
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function createItemForTypeFromValues(string $itemType, array $values): ?Item
    {
        $item = $this->getItemTypeInstance($itemType);

        foreach ($item::getDefaultPropertyNames() as $property)
        {
            if (isset($values[$property]))
            {
                $item->setDefaultProperty($property, $values[$property]);
            }
        }

        foreach ($item::getAdditionalPropertyNames() as $property)
        {
            if (isset($values[$property]))
            {
                $item->setAdditionalProperty($property, $values[$property]);
            }
        }

        $item->setType($itemType);
        $item->setDisplay(Item::DISPLAY_BOTH);
        $item->setHidden();

        if (!$this->createItem($item))
        {
            return null;
        }

        return $item;
    }

    /**
     * @throws \Exception
     */
    public function createItemTitle(ItemTitle $itemTitle): bool
    {
        if (!$this->getItemRepository()->createItemTitle($itemTitle))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function createItemTitleForItemFromParameters(Item $item, string $isocode, string $title): bool
    {
        $itemTitle = new ItemTitle();
        $itemTitle->setTitle($title);
        $itemTitle->setIsocode($isocode);
        $itemTitle->setItemId($item->getId());

        return $this->createItemTitle($itemTitle);
    }

    /**
     * @param string[][] $values
     *
     * @throws \Exception
     */
    public function createItemTitlesForItemFromValues(Item $item, array $values): bool
    {
        foreach ($values[ItemTitle::PROPERTY_TITLE] as $isocode => $title)
        {
            if (!$this->getStringUtilities()->isNullOrEmpty($title, true))
            {
                if (!$this->createItemTitleForItemFromParameters($item, $isocode, $title))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param string $itemType
     * @param string[][] $values
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function createItemWithTitlesForTypeFromValues(string $itemType, array $values): ?Item
    {
        $item = $this->createItemForTypeFromValues($itemType, $values);

        if (!$item instanceof Item)
        {
            return null;
        }

        if (!$this->createItemTitlesForItemFromValues($item, $values))
        {
            return null;
        }

        return $item;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function deleteItem(Item $item): bool
    {
        if (!$this->deleteItemChildren($item))
        {
            return false;
        }

        if (!$this->getItemRepository()->deleteItem($item))
        {
            return false;
        }

        if (!$this->getDisplayOrderHandler()->handleDisplayOrderAfterDelete($item))
        {
            return false;
        }

        if (!$this->deleteItemTitlesForItem($item))
        {
            return false;
        }

        if (!$this->getRightsService()->deleteItemRightsLocation($item))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function deleteItemChildren(Item $item): bool
    {
        $itemChildren = $this->findItemsByParentIdentifier($item->getId());

        foreach ($itemChildren as $itemChild)
        {
            if (!$this->deleteItem($itemChild))
            {
                return false;
            }
        }

        return true;
    }

    public function deleteItemTitle(ItemTitle $itemTitle): bool
    {
        if (!$this->getItemRepository()->deleteItemTitle($itemTitle))
        {
            return false;
        }

        return true;
    }

    public function deleteItemTitlesForItem(Item $item): bool
    {
        if (!$this->getItemRepository()->deleteItemTitlesForItem($item))
        {
            return false;
        }

        return true;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[] $itemTitles
     */
    public function determineItemTitleForCurrentLanguage(array $itemTitles): string
    {
        return $this->determineItemTitleForIsoCode($itemTitles, $this->getTranslator()->getLocale());
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[] $itemTitles
     */
    public function determineItemTitleForIsoCode(array $itemTitles, string $isoCode): string
    {
        if (key_exists($isoCode, $itemTitles))
        {
            return $itemTitles[$isoCode]->getTitle();
        }
        else
        {
            $fallbackIsoCodes = $this->getFallbackIsoCodes();

            foreach ($fallbackIsoCodes as $fallbackIsoCode)
            {
                if (key_exists($fallbackIsoCode, $itemTitles))
                {
                    return $itemTitles[$fallbackIsoCode]->getTitle();
                }
            }
        }

        return $this->getTranslator()->trans('MenuItem', [], 'Chamilo\Core\Menu');
    }

    public function doesItemHaveChildren(Item $item): bool
    {
        return $this->countItemsByParentIdentifier($item->getId()) > 0;
    }

    public function findItemByIdentifier(string $identifier): ?Item
    {
        return $this->getItemRepository()->findItemByIdentifier($identifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\ItemTitle>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemTitles(): ArrayCollection
    {
        return $this->getItemRepository()->findItemTitles();
    }

    /**
     * @param string $itemIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\ItemTitle>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemTitlesByItemIdentifier(string $itemIdentifier): ArrayCollection
    {
        return $this->getItemRepository()->findItemTitlesByItemIdentifier($itemIdentifier);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemTitlesByItemIdentifierIndexedByIsoCode(string $itemIdentifier): array
    {
        return $this->getPropertyMapper()->mapDataClassByProperty(
            $this->findItemTitlesByItemIdentifier($itemIdentifier), ItemTitle::PROPERTY_ISOCODE
        );
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[][][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemTitlesGroupedByItemIdentifierAndIsocode(): array
    {
        $itemTitles = $this->findItemTitles();
        $groupedItemTitles = [];

        /**
         * @var \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[][] $itemTitlesGroupedByIdentifiers
         */
        $itemTitlesGroupedByIdentifiers =
            $this->getPropertyMapper()->groupDataClassByProperty($itemTitles, ItemTitle::PROPERTY_ITEM_ID);

        foreach ($itemTitlesGroupedByIdentifiers as $itemIdentifier => $itemTitlesGroupedByIdentifier)
        {
            foreach ($itemTitlesGroupedByIdentifier as $itemTitle)

            {
                if (!array_key_exists($itemIdentifier, $groupedItemTitles))
                {
                    $groupedItemTitles[$itemIdentifier] = [];
                }

                $groupedItemTitles[$itemIdentifier][$itemTitle->getIsocode()] = $itemTitle;
            }
        }

        return $groupedItemTitles;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItems(): ArrayCollection
    {
        return $this->getItemRepository()->findItems();
    }

    /**
     * @param string[] $identifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemsByIdentifiers(array $identifiers): ArrayCollection
    {
        return $this->getItemRepository()->findItemsByIdentifiers($identifiers);
    }

    /**
     * @param string $parentIdentifier
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemsByParentIdentifier(
        string $parentIdentifier, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getItemRepository()->findItemsByParentIdentifier(
            $parentIdentifier, $count, $offset, $orderBy
        );
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemsGroupedByParentIdentifier(): array
    {
        return $this->getPropertyMapper()->groupDataClassByProperty(
            $this->findItems(), Item::PROPERTY_PARENT
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\CategoryItem>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRootCategoryItems(): ArrayCollection
    {
        return $this->getItemRepository()->findRootCategoryItems();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRootItems(): ArrayCollection
    {
        return $this->findItemsByParentIdentifier('0');
    }

    /**
     * @return \Chamilo\Libraries\Storage\Service\DisplayOrderHandler
     */
    public function getDisplayOrderHandler(): DisplayOrderHandler
    {
        return $this->displayOrderHandler;
    }

    /**
     * @return string[]
     */
    protected function getFallbackIsoCodes(): array
    {
        return $this->fallbackIsoCodes;
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\Repository\ItemRepository
     */
    public function getItemRepository(): ItemRepository
    {
        return $this->itemRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getItemTitleForCurrentLanguage(Item $item): string
    {
        return $this->determineItemTitleForCurrentLanguage(
            $this->findItemTitlesByItemIdentifierIndexedByIsoCode($item->getId())
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getItemTitleForIsoCode(Item $item, string $isoCode): string
    {
        return $this->determineItemTitleForIsoCode(
            $this->findItemTitlesByItemIdentifierIndexedByIsoCode($item->getId()), $isoCode
        );
    }

    /**
     * @template getItemTypeInstance
     *
     * @param class-string<getItemTypeInstance> $itemType
     *
     * @return ?getItemTypeInstance
     */
    protected function getItemTypeInstance(string $itemType)
    {
        return new $itemType();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getNextItemSortValueByParentIdentifier(string $parentIdentifier): int
    {
        return $this->getItemRepository()->getNextItemSortValueByParentIdentifier($parentIdentifier);
    }

    public function getPropertyMapper(): PropertyMapper
    {
        return $this->propertyMapper;
    }

    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function moveItemInDirection(Item $item, int $moveDirection): bool
    {
        $newDisplayOrder = $item->getSort() + ($moveDirection == self::PARAM_DIRECTION_UP ? - 1 : 1);
        $item->setSort($newDisplayOrder);

        return $this->updateItem($item);
    }

    /**
     * @param string[][] $values
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function saveItemFromValues(Item $item, array $values): bool
    {
        $parentHasChanged = $item->getParentId() != $values[Item::PROPERTY_PARENT];

        if ($parentHasChanged && !isset($values[Item::PROPERTY_SORT]))
        {
            $item->setSort(null);
        }

        foreach ($item::getDefaultPropertyNames() as $property)
        {
            if (isset($values[$property]))
            {
                $item->setDefaultProperty($property, $values[$property]);
            }
        }

        foreach ($item::getAdditionalPropertyNames() as $property)
        {
            if (isset($values[$property]))
            {
                $item->setAdditionalProperty($property, $values[$property]);
            }
        }

        if (!$this->updateItem($item))
        {
            return false;
        }

        return true;
    }

    /**
     * @param string[][] $values
     *
     * @throws \Exception
     */
    public function saveItemTitlesForItemFromValues(Item $item, array $values): bool
    {
        $existingItemTitles = $this->findItemTitlesByItemIdentifierIndexedByIsoCode($item->getId());
        $valuesItemTitles = $values[ItemTitle::PROPERTY_TITLE];

        $existingIsoCodes = array_keys($existingItemTitles);
        $valuesIsoCodes = array_keys($valuesItemTitles);

        $isoCodesToDelete = array_diff($existingIsoCodes, $valuesIsoCodes);
        $isoCodesToAdd = array_diff($valuesIsoCodes, $existingIsoCodes);
        $isoCodesToUpdate = array_intersect($existingIsoCodes, $valuesIsoCodes);

        foreach ($isoCodesToDelete as $isoCodeToDelete)
        {
            if (!$this->deleteItemTitle($existingItemTitles[$isoCodeToDelete]))
            {
                return false;
            }
        }

        foreach ($isoCodesToAdd as $isoCodeToAdd)
        {
            if (!$this->createItemTitleForItemFromParameters($item, $isoCodeToAdd, $valuesItemTitles[$isoCodeToAdd]))
            {
                return false;
            }
        }

        foreach ($isoCodesToUpdate as $isoCodeToUpdate)
        {
            $itemTitle = $existingItemTitles[$isoCodeToUpdate];
            $itemTitle->setTitle($valuesItemTitles[$isoCodeToUpdate]);

            if (!$this->updateItemTitle($itemTitle))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string[][] $values
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function saveItemWithTitlesFromValues(Item $item, array $values): bool
    {
        if (!$this->saveItemFromValues($item, $values))
        {
            return false;
        }

        if (!$this->saveItemTitlesForItemFromValues($item, $values))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    public function updateItem(Item $item): bool
    {
        if (!$this->getDisplayOrderHandler()->handleDisplayOrderBeforeUpdate($item))
        {
            return false;
        }

        if (!$this->getItemRepository()->updateItem($item))
        {
            return false;
        }

        if (!$this->getRightsService()->moveItemRightsLocation($item))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateItemTitle(ItemTitle $itemTitle): bool
    {
        if (!$this->getItemRepository()->updateItemTitle($itemTitle))
        {
            return false;
        }

        return true;
    }
}