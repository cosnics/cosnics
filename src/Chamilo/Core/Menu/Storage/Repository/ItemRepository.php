<?php
namespace Chamilo\Core\Menu\Storage\Repository;

use Chamilo\Core\Menu\Storage\DataClass\CategoryItem;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Menu\Storage\Repository
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @param integer $parentIdentifier
     *
     * @return integer
     */
    public function countItemsByParentIdentifier(int $parentIdentifier)
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
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     * @throws \Exception
     */
    public function createItem(Item $item)
    {
        return $this->getDataClassRepository()->create($item);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle $itemTitle
     *
     * @return boolean
     * @throws \Exception
     */
    public function createItemTitle(ItemTitle $itemTitle)
    {
        return $this->getDataClassRepository()->create($itemTitle);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function deleteItem(Item $item)
    {
        return $this->getDataClassRepository()->delete($item);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle $itemTitle
     *
     * @return boolean
     */
    public function deleteItemTitle(ItemTitle $itemTitle)
    {
        return $this->getDataClassRepository()->delete($itemTitle);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function deleteItemTitlesForItem(Item $item)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ItemTitle::class, ItemTitle::PROPERTY_ITEM_ID),
            new StaticConditionVariable($item->getId())
        );

        return $this->getDataClassRepository()->deletes(ItemTitle::class, $condition);
    }

    /**
     * @param integer $identifier
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     */
    public function findItemByIdentifier(int $identifier)
    {
        return $this->getDataClassRepository()->retrieveById(Item::class, $identifier);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[]
     */
    public function findItemTitles()
    {
        return $this->getDataClassRepository()->retrieves(ItemTitle::class, new DataClassRetrievesParameters());
    }

    /**
     * @param integer $itemIdentifier
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[]
     */
    public function findItemTitlesByItemIdentifier(int $itemIdentifier)
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
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function findItems()
    {
        $orderBy = [];
        $orderBy[] = new OrderBy(new PropertyConditionVariable(Item::class, Item::PROPERTY_PARENT));
        $orderBy[] = new OrderBy(new PropertyConditionVariable(Item::class, Item::PROPERTY_SORT));

        return $this->getDataClassRepository()->retrieves(
            Item::class, new DataClassRetrievesParameters(null, null, null, $orderBy)
        );
    }

    /**
     * @param integer[] $identifiers
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function findItemsByIdentifiers(array $identifiers)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Item::class, Item::PROPERTY_ID), $identifiers
        );

        return $this->getDataClassRepository()->retrieves(Item::class, new DataClassRetrievesParameters($condition));
    }

    /**
     *
     * @param integer $parentIdentifier
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function findItemsByParentIdentifier(
        int $parentIdentifier, int $count = null, int $offset = null, array $orderProperties = []
    )
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Item::class, Item::PROPERTY_PARENT),
            new StaticConditionVariable($parentIdentifier)
        );

        $orderProperties[] = new OrderBy(new PropertyConditionVariable(Item::class, Item::PROPERTY_SORT));

        return $this->getDataClassRepository()->retrieves(
            Item::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderProperties)
        );
    }

    /**
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\CategoryItem[]
     */
    public function findRootCategoryItems()
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Item::class, Item::PROPERTY_PARENT), new StaticConditionVariable(0)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Item::class, Item::PROPERTY_TYPE),
            new StaticConditionVariable(CategoryItem::class)
        );

        $orderBy = array(new OrderBy(new PropertyConditionVariable(Item::class, Item::PROPERTY_SORT)));

        return $this->getDataClassRepository()->retrieves(
            Item::class, new DataClassRetrievesParameters(
                new AndCondition($conditions), null, null, $orderBy
            )
        );
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param integer $parentIdentifier
     *
     * @return integer
     */
    public function getNextItemSortValueByParentIdentifier(int $parentIdentifier)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Item::class, Item::PROPERTY_PARENT),
            new StaticConditionVariable($parentIdentifier)
        );

        return $this->getDataClassRepository()->retrieveMaximumValue(Item::class, Item::PROPERTY_SORT, $condition);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function updateItem(Item $item)
    {
        return $this->getDataClassRepository()->update($item);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle $itemTitle
     *
     * @return boolean
     */
    public function updateItemTitle(ItemTitle $itemTitle)
    {
        return $this->getDataClassRepository()->update($itemTitle);
    }
}