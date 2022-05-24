<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\Repository\ItemRepository;
use Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Service\DisplayOrderHandler;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Core\Menu\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemService
{
    const PARAM_DIRECTION_DOWN = 2;

    const PARAM_DIRECTION_UP = 1;

    /**
     * @var \Chamilo\Libraries\Storage\Service\DisplayOrderHandler
     */
    private $displayOrderHandler;

    /**
     * @var \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache
     */
    private $itemCacheProvider;

    /**
     *
     * @var \Chamilo\Core\Menu\Storage\Repository\ItemRepository
     */
    private $itemRepository;

    /**
     * @var \Chamilo\Libraries\Storage\DataClass\PropertyMapper
     */
    private $propertyMapper;

    /**
     * @var \Chamilo\Core\Menu\Service\RightsService
     */
    private $rightsService;

    /**
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @param \Chamilo\Core\Menu\Storage\Repository\ItemRepository $itemRepository
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param \Chamilo\Libraries\Storage\DataClass\PropertyMapper $propertyMapper
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Storage\Service\DisplayOrderHandler $displayOrderHandler
     * @param \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache $itemCacheProvider
     */
    public function __construct(
        ItemRepository $itemRepository, RightsService $rightsService, StringUtilities $stringUtilities,
        PropertyMapper $propertyMapper, Translator $translator, DisplayOrderHandler $displayOrderHandler,
        FilesystemCache $itemCacheProvider
    )
    {
        $this->itemRepository = $itemRepository;
        $this->rightsService = $rightsService;
        $this->stringUtilities = $stringUtilities;
        $this->propertyMapper = $propertyMapper;
        $this->translator = $translator;
        $this->displayOrderHandler = $displayOrderHandler;
        $this->itemCacheProvider = $itemCacheProvider;
    }

    /**
     *
     * @param integer $parentIdentifier
     *
     * @return integer
     */
    public function countItemsByParentIdentifier(int $parentIdentifier)
    {
        return $this->getItemRepository()->countItemsByParentIdentifier($parentIdentifier);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function createItem(Item $item)
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

        $this->getItemCacheProvider()->delete(ItemCacheService::KEY_ITEMS);

        return true;
    }

    /**
     * @param string $itemType
     * @param string[] $values
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    public function createItemForTypeFromValues(string $itemType, array $values)
    {
        $item = $this->getItemTypeInstance($itemType);

        foreach ($item->getDefaultPropertyNames() as $property)
        {
            if (isset($values[$property]))
            {
                $item->setDefaultProperty($property, $values[$property]);
            }
        }

        foreach ($item->getAdditionalPropertyNames() as $property)
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
            return false;
        }

        return $item;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle $itemTitle
     *
     * @return boolean
     * @throws \Exception
     */
    public function createItemTitle(ItemTitle $itemTitle)
    {
        if (!$this->getItemRepository()->createItemTitle($itemTitle))
        {
            return false;
        }

        $this->getItemCacheProvider()->delete(ItemCacheService::KEY_ITEM_TITLES);

        return true;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param string $isocode
     * @param string $title
     *
     * @return boolean
     * @throws \Exception
     */
    public function createItemTitleForItemFromParameters(Item $item, string $isocode, string $title)
    {
        $itemTitle = new ItemTitle();
        $itemTitle->setTitle($title);
        $itemTitle->setIsocode($isocode);
        $itemTitle->setItemId($item->getId());

        return $this->createItemTitle($itemTitle);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param string[] $values
     *
     * @return boolean
     * @throws \Exception
     */
    public function createItemTitlesForItemFromValues(Item $item, array $values)
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
     * @param string[] $values
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function createItemWithTitlesForTypeFromValues(string $itemType, array $values)
    {
        $item = $this->createItemForTypeFromValues($itemType, $values);

        if (!$item instanceof Item)
        {
            return false;
        }

        if (!$this->createItemTitlesForItemFromValues($item, $values))
        {
            return false;
        }

        return $item;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function deleteItem(Item $item)
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

        $this->getItemCacheProvider()->delete(ItemCacheService::KEY_ITEMS);

        return true;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function deleteItemChildren(Item $item)
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

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle $itemTitle
     *
     * @return boolean
     */
    public function deleteItemTitle(ItemTitle $itemTitle)
    {
        if (!$this->getItemRepository()->deleteItemTitle($itemTitle))
        {
            return false;
        }

        $this->getItemCacheProvider()->delete(ItemCacheService::KEY_ITEM_TITLES);

        return true;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function deleteItemTitlesForItem(Item $item)
    {
        if (!$this->getItemRepository()->deleteItemTitlesForItem($item))
        {
            return false;
        }

        $this->getItemCacheProvider()->delete(ItemCacheService::KEY_ITEM_TITLES);

        return true;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[] $itemTitles
     *
     * @return string
     */
    public function determineItemTitleForCurrentLanguage(array $itemTitles)
    {
        return $this->determineItemTitleForIsoCode($itemTitles, $this->getTranslator()->getLocale());
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[] $itemTitles
     * @param string $isoCode
     *
     * @return string
     */
    public function determineItemTitleForIsoCode(array $itemTitles, string $isoCode)
    {
        if (key_exists($isoCode, $itemTitles))
        {
            return $itemTitles[$isoCode]->getTitle();
        }
        else
        {
            $fallbackIsoCodes = $this->getTranslator()->getFallbackLocales();

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

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function doesItemHaveChildren(Item $item)
    {
        return $this->countItemsByParentIdentifier($item->getId()) > 0;
    }

    /**
     * @param integer $identifier
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     */
    public function findItemByIdentifier(int $identifier)
    {
        return $this->getItemRepository()->findItemByIdentifier($identifier);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[]
     */
    public function findItemTitles()
    {
        return $this->getItemRepository()->findItemTitles();
    }

    /**
     * @param integer $itemIdentifier
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[]
     */
    public function findItemTitlesByItemIdentifier(int $itemIdentifier)
    {
        return $this->getItemRepository()->findItemTitlesByItemIdentifier($itemIdentifier);
    }

    /**
     * @param integer $itemIdentifier
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[]
     */
    public function findItemTitlesByItemIdentifierIndexedByIsoCode(int $itemIdentifier)
    {
        return $this->getPropertyMapper()->mapDataClassByProperty(
            $this->findItemTitlesByItemIdentifier($itemIdentifier), ItemTitle::PROPERTY_ISOCODE
        );
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function findItems()
    {
        return $this->getItemRepository()->findItems();
    }

    /**
     * @param integer[] $identifiers
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function findItemsByIdentifiers(array $identifiers)
    {
        return $this->getItemRepository()->findItemsByIdentifiers($identifiers);
    }

    /**
     *
     * @param integer $parentIdentifier
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function findItemsByParentIdentifier(
        int $parentIdentifier, int $count = null, int $offset = null, ?OrderBy $orderBy = null
    )
    {
        return $this->getItemRepository()->findItemsByParentIdentifier(
            $parentIdentifier, $count, $offset, $orderBy
        );
    }

    /**
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\CategoryItem[]
     */
    public function findRootCategoryItems()
    {
        return $this->getItemRepository()->findRootCategoryItems();
    }

    /**
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function findRootItems()
    {
        return $this->findItemsByParentIdentifier(0);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Service\DisplayOrderHandler
     */
    public function getDisplayOrderHandler(): DisplayOrderHandler
    {
        return $this->displayOrderHandler;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Service\DisplayOrderHandler $displayOrderHandler
     */
    public function setDisplayOrderHandler(DisplayOrderHandler $displayOrderHandler): void
    {
        $this->displayOrderHandler = $displayOrderHandler;
    }

    /**
     * @return \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache
     */
    public function getItemCacheProvider(): FilesystemCache
    {
        return $this->itemCacheProvider;
    }

    /**
     * @param \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache $itemCacheProvider
     */
    public function setItemCacheProvider(FilesystemCache $itemCacheProvider): void
    {
        $this->itemCacheProvider = $itemCacheProvider;
    }

    /**
     *
     * @return \Chamilo\Core\Menu\Storage\Repository\ItemRepository
     */
    public function getItemRepository()
    {
        return $this->itemRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Menu\Storage\Repository\ItemRepository $itemRepository
     */
    public function setItemRepository(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function getItemTitleForCurrentLanguage(Item $item)
    {
        return $this->determineItemTitleForCurrentLanguage(
            $this->findItemTitlesByItemIdentifierIndexedByIsoCode($item->getId())
        );
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param string $isoCode
     *
     * @return string
     */
    public function getItemTitleForIsoCode(Item $item, string $isoCode)
    {
        return $this->determineItemTitleForIsoCode(
            $this->findItemTitlesByItemIdentifierIndexedByIsoCode($item->getId()), $isoCode
        );
    }

    /**
     * @param string $itemType
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     */
    public function getItemTypeInstance(string $itemType)
    {
        return new $itemType();
    }

    /**
     * @param integer $parentIdentifier
     *
     * @return integer
     */
    public function getNextItemSortValueByParentIdentifier(int $parentIdentifier)
    {
        return $this->getItemRepository()->getNextItemSortValueByParentIdentifier($parentIdentifier);
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
     * @return \Chamilo\Core\Menu\Service\RightsService
     */
    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService): void
    {
        $this->rightsService = $rightsService;
    }

    /**
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities): void
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param integer $moveDirection
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    public function moveItemInDirection(Item $item, int $moveDirection)
    {
        $newDisplayOrder = $item->getSort() + ($moveDirection == self::PARAM_DIRECTION_UP ? - 1 : 1);
        $item->setSort($newDisplayOrder);

        return $this->updateItem($item);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param string[] $values
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    public function saveItemFromValues(Item $item, array $values)
    {
        $parentHasChanged = $item->getParentId() != $values[Item::PROPERTY_PARENT];

        if ($parentHasChanged && !isset($values[Item::PROPERTY_SORT]))
        {
            $item->setSort(null);
        }

        foreach ($item->getDefaultPropertyNames() as $property)
        {
            if (isset($values[$property]))
            {
                $item->setDefaultProperty($property, $values[$property]);
            }
        }

        foreach ($item->getAdditionalPropertyNames() as $property)
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
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param string[] $values
     *
     * @return boolean
     * @throws \Exception
     */
    public function saveItemTitlesForItemFromValues(Item $item, array $values)
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
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param string[] $values
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function saveItemWithTitlesFromValues(Item $item, array $values)
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
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return bool
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    public function updateItem(Item $item)
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

        $this->getItemCacheProvider()->delete(ItemCacheService::KEY_ITEMS);

        return true;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle $itemTitle
     *
     * @return boolean
     */
    public function updateItemTitle(ItemTitle $itemTitle)
    {
        if (!$this->getItemRepository()->updateItemTitle($itemTitle))
        {
            return false;
        }

        $this->getItemCacheProvider()->delete(ItemCacheService::KEY_ITEM_TITLES);

        return true;
    }
}