<?php
namespace Chamilo\Core\Menu\Storage\Repository;

use Chamilo\Core\Menu\Storage\DataClass\CategoryItem;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Menu\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemRepository
{

    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function countItemsByParentIdentifier(string $parentIdentifier): int
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Item::class, Item::PROPERTY_PARENT),
            new StaticConditionVariable($parentIdentifier)
        );

        return $this->getDataClassRepository()->count(
            Item::class, new DataClassCountParameters($condition)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createItem(Item $item): bool
    {
        return $this->getDataClassRepository()->create($item);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createItemTitle(ItemTitle $itemTitle): bool
    {
        return $this->getDataClassRepository()->create($itemTitle);
    }

    public function deleteItem(Item $item): bool
    {
        return $this->getDataClassRepository()->delete($item);
    }

    public function deleteItemTitle(ItemTitle $itemTitle): bool
    {
        return $this->getDataClassRepository()->delete($itemTitle);
    }

    public function deleteItemTitlesForItem(Item $item): bool
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ItemTitle::class, ItemTitle::PROPERTY_ITEM_ID),
            new StaticConditionVariable($item->getId())
        );

        return $this->getDataClassRepository()->deletes(ItemTitle::class, $condition);
    }

    public function findItemByIdentifier(string $identifier): ?Item
    {
        return $this->getDataClassRepository()->retrieveById(Item::class, $identifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\ItemTitle>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemTitles(): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(ItemTitle::class, new DataClassRetrievesParameters());
    }

    /**
     * @param string $itemIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\ItemTitle>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemTitlesByItemIdentifier(string $itemIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ItemTitle::class, ItemTitle::PROPERTY_ITEM_ID),
            new StaticConditionVariable($itemIdentifier)
        );

        return $this->getDataClassRepository()->retrieves(
            ItemTitle::class, new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItems(): ArrayCollection
    {
        $orderBy = new OrderBy();
        $orderBy->add(new OrderProperty(new PropertyConditionVariable(Item::class, Item::PROPERTY_PARENT)));
        $orderBy->add(new OrderProperty(new PropertyConditionVariable(Item::class, Item::PROPERTY_SORT)));

        return $this->getDataClassRepository()->retrieves(
            Item::class, new DataClassRetrievesParameters(null, null, null, $orderBy)
        );
    }

    /**
     * @param int[] $identifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findItemsByIdentifiers(array $identifiers): ArrayCollection
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Item::class, DataClass::PROPERTY_ID), $identifiers
        );

        return $this->getDataClassRepository()->retrieves(Item::class, new DataClassRetrievesParameters($condition));
    }

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
    ): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Item::class, Item::PROPERTY_PARENT),
            new StaticConditionVariable($parentIdentifier)
        );

        if (is_null($orderBy))
        {
            $orderBy = new OrderBy();
        }

        $orderBy->add(new OrderProperty(new PropertyConditionVariable(Item::class, Item::PROPERTY_SORT)));

        return $this->getDataClassRepository()->retrieves(
            Item::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\CategoryItem>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRootCategoryItems(): ArrayCollection
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Item::class, Item::PROPERTY_PARENT), new StaticConditionVariable(0)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Item::class, CompositeDataClass::PROPERTY_TYPE),
            new StaticConditionVariable(CategoryItem::class)
        );

        $orderBy = new OrderBy([new OrderProperty(new PropertyConditionVariable(Item::class, Item::PROPERTY_SORT))]);

        return $this->getDataClassRepository()->retrieves(
            Item::class, new DataClassRetrievesParameters(
                new AndCondition($conditions), null, null, $orderBy
            )
        );
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getNextItemSortValueByParentIdentifier(string $parentIdentifier): int
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Item::class, Item::PROPERTY_PARENT),
            new StaticConditionVariable($parentIdentifier)
        );

        return $this->getDataClassRepository()->retrieveMaximumValue(Item::class, Item::PROPERTY_SORT, $condition);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateItem(Item $item): bool
    {
        return $this->getDataClassRepository()->update($item);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateItemTitle(ItemTitle $itemTitle): bool
    {
        return $this->getDataClassRepository()->update($itemTitle);
    }
}