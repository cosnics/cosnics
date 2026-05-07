<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Architecture\Interface\ItemServiceInterface;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Storage\Architecture\Trait\SingleCacheAdapterHandlerTrait;
use Chamilo\Libraries\Storage\Service\PropertyMapper;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Core\Menu\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CachedItemService implements CacheDataPreLoaderInterface, ItemServiceInterface
{
    use SingleCacheAdapterHandlerTrait;

    public const string KEY_ITEMS = 'items';

    public function __construct(
        protected readonly ItemService $itemService, protected readonly AdapterInterface $cacheAdapter,
        protected readonly PropertyMapper $propertyMapper
    )
    {
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[][]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function __findItemsGroupedByParentIdentifier(): array
    {
        return $this->itemService->findItemsGroupedByParentIdentifier();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countItemsByParentIdentifier(string $parentIdentifier): int
    {
        return $this->itemService->countItemsByParentIdentifier($parentIdentifier);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createItem(Item $item): void
    {
        $this->itemService->createItem($item);
        $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createItemForTypeFromValues(string $itemType, array $values): ?Item
    {
        $item = $this->itemService->createItemForTypeFromValues($itemType, $values);

        if (!$item) {
            return null;
        }

        if (!$this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS])) {
            return null;
        }

        return $item;
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteItem(Item $item): void
    {
        $this->itemService->deleteItem($item);
        $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function deleteItemChildren(Item $item): void
    {
        $this->itemService->deleteItemChildren($item);
        $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]);
    }

    public function doesItemHaveChildren(Item $item): bool
    {
        $groupedItems = $this->findItemsGroupedByParentIdentifier();

        return array_key_exists($item->getId(), $groupedItems);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findApplicationItems(): ArrayCollection
    {
        return $this->itemService->findApplicationItems();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findItemByIdentifier(string $identifier): ?Item
    {
        return $this->itemService->findItemByIdentifier($identifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findItems(): ArrayCollection
    {
        return $this->itemService->findItems();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findItemsByIdentifiers(array $identifiers): ArrayCollection
    {
        return $this->itemService->findItemsByIdentifiers($identifiers);
    }

    /**
     * @param string $parentIdentifier
     * @param ?int $count
     * @param ?int $offset
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     */
    public function findItemsByParentIdentifier(
        string $parentIdentifier, ?int $count = null, ?int $offset = null, OrderBy $orderBy = new OrderBy()
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
        try {
            return $this->loadCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS],
                [$this, '__findItemsGroupedByParentIdentifier']);
        }
        catch (CacheException) {
            return [];
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findRootCategoryItems(): ArrayCollection
    {
        return $this->itemService->findRootCategoryItems();
    }

    public function findRootItems(): ArrayCollection
    {
        return $this->findItemsByParentIdentifier(DataClass::EMPTY_UUID);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getNextItemSortValueByParentIdentifier(string $parentIdentifier): int
    {
        return $this->itemService->getNextItemSortValueByParentIdentifier($parentIdentifier);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function moveItemInDirection(Item $item, int $moveDirection): void
    {
        $this->itemService->moveItemInDirection($item, $moveDirection);
        $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]);
    }

    public function preLoadCacheData(): array
    {
        return $this->findItemsGroupedByParentIdentifier();
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function saveItemFromValues(Item $item, array $values): void
    {
        $this->itemService->saveItemFromValues($item, $values);
        $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     */
    public function updateItem(Item $item): void
    {
        $this->itemService->$this->updateItem($item);
        $this->clearCacheDataForKeyParts([__CLASS__, self::KEY_ITEMS]);
    }
}