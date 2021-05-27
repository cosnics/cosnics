<?php
namespace Chamilo\Libraries\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class extends Dataclass to provide auxiliary methods which allows using its subclasses as tree-structured data.
 * It is aimed to replace nested_tree_node and all ad hoc implementations.
 *
 * @package Chamilo\Libraries\Storage\DataClass
 */
abstract class NestedSet extends DataClass
{
    const AS_FIRST_CHILD_OF = 1;

    const AS_LAST_CHILD_OF = 2;

    const AS_NEXT_SIBLING_OF = 4;

    const AS_PREVIOUS_SIBLING_OF = 3;

    const PROPERTY_LEFT_VALUE = 'left_value';

    const PROPERTY_PARENT_ID = 'parent_id';

    const PROPERTY_RIGHT_VALUE = 'right_value';

    /**
     * Build the conditions for the get / count _ ancestors methods
     *
     * @param boolean $include_object Whether or not the current node is to be added to the list of ancestors
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition Any additional conditions imposed by the
     *        query
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::getAncestorsCondition()
     */
    public function build_ancestry_condition($include_object = false, $condition = null)
    {
        $parent_conditions = $this->get_nested_set_condition_array();

        if ($include_object)
        {
            $parent_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(self::class_name(), self::PROPERTY_LEFT_VALUE),
                ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable($this->get_left_value())
            );
            $parent_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(self::class_name(), self::PROPERTY_RIGHT_VALUE),
                ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($this->get_right_value())
            );
        }
        else
        {
            $parent_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(self::class_name(), self::PROPERTY_LEFT_VALUE),
                ComparisonCondition::LESS_THAN, new StaticConditionVariable($this->get_left_value())
            );
            $parent_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(self::class_name(), self::PROPERTY_RIGHT_VALUE),
                ComparisonCondition::GREATER_THAN, new StaticConditionVariable($this->get_right_value())
            );
        }

        if ($condition)
        {
            $parent_conditions[] = $condition;
        }

        return new AndCondition($parent_conditions);
    }

    /**
     * Build the conditions for the get / count _ children / descendants methods
     *
     * @param boolean $recursive - whether to find all descendants using left / right values or only the node's
     *        immediate children using parent_id
     * @param boolean $include_self
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition - any additional conditions imposed by the
     *        query
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     * @deprecated Migrated to NestedSetDataClassRepository::getDescendantsCondition()
     * @throws \ReflectionException
     */
    public function build_offspring_condition($recursive = false, $include_self = false, $condition = null)
    {
        $children_conditions = $this->get_nested_set_condition_array();

        if ($recursive)
        {
            $children_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(self::class_name(), self::PROPERTY_LEFT_VALUE),
                $include_self ? ComparisonCondition::GREATER_THAN_OR_EQUAL : ComparisonCondition::GREATER_THAN,
                new StaticConditionVariable($this->get_left_value())
            );

            $children_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(self::class_name(), self::PROPERTY_RIGHT_VALUE),
                $include_self ? ComparisonCondition::LESS_THAN_OR_EQUAL : ComparisonCondition::LESS_THAN,
                new StaticConditionVariable($this->get_right_value())
            );
        }
        else
        {
            if ($include_self)
            {
                $children_conditions[] = new OrCondition(
                    array(
                        new EqualityCondition(
                            new PropertyConditionVariable(self::class_name(), self::PROPERTY_ID),
                            new StaticConditionVariable($this->get_id())
                        ),
                        new EqualityCondition(
                            new PropertyConditionVariable(self::class_name(), self::PROPERTY_PARENT_ID),
                            new StaticConditionVariable($this->get_id())
                        )
                    )
                );
            }
            else
            {
                $children_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(self::class_name(), self::PROPERTY_PARENT_ID),
                    new StaticConditionVariable($this->get_id())
                );
            }
        }

        if ($condition)
        {
            $children_conditions[] = $condition;
        }

        return new AndCondition($children_conditions);
    }

    /**
     * Orders the tree-structured data in post-order (i.e.
     * the order in which a depth-first traversal would leave the
     * nodes). When applied to a list of ancestors, this coincides with an inverse ordering according to the node's
     * level (leaf -> ... -> root). When applied to a list of siblings, this coincides with an ordering from right to
     * left.
     *
     * @param integer $sort_order
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy[]
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::getPostOrderBy()
     */
    public function build_post_order_ordering($sort_order = SORT_ASC)
    {
        return array(
            new OrderBy(new PropertyConditionVariable(self::class_name(), self::PROPERTY_RIGHT_VALUE), $sort_order)
        );
    }

    /**
     * Orders the tree-structured data in pre-order (i.e.
     * the order in which a depth-first traversal would enter the
     * nodes). When applied to a list of ancestors, this coincides with an ordering according to the node's level (root
     * -> ... -> leaf). When applied to a list of siblings, this coincides with an ordering from left to right.
     *
     * @param integer $sort_order
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy[]
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::getPreOrderBy()
     */
    public function build_pre_order_ordering($sort_order = SORT_ASC)
    {
        return array(
            new OrderBy(new PropertyConditionVariable(self::class_name(), self::PROPERTY_LEFT_VALUE), $sort_order)
        );
    }

    /**
     * Build the conditions for the get / count _ siblings methods
     *
     * @param boolean $include_object Whether or not the current node is to be added to the list of siblings
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition Any additional conditions imposed by the
     *        query
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::getSiblingsCondition()
     */
    public function build_sibling_condition($include_object = false, $condition = null)
    {
        $sibling_conditions = $this->get_nested_set_condition_array();

        $sibling_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->get_parent_id())
        );

        if (!$include_object)
        {
            $sibling_conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(self::class_name(), self::PROPERTY_ID),
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
     *
     * @param boolean $include_self
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::countAncestors()
     */
    public function count_ancestors($include_self = true, $condition = null)
    {
        return DataManager::count(
            get_class($this), new DataClassCountParameters($this->build_ancestry_condition($include_self, $condition))
        );
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::countDescendants()
     */
    public function count_children($condition = null)
    {
        return DataManager::count(
            get_class($this), new DataClassCountParameters($this->build_offspring_condition(false, false, $condition))
        );
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::countDescendants()
     */
    public function count_descendants($condition = null)
    {
        return DataManager::count(
            get_class($this), new DataClassCountParameters($this->build_offspring_condition(true, false, $condition))
        );
    }

    /**
     *
     * @param boolean $include_self
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::countSiblings()
     */
    public function count_siblings($include_self = true, $condition = null)
    {
        return DataManager::count(
            get_class($this), new DataClassCountParameters($this->build_sibling_condition($include_self, $condition))
        );
    }

    /**
     * @param integer $position
     * @param mixed $reference_node
     *
     * @return boolean
     * @deprecated Migrated to NestedSetDataClassRepository::create()
     * @see \Chamilo\Libraries\Storage\DataClass\DataClass::create()
     */
    public function create($position = self::AS_LAST_CHILD_OF, $reference_node = null)
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
                    $insert_after = $reference_node->get_left_value();
                    break;

                case self::AS_LAST_CHILD_OF :
                    $insert_after = $reference_node->get_right_value() - 1;
                    break;

                case self::AS_PREVIOUS_SIBLING_OF :
                    $insert_after = $reference_node->get_left_value() - 1;
                    break;

                case self::AS_NEXT_SIBLING_OF :
                    $insert_after = $reference_node->get_right_value();
                    break;
            }
        }

        // Creating a node in a nested set requires multiple updates
        // which have to be performed atomically and consistently.
        //
        // Use a transaction to guarantee this.

        $nested_set = $this;

        return DataManager::transactional(
            function ($c) use ($nested_set, $insert_after) { // Correct the left and right values wherever necessary.
                if (!$nested_set->pre_insert($insert_after, 1))
                {
                    return false;
                }

                // Left and right values have been shifted so now we
                // want to really add the location itself, but first
                // we have to set it's left and right value.
                $nested_set->set_left_value($insert_after + 1);
                $nested_set->set_right_value($insert_after + 2);

                // The call_user_func_array corresponds to parent::create()
                if (!call_user_func_array(
                    array($nested_set, '\Chamilo\Libraries\Storage\DataClass\DataClass::create'), []
                ))
                {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     * @deprecated Migrated to NestedSetDataClassRepository::delete()
     * @see \Chamilo\Libraries\Storage\DataClass\DataClass::delete()
     */
    public function delete($condition = null)
    {
        // Deleting a node from a nested set requires multiple updates
        // which have to be performed atomically and consistently.
        //
        // Use a transaction to guarantee this.
        $nested_set = $this;

        return DataManager::transactional(
            function ($c) use ($condition, $nested_set) { // Keep track the descendants that need their related data
                // cleaned up.
                $descendants = $nested_set->get_descendants($condition);

                // Since we want to hold on to this information until after all nodes have been deleted
                // We have to copy the content of this result set into a temporary array
                $need_their_related_data_removed = array($nested_set);

                foreach($descendants as $descendant)
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
     * This method is a hook which allows subclasses to clean up related data that is stored in another table.
     * For instance, it could be used to remove the users in a group.
     *
     * @return boolean - true iff the cleanup succeeded.
     * @deprecated Migrated to NestedSetDataClassRepository::deleteNestedSetDependencies()
     */
    public function delete_related_content()
    {
        return true;
    }

    /**
     * When provided with an id, retrieves the corresponding object from the database
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|integer
     *
     * @return \Chamilo\Libraries\Storage\DataClass\NestedSet
     * @done Not migrated since the inclusion of the identifier makes this a standard
     *     DataClassRepository::retrieveById()
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function find_by_id($object_or_id)
    {
        if (is_object($object_or_id))
        {
            return $object_or_id;
        }

        $conditions = $this->get_nested_set_condition_array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_ID),
            new StaticConditionVariable($object_or_id)
        );

        return DataManager::retrieve(
            get_class($this), new DataClassRetrieveParameters(new AndCondition($conditions))
        );
    }

    /**
     *
     * @param integer $parent_id
     * @param string[][] $traversal_data
     *
     * @return string[][]
     * @done No longer used and this should not be here to begin with
     * @throws \ReflectionException
     */
    private function find_values_to_fix($parent_id = 0, &$traversal_data = array('count' => 1, 'updates' => []))
    {
        $groups = DataManager::retrieves(
            get_class($this), new DataClassRetrievesParameters(
                $this->build_offspring_condition(), null, null, $this->build_pre_order_ordering()
            )
        );

        foreach($groups as $group)
        {
            $update = false;

            if ($group->get_left_value() != $traversal_data['count'])
            {
                $group->set_left_value($traversal_data['count']);
                $update = true;
            }

            $traversal_data['count'] ++;

            self::fix_nested_values_batch($group->get_id());

            if ($group->get_right_value() != $traversal_data['count'])
            {
                $group->set_right_value($traversal_data['count']);
                $update = true;
            }

            if ($update)
            {
                $traversal_data['updates'][$group->get_id()] = array(
                    $group->get_left_value(),
                    $group->get_right_value()
                );
            }

            $traversal_data['count'] ++;
        }

        return $traversal_data;
    }

    /**
     * @done No longer used and this should not be here to begin with
     *
     * @param integer $parent_id
     *
     * @throws \ReflectionException
     */
    public function fix_nested_values_batch($parent_id = 0)
    {
        $traversal_data = $this->find_values_to_fix($parent_id);

        $left_updates = [];
        $right_updates = [];
        $where_condition = [];

        foreach ($traversal_data['updates'] as $id => $leftright)
        {
            $left_updates[] = 'WHEN ' . self::PROPERTY_ID . ' = ' . $id . ' THEN ' . $leftright[0];
            $right_updates[] = 'WHEN ' . self::PROPERTY_ID . ' = ' . $id . ' THEN ' . $leftright[1];
            $where_condition[] = $id;
        }

        $query = "UPDATE " . $this->get_table_name() . " SET " . self::PROPERTY_LEFT_VALUE . " = CASE " .
            implode('', $left_updates) . ", " . " SET " . self::PROPERTY_RIGHT_VALUE . " = CASE " .
            implode('', $right_updates) . ", " . " WHERE " . self::PROPERTY_ID . " IN ( " .
            implode(', ', $where_condition) . ")";
    }

    /**
     * Returns the pre-order entry time of the node.
     *
     * @return integer the left value.
     * @done Domain method
     */
    public function getLeftValue()
    {
        return $this->get_default_property(self::PROPERTY_LEFT_VALUE);
    }

    /**
     * Returns the id that identifies the node's parent in the tree.
     *
     * @return integer The parent's id.
     * @done Domain method
     */
    public function getParentId()
    {
        return $this->get_default_property(self::PROPERTY_PARENT_ID);
    }

    /**
     * Returns the pre-order exit time of the node.
     *
     * @return integer the right value.
     * @done Domain method
     */
    public function getRightValue()
    {
        return $this->get_default_property(self::PROPERTY_RIGHT_VALUE);
    }

    /**
     * @return string[]
     */
    public function getSubTreePropertyNames()
    {
        return [];
    }

    /**
     *
     * @param boolean $include_self
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::findAncestors()
     */
    public function get_ancestors($include_self = true, $condition = null)
    {
        return DataManager::retrieves(
            get_class($this), new DataClassRetrievesParameters(
                $this->build_ancestry_condition($include_self, $condition), null, null,
                $this->build_post_order_ordering()
            )
        );
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Libraries\Storage\DataClass\DataClass>
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::findDescendants()
     */
    public function get_children($condition = null)
    {
        return DataManager::retrieves(
            get_class($this),
            new DataClassRetrievesParameters($this->build_offspring_condition(false, false, $condition))
        );
    }

    /**
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     * @done Domain method
     */
    public static function get_default_property_names($extendedPropertyNames = [])
    {
        $extendedPropertyNames[] = self::PROPERTY_PARENT_ID;
        $extendedPropertyNames[] = self::PROPERTY_LEFT_VALUE;
        $extendedPropertyNames[] = self::PROPERTY_RIGHT_VALUE;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Libraries\Storage\DataClass\DataClass>
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::findDescendants()
     */
    public function get_descendants($condition = null)
    {
        return DataManager::retrieves(
            get_class($this),
            new DataClassRetrievesParameters($this->build_offspring_condition(true, false, $condition))
        );
    }

    /**
     * Returns the pre-order entry time of the node.
     *
     * @return integer the left value.
     * @deprecated Use NestedSet::getLeftValue() now
     */
    public function get_left_value()
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
    public function get_nested_set_condition_array()
    {
        return [];
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataClass\NestedSet
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::getParent()
     */
    public function get_parent()
    {
        return $this->find_by_id($this->getParentId());
    }

    /**
     * Returns the id that identifies the node's parent in the tree.
     *
     * @return integer The parent's id.
     * @deprecated Use NestedSet::getParentId() now
     */
    public function get_parent_id()
    {
        return $this->getParentId();
    }

    /**
     * Returns the pre-order exit time of the node.
     *
     * @return integer the right value.
     * @deprecated Use NestedSet::getRightValue() now
     */
    public function get_right_value()
    {
        return $this->getRightValue();
    }

    /**
     *
     * @param boolean $include_self
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::findSiblings()
     */
    public function get_siblings($include_self = true, $condition = null)
    {
        return DataManager::retrieves(
            get_class($this), new DataClassRetrievesParameters(
                $this->build_sibling_condition($include_self, $condition), null, null, $this->build_pre_order_ordering()
            )
        );
    }

    /**
     *
     * @return boolean
     * @done Domain method
     */
    public function hasChildren()
    {
        return !($this->getLeftValue() == ($this->getRightValue() - 1));
    }

    /**
     *
     * @return boolean
     * @deprecated Use NestedSet::hasChildren() now
     */
    public function has_children()
    {
        return $this->hasChildren();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::hasSiblings()
     */
    public function has_siblings($condition = null)
    {
        return ($this->count_siblings(false, $condition) > 0);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     *
     * @return boolean
     * @done Domain method
     */
    public function isAncestorOf(NestedSet $nestedSet)
    {
        if ($this->get_left_value() < $nestedSet->get_left_value() &&
            $nestedSet->get_right_value() < $this->get_right_value())
        {
            return true;
        }

        return false;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet $nestedSet
     *
     * @return boolean
     * @done Domain method
     */
    public function isDescendantOf(NestedSet $nestedSet)
    {
        if ($this->get_left_value() > $nestedSet->get_left_value() &&
            $nestedSet->get_right_value() > $this->get_right_value())
        {
            return true;
        }

        return false;
    }

    /**
     * @return boolean
     * @done Domain method
     */
    public function isRoot()
    {
        return ($this->getParentId() == 0);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet|integer $node_or_id
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @deprecated Use NestedSet::isAncestorOf() now
     */
    public function is_ancestor_of($node_or_id)
    {
        return $this->isAncestorOf($this->find_by_id($node_or_id));
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet|integer $node_or_id
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @deprecated Use NestedSet::isDescendantOf() now
     */
    public function is_descendant_of($node_or_id)
    {
        return $this->isDescendantOf($this->find_by_id($node_or_id));
    }

    /**
     *
     * @return boolean
     * @deprecated Use NestedSet::isRoot() now
     */
    public function is_root()
    {
        return $this->isRoot();
    }

    /**
     * @param integer $position
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet|integer $reference_node
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     * @deprecated Migrated to NestedSetDataClassRepository::move()
     */
    public function move($position = self::AS_LAST_CHILD_OF, $reference_node = null, $condition = null)
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
                $insert_after = $reference_node->get_left_value();
                break;

            case self::AS_LAST_CHILD_OF :
                $insert_after = $reference_node->get_right_value() - 1;
                break;

            case self::AS_PREVIOUS_SIBLING_OF :
                $insert_after = $reference_node->get_left_value() - 1;
                $this->set_parent_id($reference_node);
                break;

            case self::AS_NEXT_SIBLING_OF :
                $insert_after = $reference_node->get_right_value();
                $this->set_parent_id($reference_node);
                break;
        }

        // Moving a node in a nested set requires multiple updates
        // which have to be performed atomically and consistently.
        //
        // Use a transaction to guarantee this.

        $nested_set = $this;

        return DataManager::transactional(
            function ($c) use ($nested_set, $insert_after, $condition
            ) { // Step 0: Compute the auxiliary values used by this
                // algorithm
                // This is the initial position of the node to be moved
                $initial_left = $nested_set->get_left_value();
                $initial_right = $nested_set->get_right_value();

                // This is the size of the subtree to be moved (i.e. the size of the gap to be created so that it can be
                // moved in)
                $delta = $nested_set->get_right_value() - $nested_set->get_left_value() + 1;

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
                    new PropertyConditionVariable($nested_set::class_name(), NestedSet::PROPERTY_LEFT_VALUE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($after_pre_insert_left)
                );
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable($nested_set::class_name(), NestedSet::PROPERTY_RIGHT_VALUE),
                    ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable($after_pre_insert_right)
                );

                if ($condition)
                {
                    $conditions[] = $condition;
                }

                $update_condition = new AndCondition($conditions);

                $left_value_variable = new PropertyConditionVariable(
                    $nested_set->class_name(), NestedSet::PROPERTY_LEFT_VALUE
                );
                $right_value_variable = new PropertyConditionVariable(
                    $nested_set->class_name(), NestedSet::PROPERTY_RIGHT_VALUE
                );

                $properties = [];

                $properties[] = new DataClassProperty(
                    $left_value_variable, new OperationConditionVariable(
                        $left_value_variable, OperationConditionVariable::ADDITION, new StaticConditionVariable($shift)
                    )
                );

                $properties[] = new DataClassProperty(
                    $right_value_variable, new OperationConditionVariable(
                        $right_value_variable, OperationConditionVariable::ADDITION, new StaticConditionVariable($shift)
                    )
                );

                $res = DataManager::updates(
                    get_class($nested_set), $properties, $update_condition
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
                $nested_set->set_left_value($after_pre_insert_left);
                $nested_set->set_right_value($after_pre_insert_right);

                $res = $nested_set->post_delete($condition);

                if (!$res)
                {
                    return false;
                }

                // Step 4: Update the parent id of the moved node.
                // This has already been performed in memory, but needs to be written to the database.

                // Set the left and right values to their final position, so the update does not alter them.
                $nested_set->set_left_value($final_left);
                $nested_set->set_right_value($final_right);

                if (!$nested_set->update())
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
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition additional condition
     *
     * @return boolean
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::postDelete()
     */
    public function post_delete($condition = null)
    {
        // This private function is only ever called from within a transaction.
        //
        // This needs to be a transaction: both updates should either commit or abort.
        // Now, it is possible that the first update succeeds, but the latter doesn't.
        // This implies that we may end up with an inconsistent nested set.
        $delta = $this->get_right_value() - $this->get_left_value() + 1;

        // 1. Update the left and right values of all successors of the deleted node.
        // A successor has a left-value which is higher than the left-value of the deleted node.

        $conditions = $this->get_nested_set_condition_array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_LEFT_VALUE),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($this->get_left_value())
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $left_value_variable = new PropertyConditionVariable(self::class_name(), self::PROPERTY_LEFT_VALUE);
        $right_value_variable = new PropertyConditionVariable(self::class_name(), self::PROPERTY_RIGHT_VALUE);

        $right_value_data_class_property = new DataClassProperty(
            $right_value_variable, new OperationConditionVariable(
                $right_value_variable, OperationConditionVariable::MINUS, new StaticConditionVariable($delta)
            )
        );

        $properties = [];
        $properties[] = $right_value_data_class_property;
        $properties[] = new DataClassProperty(
            $left_value_variable, new OperationConditionVariable(
                $left_value_variable, OperationConditionVariable::MINUS, new StaticConditionVariable($delta)
            )
        );

        $res = DataManager::updates(
            get_class($this), $properties, $update_condition
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
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_LEFT_VALUE),
            ComparisonCondition::LESS_THAN, new StaticConditionVariable($this->get_left_value())
        );

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_RIGHT_VALUE),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($this->get_right_value())
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $properties = [];
        $properties[] = $right_value_data_class_property;

        $res = DataManager::updates(
            get_class($this), $properties, $update_condition
        );

        if (!$res)
        {
            return false;
        }

        return true;
    }

    /**
     * Creates the necessary room to insert a number of values (1 by default) into the nested set: it shifts the
     * left/right values of all nodes that are traversed after the insertion point to the right.
     *
     * @param integer $insert_after the right value of the node who will act as the left sibling of the left-most
     *        inserted
     *        node. In absence of such a left sibling, the left value of the parent of the left-most inserted node.
     * @param integer $number_of_elements the number of elements that have to be inserted
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition additional condition (which filters out
     *        some nodes that will not be updated: should
     *        only be used when supporting multiple roots in a single database table)
     *
     * @return boolean
     * @throws \ReflectionException
     * @deprecated Migrated to NestedSetDataClassRepository::preInsert()
     */
    public function pre_insert($insert_after, $number_of_elements = 1, $condition = null)
    {
        // This private function is only ever called from within a transaction.
        //
        // This needs to be a transaction: both updates should either commit or abort.
        // Now, it is possible that the first update succeeds, but the latter doesn't.
        // This implies that we may end up with an inconsistent nested set.

        // Update all necessary left-values
        $conditions = $this->get_nested_set_condition_array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_LEFT_VALUE),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($insert_after)
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $left_value_variable = new PropertyConditionVariable(self::class_name(), self::PROPERTY_LEFT_VALUE);

        $properties = [];
        $properties[] = new DataClassProperty(
            $left_value_variable, new OperationConditionVariable(
                $left_value_variable, OperationConditionVariable::ADDITION,
                new StaticConditionVariable($number_of_elements * 2)
            )
        );

        $res = DataManager::updates(
            get_class($this), $properties, $update_condition
        );

        if (!$res)
        {
            return false;
        }

        // Update all necessary right-values
        $conditions = $this->get_nested_set_condition_array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_RIGHT_VALUE),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($insert_after)
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $right_value_variable = new PropertyConditionVariable(self::class_name(), self::PROPERTY_RIGHT_VALUE);

        $properties = [];

        $properties[] = new DataClassProperty(
            $right_value_variable, new OperationConditionVariable(
                $right_value_variable, OperationConditionVariable::ADDITION,
                new StaticConditionVariable($number_of_elements * 2)
            )
        );

        $res = DataManager::updates(
            get_class($this), $properties, $update_condition
        );

        if (!$res)
        {
            return false;
        }

        return true;
    }

    /**
     * Updates the pre-order entry time of the node.
     * Marked private as users are expected to use the move method to
     * change the location of a node into the tree.
     *
     * @param integer $leftValue
     *
     * @done Domain method
     *
     * @throws \Exception
     */
    public function setLeftValue($leftValue)
    {
        $this->setDefaultProperty(self::PROPERTY_LEFT_VALUE, $leftValue);
    }

    /**
     * Updates the parent of the node.
     * Marked private as users are expected to use the move method to change the
     * location of a node into the tree.
     *
     * @param integer $parentId
     *
     * @done Domain method
     * @throws \Exception
     */
    public function setParentId($parentId)
    {
        $this->setDefaultProperty(self::PROPERTY_PARENT_ID, $parentId);
    }

    /**
     * Updates the pre-order exit time of the node.
     * Marked private as users are expected to use the move method to
     * change the location of a node into the tree.
     *
     * @param integer $rightValue
     *
     * @done Domain method
     *
     * @throws \Exception
     */
    public function setRightValue($rightValue)
    {
        $this->setDefaultProperty(self::PROPERTY_RIGHT_VALUE, $rightValue);
    }

    /**
     * Updates the pre-order entry time of the node.
     * Marked private as users are expected to use the move method to
     * change the location of a node into the tree.
     *
     * @param integer $leftValue
     *
     * @throws \Exception
     * @deprecated Use NestedSet::setLeftValue() now
     */
    public function set_left_value($leftValue)
    {
        $this->setLeftValue($leftValue);
    }

    /**
     * Updates the parent of the node.
     * Marked private as users are expected to use the move method to change the
     * location of a node into the tree.
     *
     * @param integer $parentId
     *
     * @throws \Exception
     * @deprecated Use NestedSet::setParentId() now
     *
     */
    public function set_parent_id($parentId)
    {
        $this->setParentId($parentId);
    }

    /**
     * Updates the pre-order exit time of the node.
     * Marked private as users are expected to use the move method to
     * change the location of a node into the tree.
     *
     * @param integer $rightValue
     *
     * @throws \Exception
     * @deprecated Use NestedSet::setRightValue() now
     */
    public function set_right_value($rightValue)
    {
        $this->setRightValue($rightValue);
    }

    /**
     * Validates a relative position of a node, which is used when creating or moving a node.
     *
     * @param integer $position where to insert/move w.r.t. the reference node
     * @param \Chamilo\Libraries\Storage\DataClass\NestedSet|integer $reference_node null, an id or a node object that
     *        identifies the postion in the nested set.
     *
     * @return \Chamilo\Libraries\Storage\DataClass\NestedSet the node that identifies the position where to insert a
     *         new/moved node
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \Exception
     * @deprecated Migrated to NestedSetDataClassRepository::validatePosition()
     */
    public function validate_position($position = self::AS_LAST_CHILD_OF, &$reference_node = null)
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
