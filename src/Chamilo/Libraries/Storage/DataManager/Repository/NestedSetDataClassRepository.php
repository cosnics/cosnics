<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Repository
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NestedSetDataClassRepository extends DataClassRepository
{
    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param boolean $includeSelf
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @see NestedTreeNode::count_parents()
     * @see NestedSet::count_ancestors()
     */
    public function countAncestors(NestedSet $nestedSet, bool $includeSelf = true, Condition $condition = null)
    {
        return $this->count(
            get_class($nestedSet),
            new DataClassCountParameters($this->getAncestorsCondition($nestedSet, $includeSelf, $condition))
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param boolean $recursive
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @see NestedTreeNode::count_children()
     * @see NestedSet::count_children()
     * @see NestedSet::count_descendants()
     */
    public function countDescendants(NestedSet $nestedSet, $recursive = true, $condition = null)
    {
        return $this->count(
            get_class($nestedSet),
            new DataClassCountParameters($this->getDescendantsCondition($nestedSet, $recursive, $condition))
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param boolean $includeSelf
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @see NestedSet::count_siblings()
     */
    public function countSiblings(NestedSet $nestedSet, bool $includeSelf = true, Condition $condition = null)
    {
        return $this->count(
            get_class($nestedSet),
            new DataClassCountParameters($this->getSiblingsCondition($nestedSet, $includeSelf, $condition))
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param int $nestedSetIdentifier
     *
     * @return \Chamilo\Libraries\Storage\DataClass\NestedSet
     */
    public function findRelatedNestedSetByIdentifier(NestedSet $nestedSet, int $nestedSetIdentifier)
    {
        return $this->retrieveById(get_class($nestedSet), $nestedSetIdentifier);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param int $previousNestedSetIdentifier
     *
     * @return bool
     * @see NestedSet::create()
     * @see NestedTreeNode::create()
     */
    public function create(DataClass $nestedSet, $previousNestedSetIdentifier = 0)
    {
        if ($previousNestedSetIdentifier)
        {
            $position = NestedSet::AS_NEXT_SIBLING_OF;
            $referenceNode = $this->findRelatedNestedSetByIdentifier($nestedSet, $previousNestedSetIdentifier);
        }
        else
        {
            $position = NestedSet::AS_LAST_CHILD_OF;
            $referenceNode = $this->getParent($nestedSet);
        }

        // This variable is used to identify the node after which the newly
        // created node should be placed. This value is initialized with 0
        // which would create the node as the root of a nested set.
        $insertAfter = 0;

        if (!($position == NestedSet::AS_LAST_CHILD_OF && $referenceNode == 0))
        { // Not creating the root node of a hierarchy

            // Identify the reference node (except when creating the root node, there must be one).
            if ($this->validatePosition($nestedSet, $position, $referenceNode) === null)
            {
                return false;
            }

            switch ($position)
            {
                case NestedSet::AS_FIRST_CHILD_OF :
                    $insertAfter = $referenceNode->getLeftValue();
                    break;

                case NestedSet::AS_LAST_CHILD_OF :
                    $insertAfter = $referenceNode->getRightValue() - 1;
                    break;

                case NestedSet::AS_PREVIOUS_SIBLING_OF :
                    $insertAfter = $referenceNode->getLeftValue() - 1;
                    break;

                case NestedSet::AS_NEXT_SIBLING_OF :
                    $insertAfter = $referenceNode->getRightValue();
                    break;
            }
        }

        // Creating a node in a nested set requires multiple updates
        // which have to be performed atomically and consistently.
        //
        // Use a transaction to guarantee this.

        return $this->transactional(
            function ($c) use ($nestedSet, $insertAfter) { // Correct the left and right values wherever necessary.
                if (!$this->preInsert($nestedSet, $insertAfter, 1))
                {
                    return false;
                }

                // Left and right values have been shifted so now we
                // want to really add the location itself, but first
                // we have to set it's left and right value.
                $nestedSet->setLeftValue($insertAfter + 1);
                $nestedSet->setRightValue($insertAfter + 2);

                return parent::create($nestedSet);
            }
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\DataClass\NestedSet[]
     * @see NestedSet::delete()
     */
    public function delete(DataClass $nestedSet, $condition = null)
    {
        // Deleting a node from a nested set requires multiple updates which have to be performed atomically and
        // consistently. Use a transaction to guarantee this.

        return $this->transactional(
            function ($c) use ($nestedSet, $condition) {
                // Since we want to hold on to this information until after all nodes have been deleted
                // We have to copy the content of this result set into a temporary array

                $associatedNestedSets = $this->findDescendants($nestedSet, true, $condition);
                $associatedNestedSets->append($nestedSet);

                $deleteCondition = $this->getDescendantsCondition($nestedSet, true, true, $condition);

                // Delete this node as well as its offspring
                if (!$this->deletes(get_class($nestedSet), $deleteCondition))
                {
                    return false;
                }

                // Shift the remaining nodes left to fill the gap created by deleting this node and its offspring.
                if (!$this->postDelete($nestedSet, $condition))
                {
                    return false;
                }

                return $associatedNestedSets;
            }
        );
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param boolean $includeSelf
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\DataClass\NestedSet[]
     */
    public function findAncestors(NestedSet $nestedSet, bool $includeSelf = true, Condition $condition = null)
    {
        return $this->retrieves(
            get_class($nestedSet), new DataClassRetrievesParameters(
                $this->getAncestorsCondition($nestedSet, $includeSelf, $condition), null, null,
                $this->getPostOrderBy($nestedSet)
            )
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param boolean $recursive
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\DataClass\NestedSet[]
     * @see NestedSet::get_descendants()
     * @see NestedSet::get_children()
     */
    public function findDescendants(NestedSet $nestedSet, bool $recursive = true, Condition $condition = null)
    {
        return $this->retrieves(
            get_class($nestedSet), new DataClassRetrievesParameters(
                $this->getDescendantsCondition($nestedSet, $recursive, false, $condition)
            )
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param boolean $includeSelf
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\DataClass\NestedSet[]
     * @see NestedSet::get_siblings()
     */
    public function findSiblings(NestedSet $nestedSet, bool $includeSelf = true, Condition $condition = null)
    {
        return $this->retrieves(
            get_class($nestedSet), $this->getSiblingsCondition($nestedSet, $includeSelf, $condition), null, null,
            $this->getPreOrderBy()
        );
    }

    /**
     * Build the conditions for the get / count _ ancestors methods
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param boolean $includeSelf Whether or not the current node is to be added to the list of ancestors
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition Any additional conditions imposed by the
     *        query
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     * @see NestedSet::build_ancestry_condition()
     */
    protected function getAncestorsCondition(
        NestedSet $nestedSet, bool $includeSelf = false, Condition $condition = null
    )
    {
        $conditions = array();

        $subtreeCondition = $this->getSubTreeCondition($nestedSet);

        if ($subtreeCondition instanceof Condition)
        {
            $conditions[] = $subtreeCondition;
        }

        if ($includeSelf)
        {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_LEFT_VALUE),
                ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable($nestedSet->getLeftValue())
            );
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_RIGHT_VALUE),
                ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($nestedSet->getRightValue())
            );
        }
        else
        {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_LEFT_VALUE),
                ComparisonCondition::LESS_THAN, new StaticConditionVariable($nestedSet->getLeftValue())
            );
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_RIGHT_VALUE),
                ComparisonCondition::GREATER_THAN, new StaticConditionVariable($nestedSet->getRightValue())
            );
        }

        if ($condition)
        {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
    }

    /**
     * Build the conditions for the get / count _ children / descendants methods
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param boolean $recursive - whether to find all descendants using left / right values or only the node's
     *        immediate children using parent_id
     * @param boolean $includeSelf
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition - any additional conditions imposed by the
     *        query
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     * @see NestedSet::build_offspring_condition()
     */
    protected function getDescendantsCondition(
        NestedSet $nestedSet, bool $recursive = false, bool $includeSelf = false, Condition $condition = null
    )
    {
        $conditions = array();

        $subtreeCondition = $this->getSubTreeCondition($nestedSet);

        if ($subtreeCondition instanceof Condition)
        {
            $conditions[] = $subtreeCondition;
        }

        if ($recursive)
        {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_LEFT_VALUE),
                $includeSelf ? ComparisonCondition::GREATER_THAN_OR_EQUAL : ComparisonCondition::GREATER_THAN,
                new StaticConditionVariable($nestedSet->getLeftValue())
            );

            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_RIGHT_VALUE),
                $includeSelf ? ComparisonCondition::LESS_THAN_OR_EQUAL : ComparisonCondition::LESS_THAN,
                new StaticConditionVariable($nestedSet->getRightValue())
            );
        }
        else
        {
            if ($includeSelf)
            {
                $conditions[] = new OrCondition(
                    array(
                        new EqualityCondition(
                            new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_ID),
                            new StaticConditionVariable($nestedSet->getId())
                        ), new EqualityCondition(
                        new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_PARENT_ID),
                        new StaticConditionVariable($nestedSet->getId())
                    )
                    )
                );
            }
            else
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_PARENT_ID),
                    new StaticConditionVariable($nestedSet->getId())
                );
            }
        }

        if ($condition)
        {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition|null
     * @see NestedSet::get_nested_set_condition_array()
     */
    protected function getSubTreeCondition(NestedSet $nestedSet)
    {
        $subTreePropertyNames = $nestedSet->getSubTreePropertyNames();

        if (count($subTreePropertyNames) > 0)
        {
            $conditions = array();

            foreach ($subTreePropertyNames as $subTreePropertyName)
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(get_class($nestedSet), $subTreePropertyName),
                    new StaticConditionVariable($nestedSet->get_default_property($subTreePropertyName))
                );
            }

            return new AndCondition($conditions);
        }
        else
        {
            return null;
        }
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     *
     * @return \Chamilo\Libraries\Storage\DataClass\NestedSet
     * @see NestedSet::get_parent();
     */
    public function getParent(NestedSet $nestedSet)
    {
        return $this->retrieveById(get_class($nestedSet), $nestedSet->getParentId());
    }

    /**
     * Orders the tree-structured data in post-order (i.e.
     * the order in which a depth-first traversal would leave the
     * nodes). When applied to a list of ancestors, this coincides with an inverse ordering according to the node's
     * level (leaf -> ... -> root). When applied to a list of siblings, this coincides with an ordering from right to
     * left.
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param integer $sortOrder
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy[]
     * @see NestedSet::build_post_order_ordering()
     */
    protected function getPostOrderBy(NestedSet $nestedSet, $sortOrder = SORT_ASC)
    {
        return array(
            new OrderBy(
                new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_RIGHT_VALUE), $sortOrder
            )
        );
    }

    /**
     * Orders the tree-structured data in pre-order (i.e.
     * the order in which a depth-first traversal would enter the
     * nodes). When applied to a list of ancestors, this coincides with an ordering according to the node's level (root
     * -> ... -> leaf). When applied to a list of siblings, this coincides with an ordering from left to right.
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param integer $sortOrder
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy[]
     * @see NestedSet::build_pre_order_ordering()
     */
    protected function getPreOrderBy(NestedSet $nestedSet, $sortOrder = SORT_ASC)
    {
        return array(
            new OrderBy(
                new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_LEFT_VALUE), $sortOrder
            )
        );
    }

    /**
     * Build the conditions for the get / count _ siblings methods
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param boolean $includeSelf Whether or not the current node is to be added to the list of siblings
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition Any additional conditions imposed by the
     *        query
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     * @see NestedSet::build_sibling_condition()
     */
    protected function getSiblingsCondition(NestedSet $nestedSet, bool $includeSelf = false, Condition $condition = null
    )
    {
        $conditions = array();

        $subtreeCondition = $this->getSubTreeCondition($nestedSet);

        if ($subtreeCondition instanceof Condition)
        {
            $conditions[] = $subtreeCondition;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_PARENT_ID),
            new StaticConditionVariable($nestedSet->getParentId())
        );

        if (!$includeSelf)
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_ID),
                    new StaticConditionVariable($nestedSet->getId())
                )
            );
        }

        if ($condition)
        {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     */
    public function hasSiblings(NestedSet $nestedSet, Condition $condition = null)
    {
        return ($this->countSiblings($nestedSet, false, $condition) > 0);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param int $newParentId
     * @param int $newPreviousId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return bool|mixed
     * @see NestedSet::move()
     * @see NestedTreeNode::move()
     */
    public function move(DataClass $nestedSet, $newParentId = 0, $newPreviousId = 0, $condition = null)
    {
        if ($newPreviousId != 0)
        {
            $position = NestedSet::AS_NEXT_SIBLING_OF;
            $referenceNode = $this->findRelatedNestedSetByIdentifier($nestedSet, $newPreviousId);
        }
        else
        {
            if ($newParentId == 0)
            {
                $referenceNode = $this->getParent($nestedSet);
            }
            else
            {
                $referenceNode = $this->findRelatedNestedSetByIdentifier($nestedSet, $newParentId);
            }

            $position = NestedSet::AS_LAST_CHILD_OF;
        }

        if ($this->validatePosition($nestedSet, $position, $referenceNode) === null)
        {
            return false;
        }

        // This variable is used to identify the node after which the newly
        // created node should be placed. This value is initialized with 0
        // which would create the node as the root of a nested set.
        $insertAfter = 0;

        switch ($position)
        {
            case NestedSet::AS_FIRST_CHILD_OF :
                $insertAfter = $referenceNode->getLeftValue();
                break;

            case NestedSet::AS_LAST_CHILD_OF :
                $insertAfter = $referenceNode->getRightValue() - 1;
                break;

            case NestedSet::AS_PREVIOUS_SIBLING_OF :
                $insertAfter = $referenceNode->getLeftValue() - 1;
                $nestedSet->setParentId($referenceNode->getId());
                break;

            case NestedSet::AS_NEXT_SIBLING_OF :
                $insertAfter = $referenceNode->getRightValue();
                $nestedSet->setParentId($referenceNode->getId());
                break;
        }

        // Moving a node in a nested set requires multiple updates
        // which have to be performed atomically and consistently.
        //
        // Use a transaction to guarantee this.

        return $this->transactional(
            function ($c) use ($nestedSet, $insertAfter, $condition
            ) { // Step 0: Compute the auxiliary values used by this
                // algorithm
                // This is the initial position of the node to be moved
                $initialLeft = $nestedSet->getLeftValue();
                $initialRight = $nestedSet->getRightValue();

                // This is the size of the subtree to be moved (i.e. the size of the gap to be created so that it can be
                // moved in)
                $delta = $nestedSet->getRightValue() - $nestedSet->getLeftValue() + 1;

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
                $res = $this->preInsert($nestedSet, $insertAfter, $delta / 2, $condition);

                if (!$res)
                {
                    return false;
                }

                // Step 2: Move the node and its offspring to fill the newly created gap
                $conditions = array();

                $subtreeCondition = $this->getSubTreeCondition($nestedSet);

                if ($subtreeCondition instanceof Condition)
                {
                    $conditions[] = $subtreeCondition;
                }

                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable($nestedSet::class_name(), NestedSet::PROPERTY_LEFT_VALUE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($afterPreInsertLeft)
                );
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable($nestedSet::class_name(), NestedSet::PROPERTY_RIGHT_VALUE),
                    ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable($afterPreInsertRight)
                );

                if ($condition)
                {
                    $conditions[] = $condition;
                }

                $updateCondition = new AndCondition($conditions);

                $leftValueVariable = new PropertyConditionVariable(
                    get_class($nestedSet), NestedSet::PROPERTY_LEFT_VALUE
                );
                $rightValueVariable = new PropertyConditionVariable(
                    get_class($nestedSet), NestedSet::PROPERTY_RIGHT_VALUE
                );

                $properties = array();

                $properties[] = new DataClassProperty(
                    $leftValueVariable, new OperationConditionVariable(
                        $leftValueVariable, OperationConditionVariable::ADDITION, new StaticConditionVariable($shift)
                    )
                );

                $properties[] = new DataClassProperty(
                    $rightValueVariable, new OperationConditionVariable(
                        $rightValueVariable, OperationConditionVariable::ADDITION, new StaticConditionVariable($shift)
                    )
                );

                if (!$this->updates(get_class($nestedSet), new DataClassProperties($properties), $updateCondition))
                {
                    return false;
                }

                // Step 3: Close the gap created by the "removal"
                // Having shifted the nodes to their new position, we have created an equally big gap in their original
                // position.
                // This gap is closed by invoking post_delete.

                // Set the left and right values so that it reflects the place they moved away from.
                $nestedSet->setLeftValue($afterPreInsertLeft);
                $nestedSet->setRightValue($afterPreInsertRight);

                if (!$this->postDelete($nestedSet, $condition))
                {
                    return false;
                }

                // Step 4: Update the parent id of the moved node.
                // This has already been performed in memory, but needs to be written to the database.

                // Set the left and right values to their final position, so the update does not alter them.
                $nestedSet->setLeftValue($finalLeft);
                $nestedSet->setRightValue($finalRight);

                if (!$this->update($nestedSet))
                {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * Change the left/right values in the tree of every node that is affected by to the delete of this node
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition additional condition
     *
     * @return boolean
     * @see NestedSet::post_delete()
     */
    protected function postDelete(NestedSet $nestedSet, $condition = null)
    {
        // This private function is only ever called from within a transaction.
        //
        // This needs to be a transaction: both updates should either commit or abort.
        // Now, it is possible that the first update succeeds, but the latter doesn't.
        // This implies that we may end up with an inconsistent nested set.
        $delta = $nestedSet->getRightValue() - $nestedSet->getLeftValue() + 1;

        // 1. Update the left and right values of all successors of the deleted node.
        // A successor has a left-value which is higher than the left-value of the deleted node.

        $conditions = array();

        $subtreeCondition = $this->getSubTreeCondition($nestedSet);

        if ($subtreeCondition instanceof Condition)
        {
            $conditions[] = $subtreeCondition;
        }

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_LEFT_VALUE),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($nestedSet->getLeftValue())
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $updateCondition = new AndCondition($conditions);

        $leftValueVariable = new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_LEFT_VALUE);
        $rightValueVariable = new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_RIGHT_VALUE);

        $rightValueDataClassProperty = new DataClassProperty(
            $rightValueVariable, new OperationConditionVariable(
                $rightValueVariable, OperationConditionVariable::MINUS, new StaticConditionVariable($delta)
            )
        );

        $properties = array();
        $properties[] = $rightValueDataClassProperty;
        $properties[] = new DataClassProperty(
            $leftValueVariable, new OperationConditionVariable(
                $leftValueVariable, OperationConditionVariable::MINUS, new StaticConditionVariable($delta)
            )
        );

        if (!$this->updates(get_class($nestedSet), new DataClassProperties($properties), $updateCondition))
        {
            return false;
        }

        // 2. Update the right values of all ancestors of the deleted node.
        // An ancestor has a left value less than the left value of the deleted node
        // and a right value greater than the right value of the deleted node

        $conditions = array();

        $subtreeCondition = $this->getSubTreeCondition($nestedSet);

        if ($subtreeCondition instanceof Condition)
        {
            $conditions[] = $subtreeCondition;
        }

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_LEFT_VALUE),
            ComparisonCondition::LESS_THAN, new StaticConditionVariable($nestedSet->getLeftValue())
        );

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_RIGHT_VALUE),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($nestedSet->getRightValue())
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $updateCondition = new AndCondition($conditions);

        $properties = array();
        $properties[] = $rightValueDataClassProperty;

        if (!$this->updates(get_class($nestedSet), new DataClassProperties($properties), $updateCondition))
        {
            return false;
        }

        return true;
    }

    /**
     * Creates the necessary room to insert a number of values (1 by default) into the nested set: it shifts the
     * left/right values of all nodes that are traversed after the insertion point to the right.
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param integer $insertAfter the right value of the node who will act as the left sibling of the left-most
     *        inserted
     *        node. In absence of such a left sibling, the left value of the parent of the left-most inserted node.
     * @param integer $numberOfElements the number of elements that have to be inserted
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition additional condition (which filters out
     *        some nodes that will not be updated: should
     *        only be used when supporting multiple roots in a single database table)
     *
     * @return boolean
     * @see NestedSet::pre_insert()
     */
    protected function preInsert(
        NestedSet $nestedSet, $insertAfter, int $numberOfElements = 1, Condition $condition = null
    )
    {
        // This private function is only ever called from within a transaction.
        //
        // This needs to be a transaction: both updates should either commit or abort.
        // Now, it is possible that the first update succeeds, but the latter doesn't.
        // This implies that we may end up with an inconsistent nested set.

        // Update all necessary left-values
        $conditions = array();

        $subtreeCondition = $this->getSubTreeCondition($nestedSet);

        if ($subtreeCondition instanceof Condition)
        {
            $conditions[] = $subtreeCondition;
        }

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_LEFT_VALUE),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($insertAfter)
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $updateCondition = new AndCondition($conditions);

        $leftValueVariable = new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_LEFT_VALUE);

        $properties = array();
        $properties[] = new DataClassProperty(
            $leftValueVariable, new OperationConditionVariable(
                $leftValueVariable, OperationConditionVariable::ADDITION,
                new StaticConditionVariable($numberOfElements * 2)
            )
        );

        if (!$this->updates(get_class($nestedSet), new DataClassProperties($properties), $updateCondition))
        {
            return false;
        }

        // Update all necessary right-values
        $conditions = array();

        $subtreeCondition = $this->getSubTreeCondition($nestedSet);

        if ($subtreeCondition instanceof Condition)
        {
            $conditions[] = $subtreeCondition;
        }

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_RIGHT_VALUE),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($insertAfter)
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $updateCondition = new AndCondition($conditions);

        $rightValueVariable = new PropertyConditionVariable(get_class($nestedSet), NestedSet::PROPERTY_RIGHT_VALUE);

        $properties = array();
        $properties[] = new DataClassProperty(
            $rightValueVariable, new OperationConditionVariable(
                $rightValueVariable, OperationConditionVariable::ADDITION,
                new StaticConditionVariable($numberOfElements * 2)
            )
        );

        if (!$this->updates(get_class($nestedSet), new DataClassProperties($properties), $updateCondition))
        {
            return false;
        }

        return true;
    }

    /**
     * Validates a relative position of a node, which is used when creating or moving a node.
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     * @param integer $position where to insert/move w.r.t. the reference node
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $referenceNode null, an id or a node object that
     *        identifies the postion in the nested set.
     *
     * @return \Chamilo\Libraries\Storage\DataClass\NestedSet the node that identifies the position where to insert a
     *         new/moved node
     * @see NestedSet::validate_position()
     */
    protected function validatePosition(
        NestedSet $nestedSet, $position = NestedSet::AS_LAST_CHILD_OF, NestedSet $referenceNode = null
    )
    {
        if ($position == NestedSet::AS_PREVIOUS_SIBLING_OF || $position == NestedSet::AS_NEXT_SIBLING_OF)
        {
            if ($referenceNode === null)
            {
                // TODO Report an error: must provide a relative position, when create a node as a sibling to another.
                return null;
            }

            if ($nestedSet->getId() === $referenceNode->getId())
            {
                // TODO Report an error when attempting to create a node as its own sibling
                return null;
            }

            if ($nestedSet->getParentId() == 0 || $nestedSet->getParentId() != $referenceNode->getParentId())
            {
                // To be a sibling of the reference node, the parent should be the same
                $nestedSet->setParentId($referenceNode->getParentId());
            }
        }

        if ($position == NestedSet::AS_FIRST_CHILD_OF || $position == NestedSet::AS_LAST_CHILD_OF)
        {
            if ($referenceNode === null)
            {
                // Use the parent of the node as a reference
                $referenceNode = $this->getParent($nestedSet);
            }

            if ($nestedSet->getId() === $referenceNode->getId())
            {
                // TODO Report an error when attempting to create a node as its own child
                return null;
            }

            if ($nestedSet->getParentId() == 0 || $nestedSet->getParentId() != $referenceNode->getId())
            {
                // To be a child of the reference node, the parent should be set correctly
                $nestedSet->setParentId($referenceNode->getId());
            }
        }

        return $referenceNode;
    }
}