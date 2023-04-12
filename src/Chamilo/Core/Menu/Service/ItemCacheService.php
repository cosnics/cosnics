<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Core\Menu\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemCacheService
{
    public const KEY_ITEMS = 'items';
    public const KEY_ITEM_TITLES = 'titles';

    private AdapterInterface $cacheAdapter;

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

    public function clear(): bool
    {
        return $this->getCacheAdapter()->clear();
    }

    public function doesItemHaveChildren(Item $item): bool
    {
        $groupedItems = $this->getItemsGroupedByParentIdentifier();

        return array_key_exists($item->getId(), $groupedItems);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[][][]
     */
    protected function findItemTitlesGroupedByItemIdentifierAndIsocode(): array
    {
        $itemTitles = $this->getItemService()->findItemTitles();
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
     * @param int $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     */
    public function findItemsByParentIdentifier(int $parentIdentifier): ArrayCollection
    {
        $groupedItems = $this->getItemsGroupedByParentIdentifier();
        $parentKeyExists = array_key_exists($parentIdentifier, $groupedItems);
        $parentIdentifierItems = $parentKeyExists ? $groupedItems[$parentIdentifier] : [];

        return new ArrayCollection($parentIdentifierItems);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[][]
     */
    protected function findItemsGroupedByParentIdentifier(): array
    {
        return $this->getPropertyMapper()->groupDataClassByProperty(
            $this->getItemService()->findItems(), Item::PROPERTY_PARENT
        );
    }

    public function getCacheAdapter(): AdapterInterface
    {
        return $this->cacheAdapter;
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

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[]
     */
    protected function getItemTitles(Item $item): array
    {
        $groupedItemTitles = $this->getItemTitlesGroupedByItemIdentifierAndIsocode();

        $itemKeyExists = array_key_exists($item->getId(), $groupedItemTitles);

        return $itemKeyExists ? $groupedItemTitles[$item->getId()] : [];
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[][]
     */
    protected function getItemTitlesGroupedByItemIdentifierAndIsocode(): array
    {
        $cacheAdapter = $this->getCacheAdapter();

        try
        {
            $cacheItem = $cacheAdapter->getItem(self::KEY_ITEM_TITLES);

            if (!$cacheItem->isHit())
            {
                $cacheItem->set($this->findItemTitlesGroupedByItemIdentifierAndIsocode());
                $cacheAdapter->save($cacheItem);
            }

            return $cacheItem->get();
        }
        catch (InvalidArgumentException $e)
        {
            return [];
        }
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[][]
     */
    protected function getItemsGroupedByParentIdentifier(): array
    {
        $cacheAdapter = $this->getCacheAdapter();

        try
        {
            $cacheItem = $cacheAdapter->getItem(self::KEY_ITEMS);

            if (!$cacheItem->isHit())
            {
                $cacheItem->set($this->findItemsGroupedByParentIdentifier());
                $cacheAdapter->save($cacheItem);
            }

            return $cacheItem->get();
        }
        catch (InvalidArgumentException $e)
        {
            return [];
        }
    }

    public function getPropertyMapper(): PropertyMapper
    {
        return $this->propertyMapper;
    }

    public function setCacheAdapter(AdapterInterface $cacheAdapter): void
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    public function setItemService(ItemService $itemService): void
    {
        $this->itemService = $itemService;
    }

    public function setPropertyMapper(PropertyMapper $propertyMapper): void
    {
        $this->propertyMapper = $propertyMapper;
    }

    public function warmUp()
    {
        $this->getItemTitlesGroupedByItemIdentifierAndIsocode();
    }
}