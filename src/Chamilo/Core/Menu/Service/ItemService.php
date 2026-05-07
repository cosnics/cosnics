<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Architecture\Interface\ItemServiceInterface;
use Chamilo\Core\Menu\Implementation\Menu\ApplicationItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\Repository\ItemRepository;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Service\DisplayOrderHandler;
use Chamilo\Libraries\Storage\Service\PropertyMapper;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
readonly class ItemService implements ItemServiceInterface
{
    public const int PARAM_DIRECTION_DOWN = 2;
    public const int PARAM_DIRECTION_UP = 1;

    /**
     * @param string[] $fallbackIsoCodes
     */
    public function __construct(
        protected ItemRepository $itemRepository, protected StringUtilities $stringUtilities,
        protected PropertyMapper $propertyMapper, protected Translator $translator,
        protected DisplayOrderHandler $displayOrderHandler, protected array $fallbackIsoCodes
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countItemsByParentIdentifier(string $parentIdentifier): int
    {
        return $this->itemRepository->countItemsByParentIdentifier($parentIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createItem(Item $item): void
    {
        $this->displayOrderHandler->handleDisplayOrderBeforeCreate($item);
        $this->itemRepository->createItem($item);
    }

    /**
     * @param string[]|string[][] $values
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createItemForTypeFromValues(string $itemType, array $values): ?Item
    {
        $item = new Item();

        $item->setDisplay(DisplayTypeEnum::ICON_AND_LABEL);
        $item->setType($itemType);
        $item->setHidden(isset($values[Item::PROPERTY_HIDDEN]) ? 1 : 0);
        $item->setIconClass($values[Item::PROPERTY_ICON_CLASS]);
        $item->setParentId($values[Item::PROPERTY_PARENT]);

        foreach ($values[Item::PROPERTY_TITLES] as $isoCode => $title) {
            $item->setTitleForIsoCode($isoCode, $title);
        }

        foreach ($values[Item::PROPERTY_CONFIGURATION] as $configurationVariable => $configurationValue) {
            $item->setSetting($configurationVariable, $configurationValue);
        }

        $this->createItem($item);

        return $item;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteItem(Item $item): void
    {
        $this->deleteItemChildren($item);
        $this->itemRepository->deleteItem($item);
        $this->displayOrderHandler->handleDisplayOrderAfterDelete($item);
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteItemChildren(Item $item): void
    {
        $itemChildren = $this->findItemsByParentIdentifier($item->getId());

        foreach ($itemChildren as $itemChild) {
            $this->deleteItem($itemChild);
        }
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
        return $this->itemRepository->findItemByIdentifier($identifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findItems(): ArrayCollection
    {
        return $this->itemRepository->findItems();
    }

    /**
     * @param string[] $identifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findItemsByIdentifiers(array $identifiers): ArrayCollection
    {
        return $this->itemRepository->findItemsByIdentifiers($identifiers);
    }

    /**
     * @param string $parentIdentifier
     * @param ?int $count
     * @param ?int $offset
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findItemsByParentIdentifier(
        string $parentIdentifier, ?int $count = null, ?int $offset = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->itemRepository->findItemsByParentIdentifier(
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
        return $this->itemRepository->findItemsByType($type);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[][]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findItemsGroupedByParentIdentifier(): array
    {
        return $this->propertyMapper->groupDataClassByProperty(
            $this->findItems(), Item::PROPERTY_PARENT
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findRootCategoryItems(): ArrayCollection
    {
        return $this->itemRepository->findRootCategoryItems();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findRootItems(): ArrayCollection
    {
        return $this->findItemsByParentIdentifier(DataClass::EMPTY_UUID);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getNextItemSortValueByParentIdentifier(string $parentIdentifier): int
    {
        return $this->itemRepository->getNextItemSortValueByParentIdentifier($parentIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function moveItemInDirection(Item $item, int $moveDirection): void
    {
        $newDisplayOrder = $item->getSort() + ($moveDirection == self::PARAM_DIRECTION_UP ? - 1 : 1);
        $item->setSort($newDisplayOrder);

        $this->updateItem($item);
    }

    /**
     * @param string[]|string[][] $values
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function saveItemFromValues(Item $item, array $values): void
    {
        $parentHasChanged = $item->getParentId() != $values[Item::PROPERTY_PARENT];

        if ($parentHasChanged && !isset($values[Item::PROPERTY_SORT])) {
            $item->setSort(null);
        }

        $item->setDisplay(DisplayTypeEnum::ICON_AND_LABEL);
        $item->setHidden(isset($values[Item::PROPERTY_HIDDEN]) ? 1 : 0);
        $item->setIconClass($values[Item::PROPERTY_ICON_CLASS]);
        $item->setParentId($values[Item::PROPERTY_PARENT]);

        foreach ($values[Item::PROPERTY_TITLES] as $isoCode => $title) {
            $item->setTitleForIsoCode($isoCode, $title);
        }

        foreach ($values[Item::PROPERTY_CONFIGURATION] as $configurationVariable => $configurationValue) {
            $item->setSetting($configurationVariable, $configurationValue);
        }

        $this->updateItem($item);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function updateItem(Item $item): void
    {
        $this->displayOrderHandler->handleDisplayOrderBeforeUpdate($item);
        $this->itemRepository->updateItem($item);
    }
}