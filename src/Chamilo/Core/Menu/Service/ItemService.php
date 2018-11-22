<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\Repository\ItemRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Utilities\StringUtilities;

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
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     * @param \Chamilo\Core\Menu\Storage\Repository\ItemRepository $itemRepository
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function __construct(
        ItemRepository $itemRepository, RightsService $rightsService, StringUtilities $stringUtilities
    )
    {
        $this->itemRepository = $itemRepository;
        $this->rightsService = $rightsService;
        $this->stringUtilities = $stringUtilities;
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
            $item->set_default_property($property, $values[$property]);
        }

        foreach ($item->get_additional_property_names() as $property)
        {
            $item->set_additional_property($property, $values[$property]);
        }

        $item->set_display(Item::DISPLAY_BOTH);
        $item->set_hidden();
        $item->set_sort($this->getNextItemSortValueByParentIdentifier($item->get_parent()));

        if (!$this->createItem($item))
        {
            return false;
        }

        if (!$this->getRightsService()->createItemRightsLocationForItem($item))
        {
            return false;
        }

        $this->resetCache();

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
                $item_title = new ItemTitle();
                $item_title->set_title($title);
                $item_title->set_isocode($isocode);
                $item_title->set_item_id($item->getId());

                if (!$this->createItemTitle($item_title))
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
     * @return boolean
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
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item[] $items
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
     *
     * @param integer $parentIdentifier
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getItemsByParentIdentifier($parentIdentifier)
    {
        return $this->getItemRepository()->findItemsByParentIdentifier($parentIdentifier);
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
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getRootItems()
    {
        return $this->getItemsByParentIdentifier(0);
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
     * Resets the cache for the menu items
     */
    public function resetCache()
    {
        $cacheService = new ItemsCacheService($this->itemRepository);
        $cacheService->resetCache();
    }
}