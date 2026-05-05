<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Enum\ComparisonTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\Enum\OperationTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderProperty;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperty;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Group\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupRepository
{
    public function __construct(
        protected DataClassRepository $dataClassRepository,
        protected SearchQueryConditionGenerator $searchQueryConditionGenerator
    )
    {
    }

    /**
     * Change the left/right values in the tree of every node that is affected by to the delete of this node
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function __postDelete(Group $group): bool
    {
        // This private function is only ever called from within a transaction.
        //
        // This needs to be a transaction: both updates should either commit or abort.
        // Now, it is possible that the first update succeeds, but the latter doesn't.
        // This implies that we may end up with an inconsistent nested set.
        $delta = $group->getRightValue() - $group->getLeftValue() + 1;

        // 1. Update the left and right values of all successors of the deleted node.
        // A successor has a left-value which is higher than the left-value of the deleted node.

        $conditions = [];

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE), ComparisonTypeEnum::GREATER_THAN,
            new StaticConditionVariable($group->getLeftValue())
        );

        $updateCondition = new AndCondition($conditions);

        $leftValueVariable = new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE);
        $rightValueVariable = new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE);

        $rightValueDataClassProperty = new UpdateProperty(
            $rightValueVariable, new OperationConditionVariable(
                $rightValueVariable, OperationTypeEnum::MINUS, new StaticConditionVariable($delta)
            )
        );

        $properties = [];
        $properties[] = $rightValueDataClassProperty;
        $properties[] = new UpdateProperty(
            $leftValueVariable, new OperationConditionVariable(
                $leftValueVariable, OperationTypeEnum::MINUS, new StaticConditionVariable($delta)
            )
        );

        if (!$this->dataClassRepository->updates(
            Group::class, new UpdateProperties($properties), $updateCondition
        )) {
            return false;
        }

        // 2. Update the right values of all ancestors of the deleted node.
        // An ancestor has a left value less than the left value of the deleted node
        // and a right value greater than the right value of the deleted node

        $conditions = [];

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE), ComparisonTypeEnum::LESS_THAN,
            new StaticConditionVariable($group->getLeftValue())
        );

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE), ComparisonTypeEnum::GREATER_THAN,
            new StaticConditionVariable($group->getRightValue())
        );

        $updateCondition = new AndCondition($conditions);

        $properties = [];
        $properties[] = $rightValueDataClassProperty;

        if (!$this->dataClassRepository->updates(
            Group::class, new UpdateProperties($properties), $updateCondition
        )) {
            return false;
        }

        return true;
    }

    /**
     * Creates the necessary room to insert a number of values (1 by default) into the nested set: it shifts the
     * left/right values of all nodes that are traversed after the insertion point to the right.
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function __preInsert(int $insertAfter, int $numberOfElements = 1): bool
    {
        // This private function is only ever called from within a transaction.
        //
        // This needs to be a transaction: both updates should either commit or abort.
        // Now, it is possible that the first update succeeds, but the latter doesn't.
        // This implies that we may end up with an inconsistent nested set.

        // Update all necessary left-values
        $conditions = [];

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE), ComparisonTypeEnum::GREATER_THAN,
            new StaticConditionVariable($insertAfter)
        );

        $updateCondition = new AndCondition($conditions);

        $leftValueVariable = new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE);

        $properties = [];
        $properties[] = new UpdateProperty(
            $leftValueVariable, new OperationConditionVariable(
                $leftValueVariable, OperationTypeEnum::ADDITION, new StaticConditionVariable($numberOfElements * 2)
            )
        );

        if (!$this->dataClassRepository->updates(
            Group::class, new UpdateProperties($properties), $updateCondition
        )) {
            return false;
        }

        // Update all necessary right-values
        $conditions = [];

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE), ComparisonTypeEnum::GREATER_THAN,
            new StaticConditionVariable($insertAfter)
        );

        $updateCondition = new AndCondition($conditions);

        $rightValueVariable = new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE);

        $properties = [];
        $properties[] = new UpdateProperty(
            $rightValueVariable, new OperationConditionVariable(
                $rightValueVariable, OperationTypeEnum::ADDITION, new StaticConditionVariable($numberOfElements * 2)
            )
        );

        if (!$this->dataClassRepository->updates(
            Group::class, new UpdateProperties($properties), $updateCondition
        )) {
            return false;
        }

        return true;
    }

    /**
     * Validates a relative position of a node, which is used when creating or moving a node.
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    protected function __validatePosition(Group $group, ?Group $referenceNode = null): ?Group
    {
        if ($referenceNode === null) {
            // Use the parent of the node as a reference
            $referenceNode = $this->retrieveGroupByIdentifier($group->getParentId());
        }

        if ($group->getId() === $referenceNode->getId()) {
            // TODO Report an error when attempting to create a node as its own child
            return null;
        }

        if ($group->getParentId() == 0 || $group->getParentId() != $referenceNode->getId()) {
            // To be a child of the reference node, the parent should be set correctly
            $group->setParentId($referenceNode->getId());
        }

        return $referenceNode;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countAncestorsByGroup(Group $group, bool $includeSelf = true, ?ConditionInterface $condition = null
    ): int
    {
        return $this->dataClassRepository->count(
            Group::class,
            new StorageParameters(condition: $this->determineAncestorsCondition($group, $includeSelf, $condition))
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countDescendantsByGroup(Group $group, bool $recursive = true, ?ConditionInterface $condition = null
    ): int
    {
        return $this->dataClassRepository->count(
            Group::class, new StorageParameters(
                condition: $this->determineDescendantsCondition(
                    $group, $recursive, false, $condition
                )
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countGroups(?ConditionInterface $condition = null): int
    {
        return $this->dataClassRepository->count(
            Group::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countSiblingsByGroup(Group $group, bool $includeSelf = true, ?ConditionInterface $condition = null
    ): int
    {
        return $this->dataClassRepository->count(
            Group::class,
            new StorageParameters(condition: $this->determineSiblingsCondition($group, $includeSelf, $condition))
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function createGroup(Group $group): bool
    {
        $referenceNode = $this->retrieveGroupByIdentifier($group->getParentId());

        // This variable is used to identify the node after which the newly
        // created node should be placed. This value is initialized with 0
        // which would create the node as the root of a nested set.
        $insertAfter = 0;

        if ($referenceNode != 0) { // Not creating the root node of a hierarchy

            // Identify the reference node (except when creating the root node, there must be one).
            if ($this->__validatePosition($group, $referenceNode) === null) {
                return false;
            }

            $insertAfter = $referenceNode->getRightValue() - 1;
        }

        // Creating a node in a nested set requires multiple updates
        // which have to be performed atomically and consistently.
        //
        // Use a transaction to guarantee this.

        return $this->dataClassRepository->transactional(
            function () use ($group, $insertAfter) { // Correct the left and right values wherever necessary.
                if (!$this->__preInsert($insertAfter)) {
                    return false;
                }

                // Left and right values have been shifted so now we
                // want to really add the location itself, but first
                // we have to set it's left and right value.
                $group->setLeftValue($insertAfter + 1);
                $group->setRightValue($insertAfter + 2);

                return $this->dataClassRepository->create($group);
            }
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroup(Group $group): bool
    {
        $this->dataClassRepository->delete($group);
        $this->__postDelete($group);

        return true;
    }

    protected function determineAncestorsCondition(
        Group $group, bool $includeSelf = false, ?ConditionInterface $condition = null
    ): AndCondition
    {
        $conditions = [];

        if ($includeSelf) {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE),
                ComparisonTypeEnum::LESS_THAN_OR_EQUAL, new StaticConditionVariable($group->getLeftValue())
            );
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE),
                ComparisonTypeEnum::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($group->getRightValue())
            );
        }
        else {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE), ComparisonTypeEnum::LESS_THAN,
                new StaticConditionVariable($group->getLeftValue())
            );
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE),
                ComparisonTypeEnum::GREATER_THAN, new StaticConditionVariable($group->getRightValue())
            );
        }

        if ($condition) {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
    }

    protected function determineDescendantsCondition(
        Group $group, bool $recursive = false, bool $includeSelf = false, ?ConditionInterface $condition = null
    ): AndCondition
    {
        $conditions = [];

        if ($recursive) {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE),
                $includeSelf ? ComparisonTypeEnum::GREATER_THAN_OR_EQUAL : ComparisonTypeEnum::GREATER_THAN,
                new StaticConditionVariable($group->getLeftValue())
            );

            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE),
                $includeSelf ? ComparisonTypeEnum::LESS_THAN_OR_EQUAL : ComparisonTypeEnum::LESS_THAN,
                new StaticConditionVariable($group->getRightValue())
            );
        }
        elseif ($includeSelf) {
            $conditions[] = new OrCondition(
                [
                    new EqualityCondition(
                        new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID),
                        new StaticConditionVariable($group->getId())
                    ),
                    new EqualityCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                        new StaticConditionVariable($group->getId())
                    )
                ]
            );
        }
        else {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                new StaticConditionVariable($group->getId())
            );
        }

        if ($condition) {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
    }

    /**
     * Build the conditions for the get / count _ siblings methods
     */
    protected function determineSiblingsCondition(
        Group $group, bool $includeSelf = false, ?ConditionInterface $condition = null
    ): AndCondition
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($group->getParentId())
        );

        if (!$includeSelf) {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID),
                    new StaticConditionVariable($group->getId())
                )
            );
        }

        if ($condition) {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function moveGroup(Group $group, string $parentGroupIdentifier): bool
    {
        if ($parentGroupIdentifier == 0) {
            $referenceNode = $this->retrieveGroupByIdentifier($group->getParentId());
        }
        else {
            $referenceNode = $this->retrieveGroupByIdentifier($parentGroupIdentifier);
        }

        if ($this->__validatePosition($group, $referenceNode) === null) {
            return false;
        }

        // This variable is used to identify the node after which the newly
        // created node should be placed. This value is initialized with 0
        // which would create the node as the root of a nested set.
        $insertAfter = $referenceNode->getRightValue() - 1;

        // Moving a node in a nested set requires multiple updates
        // which have to be performed atomically and consistently.
        //
        // Use a transaction to guarantee this.

        return $this->dataClassRepository->transactional(
            function () use ($group, $insertAfter) { // Step 0: Compute the auxiliary values used by this
                // algorithm
                // This is the initial position of the node to be moved
                $initialLeft = $group->getLeftValue();
                $initialRight = $group->getRightValue();

                // This is the size of the subtree to be moved (i.e. the size of the gap to be created so that it can be
                // moved in)
                $delta = $group->getRightValue() - $group->getLeftValue() + 1;

                // When moving nodes left or up, the gap we have created will have incremented the left and right values
                // of the nodes to be moved by $delta.
                $afterPreInsertLeft = ($insertAfter > $initialLeft) ? $initialLeft : $initialLeft + $delta;
                $afterPreInsertRight = ($insertAfter > $initialLeft) ? $initialRight : $initialRight + $delta;

                // How the nodes should move: negative numbers mean left or up, positive numbers mean right
                $shift = ($insertAfter + 1) - $afterPreInsertLeft;

                // This is where the node will end up in the end
                // When moving left or up, simply shift the previous position
                // When moving right, also account for the fact that post_delete will decrement the left and right
                // values of the moved nodes by $delta
                $finalLeft =
                    (($insertAfter < $initialLeft) ? $afterPreInsertLeft : $afterPreInsertLeft - $delta) + $shift;
                $finalRight =
                    (($insertAfter < $initialLeft) ? $afterPreInsertRight : $afterPreInsertRight - $delta) + $shift;

                // Step 1: Create a gap where the node can be moved into.
                $res = $this->__preInsert($insertAfter, $delta / 2);

                if (!$res) {
                    return false;
                }

                // Step 2: Move the node and its offspring to fill the newly created gap
                $conditions = [];

                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE),
                    ComparisonTypeEnum::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($afterPreInsertLeft)
                );
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE),
                    ComparisonTypeEnum::LESS_THAN_OR_EQUAL, new StaticConditionVariable($afterPreInsertRight)
                );

                $updateCondition = new AndCondition($conditions);

                $leftValueVariable = new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE);
                $rightValueVariable = new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE);

                $properties = [];

                $properties[] = new UpdateProperty(
                    $leftValueVariable, new OperationConditionVariable(
                        $leftValueVariable, OperationTypeEnum::ADDITION, new StaticConditionVariable($shift)
                    )
                );

                $properties[] = new UpdateProperty(
                    $rightValueVariable, new OperationConditionVariable(
                        $rightValueVariable, OperationTypeEnum::ADDITION, new StaticConditionVariable($shift)
                    )
                );

                if (!$this->dataClassRepository->updates(
                    Group::class, new UpdateProperties($properties), $updateCondition
                )) {
                    return false;
                }

                // Step 3: Close the gap created by the "removal"
                // Having shifted the nodes to their new position, we have created an equally big gap in their original
                // position.
                // This gap is closed by invoking post_delete.

                // Set the left and right values so that it reflects the place they moved away from.
                $group->setLeftValue($afterPreInsertLeft);
                $group->setRightValue($afterPreInsertRight);

                if (!$this->__postDelete($group)) {
                    return false;
                }

                // Step 4: Update the parent id of the moved node.
                // This has already been performed in memory, but needs to be written to the database.

                // Set the left and right values to their final position, so the update does not alter them.
                $group->setLeftValue($finalLeft);
                $group->setRightValue($finalRight);

                if (!$this->dataClassRepository->update($group)) {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveAncestorsByGroup(Group $group, bool $includeSelf = true): ArrayCollection
    {
        return $this->dataClassRepository->retrieves(
            Group::class, new StorageParameters(
                condition: $this->determineAncestorsCondition($group, $includeSelf), orderBy: new OrderBy([
                new OrderProperty(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE)
                )
            ])
            )
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveAncestorsIdentifiersByGroup(Group $group, bool $includeSelf = true): array
    {
        return $this->dataClassRepository->distinct(
            Group::class, new StorageParameters(
                condition: $this->determineAncestorsCondition($group, $includeSelf),
                retrieveProperties: new RetrieveProperties(
                    [new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)]
                ), orderBy: new OrderBy([
                new OrderProperty(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE)
                )
            ])
            )
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $recursive
     *
     * @return  \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveDescendantsByGroup(Group $group, bool $recursive = false): ArrayCollection
    {
        return $this->dataClassRepository->retrieves(
            Group::class, new StorageParameters(
                condition: $this->determineDescendantsCondition($group, $recursive)
            )
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveDescendantsIdentifiersByGroup(Group $group, bool $recursive = false): array
    {
        if ($recursive) {
            $childrenCondition = [];

            $childrenCondition[] = new ComparisonCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE),
                ComparisonTypeEnum::GREATER_THAN, new StaticConditionVariable($group->getLeftValue())
            );

            $childrenCondition[] = new ComparisonCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE), ComparisonTypeEnum::LESS_THAN,
                new StaticConditionVariable($group->getRightValue())
            );

            $childrenCondition = new AndCondition($childrenCondition);
        }
        else {
            $childrenCondition = new EqualityCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                new StaticConditionVariable($group->getId())
            );
        }

        return $this->dataClassRepository->distinct(
            Group::class, new StorageParameters(
                condition: $childrenCondition, retrieveProperties: new RetrieveProperties(
                [new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)]
            )
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function retrieveGroupByCode(string $groupCode): ?Group
    {
        try {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE),
                new StaticConditionVariable($groupCode)
            );

            return $this->dataClassRepository->retrieve(
                Group::class, new StorageParameters(condition: $condition)
            );
        }
        catch (StorageNoResultException) {
            throw new NoSuchGroupException(
                [Group::PROPERTY_CODE => $groupCode]
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function retrieveGroupByCodeAndParentIdentifier(string $groupCode, string $parentIdentifier): ?Group
    {
        try {
            $conditions = [];
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE),
                new StaticConditionVariable($groupCode)
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                new StaticConditionVariable($parentIdentifier)
            );

            return $this->dataClassRepository->retrieve(
                Group::class, new StorageParameters(condition: new AndCondition($conditions))
            );
        }
        catch (StorageNoResultException) {
            throw new NoSuchGroupException(
                [Group::PROPERTY_CODE => $groupCode, Group::PROPERTY_PARENT_ID => $parentIdentifier]
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function retrieveGroupByIdentifier(string $groupId): ?Group
    {
        try {
            return $this->dataClassRepository->retrieveById(Group::class, $groupId);
        }
        catch (StorageNoResultException) {
            throw new NoSuchGroupException(
                [DataClass::PROPERTY_ID => $groupId]
            );
        }
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface $condition
     * @param ?int $count
     * @param ?int $offset
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroups(
        ?ConditionInterface $condition = null, ?int $count = null, ?int $offset = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        $parameters = new StorageParameters(condition: $condition, orderBy: $orderBy, count: $count, offset: $offset);

        return $this->dataClassRepository->retrieves(Group::class, $parameters);
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupsByIdentifiersOrderedByName(array $groupIdentifiers): ArrayCollection
    {
        $condition =
            new InCondition(new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID), $groupIdentifiers);

        return $this->dataClassRepository->retrieves(
            Group::class, new StorageParameters(
                condition: $condition, orderBy: new OrderBy(
                [new OrderProperty(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME))]
            )
            )
        );
    }

    /**
     * @param ?string $searchQuery
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupsBySearchQueryAndParentIdentifier(
        ?string $searchQuery = null, string $parentIdentifier = DataClass::EMPTY_UUID
    ): ArrayCollection
    {
        $conditions = [];

        if ($searchQuery && $searchQuery != '') {
            $conditions[] = $this->searchQueryConditionGenerator->getSearchConditions(
                $searchQuery, [
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME),
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE)
                ]
            );
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentIdentifier)
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieves(
            Group::class, new StorageParameters(
                condition: $condition, orderBy: new OrderBy(
                [new OrderProperty(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME))]
            )
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function retrieveRootGroup(): ?Group
    {
        try {
            return $this->dataClassRepository->retrieve(
                Group::class, new StorageParameters(
                    condition: new EqualityCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                        new StaticConditionVariable(DataClass::EMPTY_UUID)
                    )
                )
            );
        }
        catch (StorageNoResultException) {
            throw new NoSuchGroupException([Group::PROPERTY_PARENT_ID => DataClass::EMPTY_UUID]);
        }
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveSiblingsByGroup(
        Group $group, bool $includeSelf = true, ?ConditionInterface $condition = null
    ): ArrayCollection
    {
        return $this->dataClassRepository->retrieves(
            Group::class, new StorageParameters(
                condition: $this->determineSiblingsCondition($group, $includeSelf, $condition), orderBy: new OrderBy([
                new OrderProperty(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE)
                )
            ])
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateGroup(Group $group): bool
    {
        return $this->dataClassRepository->update($group);
    }
}