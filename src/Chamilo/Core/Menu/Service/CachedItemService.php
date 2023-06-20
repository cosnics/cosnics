<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Architecture\Interfaces\ItemServiceInterface;
use Chamilo\Core\Menu\Storage\DataClass\Item;
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

    public function doesItemHaveChildren(Item $item): bool
    {
        $groupedItems = $this->findItemsGroupedByParentIdentifier();

        return array_key_exists($item->getId(), $groupedItems);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findApplicationItems(): ArrayCollection
    {
        return $this->getItemService()->findApplicationItems();
    }

    public function findItemByIdentifier(string $identifier): ?Item
    {
        return $this->getItemService()->findItemByIdentifier($identifier);
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

    public function getNextItemSortValueByParentIdentifier(string $parentIdentifier): int
    {
        return $this->getItemService()->getNextItemSortValueByParentIdentifier($parentIdentifier);
    }

    public function getPropertyMapper(): PropertyMapper
    {
        return $this->propertyMapper;
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

    public function preLoadCacheData(): mixed
    {
        $this->findItemsGroupedByParentIdentifier();
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
}