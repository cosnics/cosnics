<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\Repository\ItemRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Core\Menu\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemService
{

    /**
     *
     * @var \Chamilo\Core\Menu\Storage\Repository\ItemRepository
     */
    private $itemRepository;

    /**
     * @var \Chamilo\Core\Menu\Service\RightsService
     */
    private $rightsService;

    /**
     * @var \Chamilo\Core\Menu\Service\ItemsCacheService
     */
    private $itemsCacheService;

    /**
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     * @var \Chamilo\Libraries\Storage\DataClass\PropertyMapper
     */
    private $propertyMapper;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @param \Chamilo\Core\Menu\Storage\Repository\ItemRepository $itemRepository
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     * @param \Chamilo\Core\Menu\Service\ItemsCacheService $itemsCacheService
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param \Chamilo\Libraries\Storage\DataClass\PropertyMapper $propertyMapper
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        ItemRepository $itemRepository, RightsService $rightsService, ItemsCacheService $itemsCacheService,
        StringUtilities $stringUtilities, PropertyMapper $propertyMapper, Translator $translator
    )
    {
        $this->itemRepository = $itemRepository;
        $this->rightsService = $rightsService;
        $this->itemsCacheService = $itemsCacheService;
        $this->stringUtilities = $stringUtilities;
        $this->propertyMapper = $propertyMapper;
        $this->translator = $translator;
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
     *
     * @return boolean
     */
    public function createItem(Item $item)
    {
        return $this->getItemRepository()->createItem($item);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function updateItem(Item $item)
    {
        return $this->getItemRepository()->updateItem($item);
    }

    /**
     * @param string $itemType
     * @param array $values
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     */
    public function createItemForTypeFromValues(string $itemType, array $values)
    {
        $item = $this->getItemTypeInstance($itemType);

        foreach ($item->get_default_property_names() as $property)
        {
            if (isset($values[$property]))
            {
                $item->set_default_property($property, $values[$property]);
            }
        }

        foreach ($item->get_additional_property_names() as $property)
        {
            if (isset($values[$property]))
            {
                $item->set_additional_property($property, $values[$property]);
            }
        }

        $item->set_type($itemType);
        $item->set_display(Item::DISPLAY_BOTH);
        $item->set_hidden();
        $item->set_sort($this->getNextItemSortValueByParentIdentifier($item->get_parent()));

        if (!$this->createItem($item))
        {
            return false;
        }

        if (!$this->getRightsService()->createItemRightsLocationWithViewRightForEveryone($item))
        {
            return false;
        }

        $this->getItemsCacheService()->resetCache();

        return $item;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle $itemTitle
     *
     * @return boolean
     */
    public function createItemTitle(ItemTitle $itemTitle)
    {
        return $this->getItemRepository()->createItemTitle($itemTitle);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param string[] $values
     *
     * @return boolean
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
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param string $isocode
     * @param string $title
     *
     * @return boolean
     */
    public function createItemTitleForItemFromParameters(Item $item, string $isocode, string $title)
    {
        $itemTitle = new ItemTitle();
        $itemTitle->set_title($title);
        $itemTitle->set_isocode($isocode);
        $itemTitle->set_item_id($item->getId());

        return $this->createItemTitle($itemTitle);
    }

    /**
     * @param string $itemType
     * @param string[] $values
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
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
     *
     * @param User $user
     *
     * @return boolean[]
     */
    public function determineRightsForUser(User $user)
    {
        $rightsCacheService = new RightsCacheService($this);

        return $rightsCacheService->getForUser($user);
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
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[] $itemTitles
     *
     * @return string
     */
    public function determineItemTitleForCurrentLanguage(array $itemTitles)
    {
        $isoCode = $this->getTranslator()->getLocale();
        $fallbackIsoCodes = $this->getTranslator()->getFallbackLocales();

        if (key_exists($isoCode, $itemTitles))
        {
            return $itemTitles[$isoCode]->get_title();
        }
        else
        {
            foreach ($fallbackIsoCodes as $fallbackIsoCode)
            {
                if (key_exists($fallbackIsoCode, $itemTitles))
                {
                    return $itemTitles[$fallbackIsoCode]->get_title();
                }
            }
        }

        return $this->getTranslator()->trans('MenuItem', [], 'Chamilo\Core\Menu');
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function findItems()
    {
        return $this->getItemRepository()->findItems();
    }

    /**
     *
     * @param integer $parentIdentifier
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function findItemsByParentIdentifier(int $parentIdentifier)
    {
        return $this->getItemRepository()->findItemsByParentIdentifier($parentIdentifier);
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
     * @param string $itemType
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     */
    public function getItemTypeInstance(string $itemType)
    {
        return new $itemType();
    }

    /**
     * @return \Chamilo\Core\Menu\Service\ItemsCacheService
     */
    public function getItemsCacheService(): ItemsCacheService
    {
        return $this->itemsCacheService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\ItemsCacheService $itemsCacheService
     */
    public function setItemsCacheService(ItemsCacheService $itemsCacheService): void
    {
        $this->itemsCacheService = $itemsCacheService;
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
     * @param string[] $values
     *
     * @return boolean
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
            $itemTitle->set_title($valuesItemTitles[$isoCodeToUpdate]);

            if (!$this->updateItemTitle($itemTitle))
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
        return $this->getItemRepository()->deleteItemTitle($itemTitle);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle $itemTitle
     *
     * @return boolean
     */
    public function updateItemTitle(ItemTitle $itemTitle)
    {
        return $this->getItemRepository()->updateItemTitle($itemTitle);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param string[] $values
     *
     * @return boolean
     */
    public function saveItemFromValues(Item $item, array $values)
    {
        foreach ($item->get_default_property_names() as $property)
        {
            if (isset($values[$property]))
            {
                $item->set_default_property($property, $values[$property]);
            }
        }

        foreach ($item->get_additional_property_names() as $property)
        {
            if (isset($values[$property]))
            {
                $item->set_additional_property($property, $values[$property]);
            }
        }

        if (!$this->updateItem($item))
        {
            return false;
        }

        return true;
    }
}