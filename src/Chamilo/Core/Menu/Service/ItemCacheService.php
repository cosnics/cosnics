<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;

/**
 * @package Chamilo\Core\Menu\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemCacheService
{
    const KEY_ITEMS = 'items';
    const KEY_ITEM_TITLES = 'titles';

    /**
     * @var \Chamilo\Core\Menu\Service\ItemService
     */
    private $itemService;

    /**
     * @var \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache
     */
    private $cacheProvider;

    /**
     * @var \Chamilo\Libraries\Storage\DataClass\PropertyMapper
     */
    private $propertyMapper;

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache $cacheProvider
     * @param \Chamilo\Libraries\Storage\DataClass\PropertyMapper $propertyMapper
     */
    public function __construct(
        ItemService $itemService, FilesystemCache $cacheProvider, PropertyMapper $propertyMapper
    )
    {
        $this->itemService = $itemService;
        $this->cacheProvider = $cacheProvider;
        $this->propertyMapper = $propertyMapper;
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\PropertyMapper
     */
    public function getPropertyMapper(): PropertyMapper
    {
        return $this->propertyMapper;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\PropertyMapper $propertyMapper
     */
    public function setPropertyMapper(PropertyMapper $propertyMapper): void
    {
        $this->propertyMapper = $propertyMapper;
    }

    /**
     * @return \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache
     */
    public function getCacheProvider(): FilesystemCache
    {
        return $this->cacheProvider;
    }

    /**
     * @param \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache $cacheProvider
     */
    public function setCacheProvider(FilesystemCache $cacheProvider): void
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     */
    public function setItemService(ItemService $itemService): void
    {
        $this->itemService = $itemService;
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[][]
     */
    protected function findItemsGroupedByParentIdentifier()
    {
        return $this->getPropertyMapper()->groupDataClassByProperty(
            $this->getItemService()->findItems(), Item::PROPERTY_PARENT
        );
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[][]
     */
    protected function getItemsGroupedByParentIdentifier()
    {
        $cacheProvider = $this->getCacheProvider();

        if (!$cacheProvider->contains(self::KEY_ITEMS))
        {
            $cacheProvider->save(self::KEY_ITEMS, $this->findItemsGroupedByParentIdentifier());
        }

        return $cacheProvider->fetch(self::KEY_ITEMS);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function doesItemHaveChildren(Item $item)
    {
        $groupedItems = $this->getItemsGroupedByParentIdentifier();

        return array_key_exists($item->getId(), $groupedItems);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    /**
     * @param $parentIdentifier
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findItemsByParentIdentifier(int $parentIdentifier)
    {
        $groupedItems = $this->getItemsGroupedByParentIdentifier();
        $parentKeyExists = array_key_exists($parentIdentifier, $groupedItems);
        $parentIdentifierItems = $parentKeyExists ? $groupedItems[$parentIdentifier] : [];

        return new DataClassIterator(Item::class, $parentIdentifierItems);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[][][]
     */
    protected function findItemTitlesGroupedByItemIdentifierAndIsocode()
    {
        $itemTitles = $this->getItemService()->findItemTitles();
        $groupedItemTitles = [];

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
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[][][]
     */
    protected function getItemTitlesGroupedByItemIdentifierAndIsocode()
    {
        $cacheProvider = $this->getCacheProvider();

        if (!$cacheProvider->contains(self::KEY_ITEM_TITLES))
        {
            $cacheProvider->save(self::KEY_ITEM_TITLES, $this->findItemTitlesGroupedByItemIdentifierAndIsocode());
        }

        return $cacheProvider->fetch(self::KEY_ITEM_TITLES);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle|\Chamilo\Core\Menu\Storage\DataClass\ItemTitle[][]
     */
    protected function getItemTitles(Item $item)
    {
        $groupedItemTitles = $this->getItemTitlesGroupedByItemIdentifierAndIsocode();

        $itemKeyExists = array_key_exists($item->getId(), $groupedItemTitles);

        return $itemKeyExists ? $groupedItemTitles[$item->getId()] : [];
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function getItemTitleForCurrentLanguage(Item $item)
    {
        return $this->getItemService()->determineItemTitleForCurrentLanguage($this->getItemTitles($item));
    }

    /**
     * @return boolean
     */
    public function clear()
    {
        return $this->getCacheProvider()->deleteAll();
    }

    public function warmUp()
    {
        $this->getItemTitlesGroupedByItemIdentifierAndIsocode();
    }
}