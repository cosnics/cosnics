<?php
namespace Chamilo\Libraries\Storage\DataClass;

/**
 *
 * @deprecated This class currently only serves as an interface adapter between its existing subclasses and the new
 *             NestedSet class. All its functionality has been subsumed by the latter, which also provides transactional
 *             security.
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\DataClass
 */
abstract class NestedTreeNode extends NestedSet
{

    /**
     * Count the number of children of the object
     *
     * @param boolean $recursive If put on true, every child will be counted, even those who are not directly connected
     *        with parent_id
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @done Migrated to NestedSetDataClassRepository::countDescendants()
     * @throws \ReflectionException
     */
    public function count_children($recursive = true, $condition = null)
    {
        if (!$recursive)
        {
            return parent::count_children($condition);
        }
        else
        {
            return parent::count_descendants($condition);
        }
    }

    /**
     * Count the parents of the object, recursivly, every parent will be counted, even those who are not directly
     * connected with parent_id
     *
     * @param boolean $includeSelf - if put on true, the current object will be included in the count
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @done Migrated to NestedSetDataClassRepository::countAncestors()
     * @throws \ReflectionException
     */
    public function count_parents($includeSelf = true, $condition = null)
    {
        return parent::count_ancestors($includeSelf, $condition);
    }

    /**
     * Create the object in the database (either with parent id or previous node id)
     *
     * @param int $previousId - the previous node id where you want to add the object
     * @param boolean $createInBatch
     *
     * @return boolean
     * @done Migrated to NestedSetDataClassRepository::create()
     */
    public function create($previousId = 0, $createInBatch = false)
    {
        $parent_id = $this->get_parent_id();

        if ($previousId)
        {
            return parent::create(parent::AS_NEXT_SIBLING_OF, $previousId);
        }
        else
        {
            return parent::create(parent::AS_LAST_CHILD_OF, $parent_id);
        }
    }

    /**
     * Retrieve the children of the object
     *
     * @param boolean $recursive - if put on true, every child will be retrieved, even those who are not directly
     *        connected with parent_id
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Libraries\Storage\DataClass\DataClass>
     * @done Migrated to NestedSetDataClassRepository::findDescendants()
     * @throws \ReflectionException
     */
    public function get_children($recursive = true, $condition = null)
    {
        if (!$recursive)
        {
            return parent::get_children($condition);
        }
        else
        {
            return parent::get_descendants($condition);
        }
    }

    /**
     * Retrieve the parents of the object, recursivly, every parent will be counted, even those who are not directly
     * connected with parent_id
     *
     * @param boolean $includeSelf - if put on true, the current object will be included in the parents list
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     * @done Migrated to NestedSetDataClassRepository::findAncestors()
     * @throws \ReflectionException
     */
    public function get_parents($includeSelf = true, $condition = null)
    {
        return parent::get_ancestors($includeSelf, $condition);
    }

    /**
     * Check if the current object is a child of the given object
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedTreeNode $node The possible parent node
     *
     * @return boolean
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @deprecated Use NestedSet::isDescendantOf() now
     */
    public function is_child_of($node)
    {
        return parent::is_descendant_of($node);
    }

    /**
     * Check if the object is the parent of the given object
     *
     * @param \Chamilo\Libraries\Storage\DataClass\NestedTreeNode $node The possible child node
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @deprecated Use NestedSet::isAncestorOf() now
     */
    public function is_parent_of($node)
    {
        return parent::is_ancestor_of($node);
    }

    /**
     * Move the object to another place in the tree (either with parent id or previous node id)
     *
     * @param integer $newParentId - the new parent_id
     * @param integer $newPreviousId - the previous node id where you want to add the object
     *
     * @return boolean
     * @done Migrated to NestedSetDataClassRepository::move()
     */
    public function move($newParentId = 0, $newPreviousId = 0)
    {
        if ($newPreviousId != 0)
        {
            return parent::move(self::AS_NEXT_SIBLING_OF, $newPreviousId);
        }
        else
        {
            if ($newParentId == 0)
            {
                $newParentId = $this->getParentId();
            }

            return parent::move(self::AS_LAST_CHILD_OF, $newParentId);
        }
    }
}
