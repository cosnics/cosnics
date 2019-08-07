<?php

namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\ClosureTable;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Based on https://gist.github.com/kentoj/872cbefc68f68a2a97b6189da9cd6e23
 *
 * @package Chamilo\Core\Group\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ClosureTableRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * ClosureTableRepository constructor.
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(
        \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
    )
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param string $dataClass
     * @param string $closureTableClass
     * @param int $parentId
     * @param bool $includeSelf
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    protected function getAllChildrenByParentId(
        string $dataClass, string $closureTableClass, int $parentId, bool $includeSelf = true
    )
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $closureTableClass,
                new EqualityCondition(
                    new PropertyConditionVariable($dataClass, DataClass::PROPERTY_ID),
                    new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_CHILD_ID)
                )
            )
        );

        $condition = $this->buildGetChildrenConditionByParentId($closureTableClass, $parentId, $includeSelf);
        $orderBy[] = new OrderBy(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_DEPTH));

        $properties = new DataClassRetrievesParameters($condition, null, null, $orderBy, $joins);

        return $this->dataClassRepository->retrieves($dataClass, $properties);
    }

    /**
     * @param string $closureTableClass
     * @param int $parentId
     * @param bool $includeSelf
     *
     * @return int[]|string[]
     */
    protected function getAllChildIdsByParentId(string $closureTableClass, int $parentId, bool $includeSelf = true)
    {
        $condition = $this->buildGetChildrenConditionByParentId($closureTableClass, $parentId, $includeSelf);
        $orderBy[] = new OrderBy(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_DEPTH));

        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_CHILD_ID));

        $distinctParameters = new DataClassDistinctParameters($condition, $properties, null, $orderBy);

        return $this->dataClassRepository->distinct($closureTableClass, $distinctParameters);
    }

    /**
     * @param string $closureTableClass
     * @param int $parentId
     * @param bool $includeSelf
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function buildGetChildrenConditionByParentId(
        string $closureTableClass, int $parentId, bool $includeSelf = true
    )
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentId)
        );

        if (!$includeSelf)
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_CHILD_ID),
                    new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_PARENT_ID)
                )
            );
        }

        return new AndCondition($conditions);
    }

    /**
     * @param string $dataClass
     * @param string $closureTableClass
     * @param int $childId
     * @param bool $includeSelf
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    protected function getAllParentsByChildId(
        string $dataClass, string $closureTableClass, int $childId, bool $includeSelf = true
    )
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $closureTableClass,
                new EqualityCondition(
                    new PropertyConditionVariable($dataClass, DataClass::PROPERTY_ID),
                    new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_PARENT_ID)
                )
            )
        );

        $condition = $this->buildGetParentsConditionByParentId($closureTableClass, $childId, $includeSelf);
        $orderBy[] =
            new OrderBy(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_DEPTH), SORT_DESC);

        $properties = new DataClassRetrievesParameters($condition, null, null, $orderBy, $joins);

        return $this->dataClassRepository->retrieves($dataClass, $properties);
    }

    /**
     * @param string $closureTableClass
     * @param int $childId
     * @param bool $includeSelf
     *
     * @return int[]|string[]
     */
    protected function getAllParentIdsByChildId(string $closureTableClass, int $childId, bool $includeSelf = true)
    {
        $condition = $this->buildGetParentsConditionByParentId($closureTableClass, $childId, $includeSelf);
        $orderBy[] =
            new OrderBy(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_DEPTH), SORT_DESC);

        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_PARENT_ID));

        $distinctParameters = new DataClassDistinctParameters($condition, $properties, null, $orderBy);

        return $this->dataClassRepository->distinct($closureTableClass, $distinctParameters);
    }

    /**
     * @param string $closureTableClass
     * @param int $childId
     * @param bool $includeSelf
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function buildGetParentsConditionByParentId(
        string $closureTableClass, int $childId, bool $includeSelf = true
    )
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_CHILD_ID),
            new StaticConditionVariable($childId)
        );

        if (!$includeSelf)
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_CHILD_ID),
                    new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_PARENT_ID)
                )
            );
        }

        return new AndCondition($conditions);
    }

    /**
     * @param string $closureTableClass
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $child
     * @param int $parentId
     *
     * @return bool
     */
    protected function addChildToParent(string $closureTableClass, DataClass $child, int $parentId)
    {
        $columns = new DataClassProperties();
        $columns->add(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_PARENT_ID));
        $columns->add(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_CHILD_ID));
        $columns->add(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_DEPTH));

        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_PARENT_ID));
        $properties->add(new StaticConditionVariable($child->getId()));

        $properties->add(
            new OperationConditionVariable(
                new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_DEPTH),
                OperationConditionVariable::ADDITION,
                new StaticConditionVariable(1)
            )
        );

        $condition = new EqualityCondition(
            new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_CHILD_ID),
            new StaticConditionVariable($parentId)
        );

        $recordRetrievesParameters = new RecordRetrievesParameters($properties, $condition);

        return $this->dataClassRepository->transactional(
            function () use ($closureTableClass, $columns, $recordRetrievesParameters, $child) {
                $success = $this->dataClassRepository->insertIntoSelectFrom(
                    $closureTableClass, $columns, $closureTableClass, $recordRetrievesParameters
                );

                if (!$success)
                {
                    return false;
                }

                return $this->dataClassRepository->createRecord(
                    $closureTableClass,
                    [
                        ClosureTable::PROPERTY_PARENT_ID => $child->getId(),
                        ClosureTable::PROPERTY_CHILD_ID => $child->getId(),
                        ClosureTable::PROPERTY_DEPTH => 0
                    ]
                );
            }
        );
    }

    /**
     * @param string $closureTableClass
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $child
     *
     * @return bool
     */
    protected function deleteChildFromTree(string $closureTableClass, DataClass $child)
    {
        $childIds = $this->getAllChildIdsByParentId($closureTableClass, $child->getId());

        $condition = new InCondition(
            new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_CHILD_ID), $childIds
        );

        return $this->dataClassRepository->deletes($closureTableClass, $condition);
    }

    /**
     * @param string $closureTableClass
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $child
     * @param int $newParentId
     *
     * @return bool
     */
    protected function moveChildToNewParent(string $closureTableClass, DataClass $child, int $newParentId)
    {
        return $this->dataClassRepository->transactional(
            function () use ($closureTableClass, $child, $newParentId) {
                // Delete the relations between the parents of the group and the children of the group
                $childIds = $this->getAllChildIdsByParentId($closureTableClass, $child->getId());
                $parentIds = $this->getAllParentIdsByChildId($closureTableClass, $child->getId(), false);

                // Delete the relations between the parents of the group and the children of the group
                $conditions = [];

                $conditions[] = new InCondition(
                    new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_CHILD_ID), $childIds
                );

                $conditions[] = new InCondition(
                    new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_PARENT_ID), $parentIds
                );

                $condition = new AndCondition($conditions);

                if(!$this->dataClassRepository->deletes($closureTableClass, $condition))
                {
                    return false;
                }

                // INSERT SUBTREE INTO NEW PARENTS. Because cross join is not possible in the framework we retrieve the parents
                // and insert the subtree into each parent

                $newParentIds = $this->getAllParentIdsByChildId($closureTableClass, $newParentId);
                $currentDepth = count($newParentIds);
                foreach ($newParentIds as $newParentId)
                {
                    $columns = new DataClassProperties();
                    $columns->add(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_PARENT_ID));
                    $columns->add(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_CHILD_ID));
                    $columns->add(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_DEPTH));

                    $properties = new DataClassProperties();
                    $properties->add(new StaticConditionVariable($newParentId));
                    $properties->add(new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_CHILD_ID));

                    $properties->add(
                        new OperationConditionVariable(
                            new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_DEPTH),
                            OperationConditionVariable::ADDITION,
                            new StaticConditionVariable($currentDepth)
                        )
                    );

                    $condition = new EqualityCondition(
                        new PropertyConditionVariable($closureTableClass, ClosureTable::PROPERTY_PARENT_ID),
                        new StaticConditionVariable($child->getId())
                    );

                    $recordRetrievesParameters = new RecordRetrievesParameters($properties, $condition);

                    if(!$this->dataClassRepository->insertIntoSelectFrom(
                        $closureTableClass, $columns, $closureTableClass, $recordRetrievesParameters
                    ))
                    {
                        return false;
                    }

                    $currentDepth --;
                }

                return true;
            }
        );
    }
}