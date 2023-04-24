<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Architecture\Interfaces\ItemServiceInterface;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Core\Menu\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CachedItemService implements CacheDataPreLoaderInterface, ItemServiceInterface
{
    use CacheAdapterHandlerTrait;

    public const KEY_ITEMS = 'items';
    public const KEY_ITEM_TITLES = 'titles';

    private ItemService $itemService;

    private PropertyMapper $propertyMapper;

    public function __construct(
        ItemService $itemService, AdapterInterface $cacheAdapter, PropertyMapper $propertyMapper
    )
    {
        $this->itemService = $itemService;
        $this->cacheAdapter = $cacheAdapter;
        $this->propertyMapper = $propertyMapper;
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[][][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function __findItemTitlesGroupedByItemIdentifierAndIsocode(): array
    {
        return $this->getItemService()->findItemTitlesGroupedByItemIdentifierAndIsocode();
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function __findItemsGroupedByParentIdentifier(): array
    {
        return $this->getItemService()->findItemsGroupedByParentIdentifier();
    }

    public function countItemsByParentIdentifier(string $parentIdentifier): int
    {
        return $this->getItemService()->countItemsByParentIdentifier($parentIdentifier);
    }

    public function createItem(Item $item): bool
    {
        if (!$this->getItemService()->createItem($item))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]);
    }

    public function createItemForTypeFromValues(string $itemType, array $values): ?Item
    {
        $item = $this->getItemService()->createItemForTypeFromValues($itemType, $values);

        if (!$item || !$this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]))
        {
            return null;
        }

        return $item;
    }

    public function createItemTitle(ItemTitle $itemTitle): bool
    {
        if (!$this->getItemService()->createItemTitle($itemTitle))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEM_TITLES]);
    }

    public function createItemTitleForItemFromParameters(Item $item, string $isocode, string $title): bool
    {
        if (!$this->getItemService()->createItemTitleForItemFromParameters($item, $isocode, $title))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEM_TITLES]);
    }

    public function createItemTitlesForItemFromValues(Item $item, array $values): bool
    {
        if (!$this->getItemService()->createItemTitlesForItemFromValues($item, $values))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEM_TITLES]);
    }

    public function createItemWithTitlesForTypeFromValues(string $itemType, array $values): ?Item
    {
        $item = $this->getItemService()->createItemWithTitlesForTypeFromValues($itemType, $values);

        if (!$item || !$this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]) ||
            !$this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEM_TITLES]))
        {
            return null;
        }

        return $item;
    }

    public function deleteItem(Item $item): bool
    {
        if (!$this->getItemService()->deleteItem($item))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function deleteItemChildren(Item $item): bool
    {
        if (!$this->getItemService()->deleteItemChildren($item))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function deleteItemTitle(ItemTitle $itemTitle): bool
    {
        if (!$this->getItemService()->deleteItemTitle($itemTitle))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEM_TITLES]);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function deleteItemTitlesForItem(Item $item): bool
    {
        if (!$this->getItemService()->deleteItemTitlesForItem($item))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEM_TITLES]);
    }

    public function determineItemTitleForCurrentLanguage(array $itemTitles): string
    {
        return $this->getItemService()->determineItemTitleForCurrentLanguage($itemTitles);
    }

    public function determineItemTitleForIsoCode(array $itemTitles, string $isoCode): string
    {
        return $this->getItemService()->determineItemTitleForIsoCode($itemTitles, $isoCode);
    }

    public function doesItemHaveChildren(Item $item): bool
    {
        $groupedItems = $this->findItemsGroupedByParentIdentifier();

        return array_key_exists($item->getId(), $groupedItems);
    }

    public function findItemByIdentifier(string $identifier): ?Item
    {
        return $this->getItemService()->findItemByIdentifier($identifier);
    }

    public function findItemTitles(): ArrayCollection
    {
        return $this->getItemService()->findItemTitles();
    }

    public function findItemTitlesByItemIdentifier(string $itemIdentifier): ArrayCollection
    {
        return $this->getItemService()->findItemTitlesByItemIdentifier($itemIdentifier);
    }

    public function findItemTitlesByItemIdentifierIndexedByIsoCode(string $itemIdentifier): array
    {
        return $this->getItemService()->findItemTitlesByItemIdentifierIndexedByIsoCode($itemIdentifier);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[][]
     */
    public function findItemTitlesGroupedByItemIdentifierAndIsocode(): array
    {
        try
        {
            return $this->loadCacheDataForKeyParts([__CLASS__, self::KEY_ITEM_TITLES],
                [$this, '__findItemTitlesGroupedByItemIdentifierAndIsocode']);
        }
        catch (CacheException $e)
        {
            return [];
        }
    }

    public function findItems(): ArrayCollection
    {
        return $this->getItemService()->findItems();
    }

    public function findItemsByIdentifiers(array $identifiers): ArrayCollection
    {
        return $this->getItemService()->findItemsByIdentifiers($identifiers);
    }

    /**
     * @param string $parentIdentifier
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     */
    public function findItemsByParentIdentifier(
        string $parentIdentifier, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $groupedItems = $this->findItemsGroupedByParentIdentifier();
        $parentKeyExists = array_key_exists($parentIdentifier, $groupedItems);
        $parentIdentifierItems = $parentKeyExists ? $groupedItems[$parentIdentifier] : [];

        return new ArrayCollection($parentIdentifierItems);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[][]
     */
    public function findItemsGroupedByParentIdentifier(): array
    {
        try
        {
            return $this->loadCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS],
                [$this, '__findItemsGroupedByParentIdentifier']);
        }
        catch (CacheException $e)
        {
            return [];
        }
    }

    public function findRootCategoryItems(): ArrayCollection
    {
        return $this->getItemService()->findRootCategoryItems();
    }

    public function findRootItems(): ArrayCollection
    {
        return $this->findItemsByParentIdentifier('0');
    }

    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function getItemTitleForCurrentLanguage(Item $item): string
    {
        return $this->getItemService()->determineItemTitleForCurrentLanguage($this->getItemTitles($item));
    }

    public function getItemTitleForIsoCode(Item $item, string $isoCode): string
    {
        return $this->getItemService()->getItemTitleForIsoCode($item, $isoCode);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[]
     */
    protected function getItemTitles(Item $item): array
    {
        $groupedItemTitles = $this->findItemTitlesGroupedByItemIdentifierAndIsocode();

        $itemKeyExists = array_key_exists($item->getId(), $groupedItemTitles);

        return $itemKeyExists ? $groupedItemTitles[$item->getId()] : [];
    }

    public function getNextItemSortValueByParentIdentifier(string $parentIdentifier): int
    {
        return $this->getItemService()->getNextItemSortValueByParentIdentifier($parentIdentifier);
    }

    public function getPropertyMapper(): PropertyMapper
    {
        return $this->propertyMapper;
    }

    public function preLoadCacheData()
    {
        $this->findItemTitlesGroupedByItemIdentifierAndIsocode();
        $this->findItemsGroupedByParentIdentifier();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    public function moveItemInDirection(Item $item, int $moveDirection): bool
    {
        if (!$this->getItemService() - $this->moveItemInDirection($item, $moveDirection))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    public function saveItemFromValues(Item $item, array $values): bool
    {
        if (!$this->getItemService() - $this->saveItemFromValues($item, $values))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]);
    }

    public function saveItemTitlesForItemFromValues(Item $item, array $values): bool
    {
        if (!$this->getItemService() - $this->saveItemTitlesForItemFromValues($item, $values))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEM_TITLES]);
    }

    public function saveItemWithTitlesFromValues(Item $item, array $values): bool
    {
        if (!$this->getItemService() - $this->saveItemWithTitlesFromValues($item, $values))
        {
            return false;
        }

        if (!$this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEM_TITLES]))
        {
            return false;
        }

        if (!$this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    public function updateItem(Item $item): bool
    {
        if (!$this->getItemService() - $this->updateItem($item))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function updateItemTitle(ItemTitle $itemTitle): bool
    {
        if (!$this->getItemService() - $this->updateItemTitle($itemTitle))
        {
            return false;
        }

        return $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEM_TITLES]);
    }
}