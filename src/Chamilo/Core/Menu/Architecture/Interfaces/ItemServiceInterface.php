<?php
namespace Chamilo\Core\Menu\Architecture\Interfaces;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Menu\Architecture\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ItemServiceInterface
{
    public function countItemsByParentIdentifier(string $parentIdentifier): int;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function createItem(Item $item): bool;

    /**
     * @param string[][] $values
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function createItemForTypeFromValues(string $itemType, array $values): ?Item;

    /**
     * @throws \Exception
     */
    public function createItemTitle(ItemTitle $itemTitle): bool;

    /**
     * @throws \Exception
     */
    public function createItemTitleForItemFromParameters(Item $item, string $isocode, string $title): bool;

    /**
     * @param string[][] $values
     *
     * @throws \Exception
     */
    public function createItemTitlesForItemFromValues(Item $item, array $values): bool;

    /**
     * @param string $itemType
     * @param string[][] $values
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function createItemWithTitlesForTypeFromValues(string $itemType, array $values): ?Item;

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function deleteItem(Item $item): bool;

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function deleteItemChildren(Item $item): bool;

    public function deleteItemTitle(ItemTitle $itemTitle): bool;

    public function deleteItemTitlesForItem(Item $item): bool;

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[] $itemTitles
     */
    public function determineItemTitleForCurrentLanguage(array $itemTitles): string;

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[] $itemTitles
     */
    public function determineItemTitleForIsoCode(array $itemTitles, string $isoCode): string;

    public function doesItemHaveChildren(Item $item): bool;

    public function findItemByIdentifier(string $identifier): ?Item;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\ItemTitle>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemTitles(): ArrayCollection;

    /**
     * @param string $itemIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\ItemTitle>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemTitlesByItemIdentifier(string $itemIdentifier): ArrayCollection;

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemTitlesByItemIdentifierIndexedByIsoCode(string $itemIdentifier): array;

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[][][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemTitlesGroupedByItemIdentifierAndIsocode(): array;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItems(): ArrayCollection;

    /**
     * @param string[] $identifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemsByIdentifiers(array $identifiers): ArrayCollection;

    /**
     * @param string $parentIdentifier
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemsByParentIdentifier(
        string $parentIdentifier, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection;

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemsGroupedByParentIdentifier(): array;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\CategoryItem>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRootCategoryItems(): ArrayCollection;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRootItems(): ArrayCollection;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getItemTitleForCurrentLanguage(Item $item): string;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getItemTitleForIsoCode(Item $item, string $isoCode): string;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getNextItemSortValueByParentIdentifier(string $parentIdentifier): int;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function moveItemInDirection(Item $item, int $moveDirection): bool;

    /**
     * @param string[][] $values
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function saveItemFromValues(Item $item, array $values): bool;

    /**
     * @param string[][] $values
     *
     * @throws \Exception
     */
    public function saveItemTitlesForItemFromValues(Item $item, array $values): bool;

    /**
     * @param string[][] $values
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function saveItemWithTitlesFromValues(Item $item, array $values): bool;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    public function updateItem(Item $item): bool;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateItemTitle(ItemTitle $itemTitle): bool;
}