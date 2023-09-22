<?php
namespace Chamilo\Libraries\Storage\DataClass;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Query\UpdateProperty;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This class extends Dataclass to provide auxiliary methods which allows using its subclasses as tree-structured data.
 * It is aimed to replace nested_tree_node and all ad hoc implementations.
 *
 * @package Chamilo\Libraries\Storage\DataClass
 */
abstract class NestedSet extends DataClass
{
    public const AS_FIRST_CHILD_OF = 1;
    public const AS_LAST_CHILD_OF = 2;
    public const AS_NEXT_SIBLING_OF = 4;
    public const AS_PREVIOUS_SIBLING_OF = 3;

    public const CONTEXT = StringUtilities::LIBRARIES;

    public const PROPERTY_LEFT_VALUE = 'left_value';
    public const PROPERTY_PARENT_ID = 'parent_id';
    public const PROPERTY_RIGHT_VALUE = 'right_value';

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::getAncestorsCondition()
     */
    public function build_ancestry_condition(bool $include_object = false, ?Condition $condition = null): AndCondition
    {
        $parent_conditions = $this->get_nested_set_condition_array();

        if ($include_object)
        {
            $parent_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(static::class, self::PROPERTY_LEFT_VALUE),
                ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable($this->getLeftValue())
            );
            $parent_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(static::class, self::PROPERTY_RIGHT_VALUE),
                ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($this->getRightValue())
            );
        }
        else
        {
            $parent_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(static::class, self::PROPERTY_LEFT_VALUE), ComparisonCondition::LESS_THAN,
                new StaticConditionVariable($this->getLeftValue())
            );
            $parent_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(static::class, self::PROPERTY_RIGHT_VALUE),
                ComparisonCondition::GREATER_THAN, new StaticConditionVariable($this->getRightValue())
            );
        }

        if ($condition)
        {
            $parent_conditions[] = $condition;
        }

        return new AndCondition($parent_conditions);
    }

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::getDescendantsCondition()
     */
    public function build_offspring_condition(
        bool $recursive = false, bool $include_self = false, ?Condition $condition = null
    ): AndCondition
    {
        $children_conditions = $this->get_nested_set_condition_array();

        if ($recursive)
        {
            $children_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(static::class, self::PROPERTY_LEFT_VALUE),
                $include_self ? ComparisonCondition::GREATER_THAN_OR_EQUAL : ComparisonCondition::GREATER_THAN,
                new StaticConditionVariable($this->getLeftValue())
            );

            $children_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(static::class, self::PROPERTY_RIGHT_VALUE),
                $include_self ? ComparisonCondition::LESS_THAN_OR_EQUAL : ComparisonCondition::LESS_THAN,
                new StaticConditionVariable($this->getRightValue())
            );
        }
        elseif ($include_self)
        {
            $children_conditions[] = new OrCondition(
                [
                    new EqualityCondition(
                        new PropertyConditionVariable(static::class, self::PROPERTY_ID),
                        new StaticConditionVariable($this->get_id())
                    ),
                    new EqualityCondition(
                        new PropertyConditionVariable(static::class, self::PROPERTY_PARENT_ID),
                        new StaticConditionVariable($this->get_id())
                    )
                ]
            );
        }
        else
        {
            $children_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(static::class, self::PROPERTY_PARENT_ID),
                new StaticConditionVariable($this->get_id())
            );
        }

        if ($condition)
        {
            $children_conditions[] = $condition;
        }

        return new AndCondition($children_conditions);
    }

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::getPostOrderBy()
     */
    public function build_post_order_ordering(int $sort_order = SORT_ASC): OrderBy
    {
        return new OrderBy([
            new OrderProperty(
                new PropertyConditionVariable(static::class, self::PROPERTY_RIGHT_VALUE), $sort_order
            )
        ]);
    }

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::getPreOrderBy()
     */
    public function build_pre_order_ordering(int $sort_order = SORT_ASC): OrderBy
    {
        return new OrderBy([
            new OrderProperty(
                new PropertyConditionVariable(static::class, self::PROPERTY_LEFT_VALUE), $sort_order
            )
        ]);
    }

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::getSiblingsCondition()
     */
    public function build_sibling_condition(bool $include_object = false, ?Condition $condition = null): AndCondition
    {
        $sibling_conditions = $this->get_nested_set_condition_array();

        $sibling_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(static::class, self::PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->getParentId())
        );

        if (!$include_object)
        {
            $sibling_conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(static::class, self::PROPERTY_ID),
                    new StaticConditionVariable($this->get_id())
                )
            );
        }

        if ($condition)
        {
            $sibling_conditions[] = $condition;
        }

        return new AndCondition($sibling_conditions);
    }

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::countAncestors()
     */
    public function count_ancestors(bool $include_self = true, ?Condition $condition = null): int
    {
        return DataManager::count(
            get_class($this), new DataClassCountParameters($this->build_ancestry_condition($include_self, $condition))
        );
    }

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::countDescendants()
     */
    public function count_children(?Condition $condition = null): int
    {
        return DataManager::count(
            get_class($this), new DataClassCountParameters($this->build_offspring_condition(false, false, $condition))
        );
    }

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::countDescendants()
     */
    public function count_descendants(?Condition $condition = null): int
    {
        return DataManager::count(
            get_class($this), new DataClassCountParameters($this->build_offspring_condition(true, false, $condition))
        );
    }

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::countSiblings()
     */
    public function count_siblings(bool $include_self = true, ?Condition $condition = null): int
    {
        return DataManager::count(
            get_class($this), new DataClassCountParameters($this->build_sibling_condition($include_self, $condition))
        );
    }

    /**
     * @param mixed $reference_node
     *
     * @throws \Throwable
     * @see        \Chamilo\Libraries\Storage\DataClass\DataClass::create()
     * @deprecated Migrated to NestedSetDataClassRepository::create()
     */
    public function create(int $position = self::AS_LAST_CHILD_OF, $reference_node = null): bool
    {
        // This variable is used to identify the node after which the newly
        // created node should be placed. This value is initialized with 0
        // which would create the node as the root of a nested set.
        $insert_after = 0;

        if (!($position == self::AS_LAST_CHILD_OF && $reference_node == 0))
        { // Not creating the root node of a hierarchy

            // Identify the reference node (except when creating the root node, there must be one).
            if ($this->validate_position($position, $reference_node) === null)
            {
                return false;
            }

            switch ($position)
            {
                case self::AS_FIRST_CHILD_OF :
                    $insert_after = $reference_node->getLeftValue();
                    break;

                case self::AS_LAST_CHILD_OF :
                    $insert_after = $reference_node->getRightValue() - 1;
                    break;

                case self::AS_PREVIOUS_SIBLING_OF :
                    $insert_after = $reference_node->getLeftValue() - 1;
                    break;

                case self::AS_NEXT_SIBLING_OF :
                    $insert_after = $reference_node->getRightValue();
                    break;
            }
        }

        // Creating a node in a nested set requires multiple updates
        // which have to be performed atomically and consistently.
        //
        // Use a transaction to guarantee this.

        $nested_set = $this;

        return DataManager::transactional(
            function () use ($nested_set, $insert_after) { // Correct the left and right values wherever necessary.
                if (!$nested_set->pre_insert($insert_after))
                {
                    return false;
                }

                // Left and right values have been shifted so now we
                // want to really add the location itself, but first
                // we have to set it's left and right value.
                $nested_set->setLeftValue($insert_after + 1);
                $nested_set->setRightValue($insert_after + 2);

                // The call_user_func_array corresponds to parent::create()
                if (!call_user_func_array(
                    [$nested_set, '\Chamilo\Libraries\Storage\DataClass\DataClass::create'], []
                ))
                {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * @throws \Throwable
     * @see        \Chamilo\Libraries\Storage\DataClass\DataClass::delete()
     * @deprecated Migrated to NestedSetDataClassRepository::delete()
     */
    public function delete(?Condition $condition = null): bool
    {
        // Deleting a node from a nested set requires multiple updates
        // which have to be performed atomically and consistently.
        //
        // Use a transaction to guarantee this.
        $nested_set = $this;

        return DataManager::transactional(
            function () use ($condition, $nested_set) { // Keep track the descendants that need their related data
                // cleaned up.
                $descendants = $nested_set->get_descendants($condition);

                // Since we want to hold on to this information until after all nodes have been deleted
                // We have to copy the content of this result set into a temporary array
                $need_their_related_data_removed = [$nested_set];

                foreach ($descendants as $descendant)
                {
                    $need_their_related_data_removed[] = $descendant;
                }

                // Delete this node as well as its offspring
                if (!DataManager::deletes(
                    get_class($nested_set), $nested_set->build_offspring_condition(true, true, $condition)
                ))
                {
                    return false;
                }

                // Shift the remaining nodes left to fill the gap created by deleting this node and its offspring.
                if (!$nested_set->post_delete($condition))
                {
                    return false;
                }

                // Clean up the related data associated with the deleted node and its descendants.
                foreach ($need_their_related_data_removed as $node)
                {
                    if (!$node->delete_related_content())
                    {
                        return false;
                    }
                }

                return true;
            }
        );
    }

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::deleteNestedSetDependencies()
     */
    public function delete_related_content(): bool
    {
        return true;
    }

    /**
     * @done Not migrated since the inclusion of the identifier makes this a standard
     *     DataClassRepository::retrieveById()
     */
    public function find_by_id($object_or_id): NestedSet
    {
        if (is_object($object_or_id))
        {
            return $object_or_id;
        }

        $conditions = $this->get_nested_set_condition_array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(static::class, self::PROPERTY_ID), new StaticConditionVariable($object_or_id)
        );

        return DataManager::retrieve(
            get_class($this), new DataClassRetrieveParameters(new AndCondition($conditions))
        );
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_PARENT_ID;
        $extendedPropertyNames[] = self::PROPERTY_LEFT_VALUE;
        $extendedPropertyNames[] = self::PROPERTY_RIGHT_VALUE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public function getLeftValue(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_LEFT_VALUE);
    }

    public function getParentId(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_PARENT_ID);
    }

    public function getRightValue(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_RIGHT_VALUE);
    }

    /**
     * @return string[]
     */
    public function getSubTreePropertyNames(): array
    {
        return [];
    }

    /**
     * @param bool $include_self
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Storage\DataClass\NestedSet>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Migrated to NestedSetDataClassRepository::findAncestors()
     */
    public function get_ancestors(bool $include_self = true, ?Condition $condition = null): ArrayCollection
    {
        return DataManager::retrieves(
            get_class($this), new DataClassRetrievesParameters(
                $this->build_ancestry_condition($include_self, $condition), null, null,
                $this->build_post_order_ordering()
            )
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Storage\DataClass\NestedSet>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Migrated to NestedSetDataClassRepository::findDescendants()
     */
    public function get_children(?Condition $condition = null): ArrayCollection
    {
        return DataManager::retrieves(
            get_class($this),
            new DataClassRetrievesParameters($this->build_offspring_condition(false, false, $condition))
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Storage\DataClass\NestedSet>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Migrated to NestedSetDataClassRepository::findDescendants()
     */
    public function get_descendants(?Condition $condition = null): ArrayCollection
    {
        return DataManager::retrieves(
            get_class($this),
            new DataClassRetrievesParameters($this->build_offspring_condition(true, false, $condition))
        );
    }

    /**
     * @deprecated Use NestedSet::getLeftValue() now
     */
    public function get_left_value(): int
    {
        return $this->getLeftValue();
    }

    /**
     * Returns an array of conditions that specify which nested set should be queried/altered.
     * By default, it returns an
     * empty array. When representing multiple trees within a single table, one should return conditions that identify
     * the correct tree (root).
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     * @deprecated Migrated to NestedSetDataClassRepository::getSubTreeCondition()
     */
    public function get_nested_set_condition_array(): array
    {
        return [];
    }

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::getParent()
     */
    public function get_parent(): NestedSet
    {
        return $this->find_by_id($this->getParentId());
    }

    /**
     * @deprecated Use NestedSet::getParentId() now
     */
    public function get_parent_id(): string
    {
        return $this->getParentId();
    }

    /**
     * @deprecated Use NestedSet::getRightValue() now
     */
    public function get_right_value(): int
    {
        return $this->getRightValue();
    }

    /**
     * @param bool $include_self
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Storage\DataClass\NestedSet>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Migrated to NestedSetDataClassRepository::findSiblings()
     */
    public function get_siblings(bool $include_self = true, ?Condition $condition = null): ArrayCollection
    {
        return DataManager::retrieves(
            get_class($this), new DataClassRetrievesParameters(
                $this->build_sibling_condition($include_self, $condition), null, null, $this->build_pre_order_ordering()
            )
        );
    }

    public function hasChildren(): bool
    {
        return !($this->getLeftValue() == ($this->getRightValue() - 1));
    }

    /**
     * @deprecated Use NestedSet::hasChildren() now
     */
    public function has_children(): bool
    {
        return $this->hasChildren();
    }

    public function isAncestorOf(NestedSet $nestedSet): bool
    {
        if ($this->getLeftValue() < $nestedSet->getLeftValue() && $nestedSet->getRightValue() < $this->getRightValue())
        {
            return true;
        }

        return false;
    }

    public function isDescendantOf(NestedSet $nestedSet): bool
    {
        if ($this->getLeftValue() > $nestedSet->getLeftValue() && $nestedSet->getRightValue() > $this->getRightValue())
        {
            return true;
        }

        return false;
    }

    public function isRoot(): bool
    {
        return ($this->getParentId() == 0);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet|int $node_or_id
     *
     * @deprecated Use NestedSet::isAncestorOf() now
     */
    public function is_ancestor_of($node_or_id): bool
    {
        return $this->isAncestorOf($this->find_by_id($node_or_id));
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet|int $node_or_id
     *
     * @deprecated Use NestedSet::isDescendantOf() now
     */
    public function is_descendant_of($node_or_id): bool
    {
        return $this->isDescendantOf($this->find_by_id($node_or_id));
    }

    /**
     * @deprecated Use NestedSet::isRoot() now
     */
    public function is_root(): bool
    {
        return $this->isRoot();
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet|int $reference_node
     *
     * @throws \Exception
     * @throws \Throwable
     * @deprecated Migrated to NestedSetDataClassRepository::move()
     */
    public function move(int $position = self::AS_LAST_CHILD_OF, $reference_node = null, ?Condition $condition = null
    ): bool
    {
        if ($this->validate_position($position, $reference_node) === null)
        {
            return false;
        }

        // This variable is used to identify the node after which the newly
        // created node should be placed. This value is initialized with 0
        // which would create the node as the root of a nested set.
        $insert_after = 0;

        switch ($position)
        {
            case self::AS_FIRST_CHILD_OF :
                $insert_after = $reference_node->getLeftValue();
                break;

            case self::AS_LAST_CHILD_OF :
                $insert_after = $reference_node->getRightValue() - 1;
                break;

            case self::AS_PREVIOUS_SIBLING_OF :
                $insert_after = $reference_node->getLeftValue() - 1;
                $this->set_parent_id($reference_node);
                break;

            case self::AS_NEXT_SIBLING_OF :
                $insert_after = $reference_node->getRightValue();
                $this->set_parent_id($reference_node);
                break;
        }

        // Moving a node in a nested set requires multiple updates
        // which have to be performed atomically and consistently.
        //
        // Use a transaction to guarantee this.

        $nested_set = $this;

        return DataManager::transactional(
            function () use ($nested_set, $insert_after, $condition
            ) { // Step 0: Compute the auxiliary values used by this
                // algorithm
                // This is the initial position of the node to be moved
                $initial_left = $nested_set->getLeftValue();
                $initial_right = $nested_set->getRightValue();

                // This is the size of the subtree to be moved (i.e. the size of the gap to be created so that it can be
                // moved in)
                $delta = $nested_set->getRightValue() - $nested_set->getLeftValue() + 1;

                // When moving nodes left or up, the gap we have created will have incremented the left and right values
                // of the nodes to be moved by $delta.
                $after_pre_insert_left = ($insert_after > $initial_left) ? $initial_left : $initial_left + $delta;
                $after_pre_insert_right = ($insert_after > $initial_left) ? $initial_right : $initial_right + $delta;

                // How the nodes should move: negative numbers mean left or up, positive numbers mean right
                $shift = ($insert_after + 1) - $after_pre_insert_left;

                // This is where the node will end up in the end
                // When moving left or up, simply shift the previous position
                // When moving right, also account for the fact that post_delete will decrement the left and right
                // values of the moved nodes by $delta
                $final_left =
                    (($insert_after < $initial_left) ? $after_pre_insert_left : $after_pre_insert_left - $delta) +
                    $shift;
                $final_right =
                    (($insert_after < $initial_left) ? $after_pre_insert_right : $after_pre_insert_right - $delta) +
                    $shift;

                // Step 1: Create a gap where the node can be moved into.
                $res = $nested_set->pre_insert($insert_after, $delta / 2, $condition);

                if (!$res)
                {
                    return false;
                }

                // Step 2: Move the node and its offspring to fill the newly created gap
                $conditions = $nested_set->get_nested_set_condition_array();
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(get_class($nested_set), NestedSet::PROPERTY_LEFT_VALUE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($after_pre_insert_left)
                );
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(get_class($nested_set), NestedSet::PROPERTY_RIGHT_VALUE),
                    ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable($after_pre_insert_right)
                );

                if ($condition)
                {
                    $conditions[] = $condition;
                }

                $update_condition = new AndCondition($conditions);

                $left_value_variable = new PropertyConditionVariable(
                    get_class($nested_set), NestedSet::PROPERTY_LEFT_VALUE
                );
                $right_value_variable = new PropertyConditionVariable(
                    get_class($nested_set), NestedSet::PROPERTY_RIGHT_VALUE
                );

                $properties = [];

                $properties[] = new UpdateProperty(
                    $left_value_variable, new OperationConditionVariable(
                        $left_value_variable, OperationConditionVariable::ADDITION, new StaticConditionVariable($shift)
                    )
                );

                $properties[] = new UpdateProperty(
                    $right_value_variable, new OperationConditionVariable(
                        $right_value_variable, OperationConditionVariable::ADDITION, new StaticConditionVariable($shift)
                    )
                );

                $res = DataManager::updates(
                    get_class($nested_set), new UpdateProperties($properties), $update_condition
                );

                if (!$res)
                {
                    return false;
                }

                // Step 3: Close the gap created by the "removal"
                // Having shifted the nodes to their new position, we have created an equally big gap in their original
                // position.
                // This gap is closed by invoking post_delete.

                // Set the left and right values so that it reflects the place they moved away from.
                $nested_set->setLeftValue($after_pre_insert_left);
                $nested_set->setRightValue($after_pre_insert_right);

                $res = $nested_set->post_delete($condition);

                if (!$res)
                {
                    return false;
                }

                // Step 4: Update the parent id of the moved node.
                // This has already been performed in memory, but needs to be written to the database.

                // Set the left and right values to their final position, so the update does not alter them.
                $nested_set->setLeftValue($final_left);
                $nested_set->setRightValue($final_right);

                if (!$nested_set->update())
                {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::postDelete()
     */
    public function post_delete(?Condition $condition = null): bool
    {
        // This private function is only ever called from within a transaction.
        //
        // This needs to be a transaction: both updates should either commit or abort.
        // Now, it is possible that the first update succeeds, but the latter doesn't.
        // This implies that we may end up with an inconsistent nested set.
        $delta = $this->getRightValue() - $this->getLeftValue() + 1;

        // 1. Update the left and right values of all successors of the deleted node.
        // A successor has a left-value which is higher than the left-value of the deleted node.

        $conditions = $this->get_nested_set_condition_array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(static::class, self::PROPERTY_LEFT_VALUE), ComparisonCondition::GREATER_THAN,
            new StaticConditionVariable($this->getLeftValue())
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $left_value_variable = new PropertyConditionVariable(static::class, self::PROPERTY_LEFT_VALUE);
        $right_value_variable = new PropertyConditionVariable(static::class, self::PROPERTY_RIGHT_VALUE);

        $right_value_data_class_property = new UpdateProperty(
            $right_value_variable, new OperationConditionVariable(
                $right_value_variable, OperationConditionVariable::MINUS, new StaticConditionVariable($delta)
            )
        );

        $properties = [];
        $properties[] = $right_value_data_class_property;
        $properties[] = new UpdateProperty(
            $left_value_variable, new OperationConditionVariable(
                $left_value_variable, OperationConditionVariable::MINUS, new StaticConditionVariable($delta)
            )
        );

        $res = DataManager::updates(
            get_class($this), new UpdateProperties($properties), $update_condition
        );

        if (!$res)
        {
            return false;
        }

        // 2. Update the right values of all ancestors of the deleted node.
        // An ancestor has a left value less than the left value of the deleted node
        // and a right value greater than the right value of the deleted node

        $conditions = $this->get_nested_set_condition_array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(static::class, self::PROPERTY_LEFT_VALUE), ComparisonCondition::LESS_THAN,
            new StaticConditionVariable($this->getLeftValue())
        );

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(static::class, self::PROPERTY_RIGHT_VALUE), ComparisonCondition::GREATER_THAN,
            new StaticConditionVariable($this->getRightValue())
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $properties = [];
        $properties[] = $right_value_data_class_property;

        $res = DataManager::updates(
            get_class($this), new UpdateProperties($properties), $update_condition
        );

        if (!$res)
        {
            return false;
        }

        return true;
    }

    /**
     * @deprecated Migrated to NestedSetDataClassRepository::preInsert()
     */
    public function pre_insert(int $insert_after, int $number_of_elements = 1, ?Condition $condition = null): bool
    {
        // This private function is only ever called from within a transaction.
        //
        // This needs to be a transaction: both updates should either commit or abort.
        // Now, it is possible that the first update succeeds, but the latter doesn't.
        // This implies that we may end up with an inconsistent nested set.

        // Update all necessary left-values
        $conditions = $this->get_nested_set_condition_array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(static::class, self::PROPERTY_LEFT_VALUE), ComparisonCondition::GREATER_THAN,
            new StaticConditionVariable($insert_after)
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $left_value_variable = new PropertyConditionVariable(static::class, self::PROPERTY_LEFT_VALUE);

        $properties = [];
        $properties[] = new UpdateProperty(
            $left_value_variable, new OperationConditionVariable(
                $left_value_variable, OperationConditionVariable::ADDITION,
                new StaticConditionVariable($number_of_elements * 2)
            )
        );

        $res = DataManager::updates(
            get_class($this), new UpdateProperties($properties), $update_condition
        );

        if (!$res)
        {
            return false;
        }

        // Update all necessary right-values
        $conditions = $this->get_nested_set_condition_array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(static::class, self::PROPERTY_RIGHT_VALUE), ComparisonCondition::GREATER_THAN,
            new StaticConditionVariable($insert_after)
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $right_value_variable = new PropertyConditionVariable(static::class, self::PROPERTY_RIGHT_VALUE);

        $properties = [];

        $properties[] = new UpdateProperty(
            $right_value_variable, new OperationConditionVariable(
                $right_value_variable, OperationConditionVariable::ADDITION,
                new StaticConditionVariable($number_of_elements * 2)
            )
        );

        $res = DataManager::updates(
            get_class($this), new UpdateProperties($properties), $update_condition
        );

        if (!$res)
        {
            return false;
        }

        return true;
    }

    public function setLeftValue(int $leftValue)
    {
        $this->setDefaultProperty(self::PROPERTY_LEFT_VALUE, $leftValue);
    }

    public function setParentId(string $parentId)
    {
        $this->setDefaultProperty(self::PROPERTY_PARENT_ID, $parentId);
    }

    public function setRightValue(int $rightValue)
    {
        $this->setDefaultProperty(self::PROPERTY_RIGHT_VALUE, $rightValue);
    }

    /**
     * @deprecated Use NestedSet::setLeftValue() now
     */
    public function set_left_value(int $leftValue)
    {
        $this->setLeftValue($leftValue);
    }

    /**
     * @deprecated Use NestedSet::setParentId() now
     */
    public function set_parent_id(string $parentId)
    {
        $this->setParentId($parentId);
    }

    /**
     * @deprecated Use NestedSet::setRightValue() now
     */
    public function set_right_value(int $rightValue)
    {
        $this->setRightValue($rightValue);
    }

    /**
     * @param int $position
     * @param mixed $reference_node
     *
     * @return \Chamilo\Libraries\Storage\DataClass\NestedSet|null
     * @deprecated Migrated to NestedSetDataClassRepository::validatePosition()
     */
    public function validate_position(int $position = self::AS_LAST_CHILD_OF, &$reference_node = null): ?NestedSet
    {
        if ($position == self::AS_PREVIOUS_SIBLING_OF || $position == self::AS_NEXT_SIBLING_OF)
        {
            if ($reference_node === null)
            {
                // TODO Report an error: must provide a relative position, when create a node as a sibling to another.
                return null;
            }

            if (!is_object($reference_node))
            {
                // The passed reference_node may be an id: try to fetch the corresponding node
                $reference_node = $this->find_by_id($reference_node);

                // TODO Report an error if no such node could be found
                if (!is_object($reference_node))
                {
                    return null;
                }
            }

            if ($this->getId() === $reference_node->getId())
            {
                // TODO Report an error when attempting to create a node as its own sibling
                return null;
            }

            if ($this->getParentId() == 0 || $this->getParentId() != $reference_node->getParentId())
            {
                // To be a sibling of the reference node, the parent should be the same
                $this->setParentId($reference_node->getParentId());
            }
        }

        if ($position == self::AS_FIRST_CHILD_OF || $position == self::AS_LAST_CHILD_OF)
        {
            if ($reference_node === null)
            {
                // Use the parent of the node as a reference
                $reference_node = $this->find_by_id($this->getParentId());

                // TODO Report an error if no such node can be found
                if (!is_object($reference_node))
                {
                    return null;
                }
            }

            if (!is_object($reference_node))
            {
                // The passed reference_node may be an id: try to fetch the corresponding node
                $reference_node = $this->find_by_id($reference_node);

                // TODO Report an error if no such node could be found
                if (!is_object($reference_node))
                {
                    return null;
                }
            }

            if ($this->getId() === $reference_node->getId())
            {
                // TODO Report an error when attempting to create a node as its own child
                return null;
            }

            if ($this->getParentId() == 0 || $this->getParentId() != $reference_node->getId())
            {
                // To be a child of the reference node, the parent should be set correctly
                $this->setParentId($reference_node->getId());
            }
        }

        return $reference_node;
    }
}
