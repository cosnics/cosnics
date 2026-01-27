<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Architecture\Interface\ItemServiceInterface;
use Chamilo\Core\Menu\Implementation\Menu\ApplicationItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\Repository\ItemRepository;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Service\DisplayOrderHandler;
use Chamilo\Libraries\Storage\Service\PropertyMapper;
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

    private StringUtilities $stringUtilities;

    private Translator $translator;

    public function __construct(
        ItemRepository $itemRepository, StringUtilities $stringUtilities, PropertyMapper $propertyMapper,
        Translator $translator, DisplayOrderHandler $displayOrderHandler, array $fallbackIsoCodes
    )
    {
        $this->itemRepository = $itemRepository;
        $this->stringUtilities = $stringUtilities;
        $this->propertyMapper = $propertyMapper;
        $this->translator = $translator;
        $this->displayOrderHandler = $displayOrderHandler;
        $this->fallbackIsoCodes = $fallbackIsoCodes;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countItemsByParentIdentifier(string $parentIdentifier): int
    {
        return $this->getItemRepository()->countItemsByParentIdentifier($parentIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
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

        return true;
    }

    /**
     * @param string[]|string[][] $values
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function createItemForTypeFromValues(string $itemType, array $values): ?Item
    {
        $item = new Item();

        $item->setDisplay(Item::DISPLAY_BOTH);
        $item->setType($itemType);
        $item->setHidden(isset($values[Item::PROPERTY_HIDDEN]) ? 1 : 0);
        $item->setIconClass($values[Item::PROPERTY_ICON_CLASS]);
        $item->setParentId($values[Item::PROPERTY_PARENT]);

        foreach ($values[Item::PROPERTY_TITLES] as $isoCode => $title)
        {
            $item->setTitleForIsoCode($isoCode, $title);
        }

        foreach ($values[Item::PROPERTY_CONFIGURATION] as $configurationVariable => $configurationValue)
        {
            $item->setSetting($configurationVariable, $configurationValue);
        }

        if (!$this->createItem($item))
        {
            return null;
        }

        return $item;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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

        return true;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function doesItemHaveChildren(Item $item): bool
    {
        return $this->countItemsByParentIdentifier($item->getId()) > 0;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findApplicationItems(): ArrayCollection
    {
        return $this->findItemsByType(ApplicationItemRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findItemByIdentifier(string $identifier): ?Item
    {
        return $this->getItemRepository()->findItemByIdentifier($identifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findItems(): ArrayCollection
    {
        return $this->getItemRepository()->findItems();
    }

    /**
     * @param string[] $identifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findItemsByIdentifiers(array $identifiers): ArrayCollection
    {
        return $this->getItemRepository()->findItemsByIdentifiers($identifiers);
    }

    /**
     * @param string $parentIdentifier
     * @param ?int $count
     * @param ?int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findItemsByParentIdentifier(
        string $parentIdentifier, ?int $count = null, ?int $offset = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->getItemRepository()->findItemsByParentIdentifier(
            $parentIdentifier, $count, $offset, $orderBy
        );
    }

    /**
     * @param string $type
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findItemsByType(string $type): ArrayCollection
    {
        return $this->getItemRepository()->findItemsByType($type);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[][]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findItemsGroupedByParentIdentifier(): array
    {
        return $this->getPropertyMapper()->groupDataClassByProperty(
            $this->findItems(), Item::PROPERTY_PARENT
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findRootCategoryItems(): ArrayCollection
    {
        return $this->getItemRepository()->findRootCategoryItems();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getNextItemSortValueByParentIdentifier(string $parentIdentifier): int
    {
        return $this->getItemRepository()->getNextItemSortValueByParentIdentifier($parentIdentifier);
    }

    public function getPropertyMapper(): PropertyMapper
    {
        return $this->propertyMapper;
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function moveItemInDirection(Item $item, int $moveDirection): bool
    {
        $newDisplayOrder = $item->getSort() + ($moveDirection == self::PARAM_DIRECTION_UP ? - 1 : 1);
        $item->setSort($newDisplayOrder);

        return $this->updateItem($item);
    }

    /**
     * @param string[]|string[][] $values
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function saveItemFromValues(Item $item, array $values): bool
    {
        $parentHasChanged = $item->getParentId() != $values[Item::PROPERTY_PARENT];

        if ($parentHasChanged && !isset($values[Item::PROPERTY_SORT]))
        {
            $item->setSort(null);
        }

        $item->setDisplay(Item::DISPLAY_BOTH);
        $item->setHidden(isset($values[Item::PROPERTY_HIDDEN]) ? 1 : 0);
        $item->setIconClass($values[Item::PROPERTY_ICON_CLASS]);
        $item->setParentId($values[Item::PROPERTY_PARENT]);

        foreach ($values[Item::PROPERTY_TITLES] as $isoCode => $title)
        {
            $item->setTitleForIsoCode($isoCode, $title);
        }

        foreach ($values[Item::PROPERTY_CONFIGURATION] as $configurationVariable => $configurationValue)
        {
            $item->setSetting($configurationVariable, $configurationValue);
        }

        if (!$this->updateItem($item))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
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

        return true;
    }
}