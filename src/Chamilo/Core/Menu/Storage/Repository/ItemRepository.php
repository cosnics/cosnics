<?php
namespace Chamilo\Core\Menu\Storage\Repository;

use Chamilo\Core\Menu\Service\Renderer\CategoryItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
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

    public function deleteItem(Item $item): bool
    {
        return $this->getDataClassRepository()->delete($item);
    }

    public function findItemByIdentifier(string $identifier): ?Item
    {
        return $this->getDataClassRepository()->retrieveById(Item::class, $identifier);
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
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
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
            new StaticConditionVariable(CategoryItemRenderer::class)
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateItem(Item $item): bool
    {
        return $this->getDataClassRepository()->update($item);
    }
}