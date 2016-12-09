<?php
namespace Chamilo\Libraries\Storage\DataClass;

/**
 *
 * @deprecated This class currently only serves as an interface adapter between its existing subclasses and the new
 *             NestedSet class. All its functionality has been subsumed by the latter, which also provides transactional
 *             security.
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
abstract class NestedTreeNode extends NestedSet
{
    // Nested trees functionality
    
    /**
     * Count the number of children of the object
     * 
     * @param boolean $recursive - if put on true, every child will be counted, even those who are not directly
     *        connected with parent_id
     */
    public function count_children($recursive = true, $condition = null)
    {
        if (! $recursive)
        {
            return parent :: count_children($condition);
        }
        else
        {
            return parent :: count_descendants($condition);
        }
    }

    /**
     * Retrieve the children of the object
     * 
     * @param boolean $recursive - if put on true, every child will be retrieved, even those who are not directly
     *        connected with parent_id
     */
    public function get_children($recursive = true, $condition = null)
    {
        if (! $recursive)
        {
            return parent :: get_children($condition);
        }
        else
        {
            return parent :: get_descendants($condition);
        }
    }

    /**
     * Count the parents of the object, recursivly, every parent will be counted, even those who are not directly
     * connected with parent_id
     * 
     * @param boolean $include_self - if put on true, the current object will be included in the count
     */
    public function count_parents($include_self = true, $condition = null)
    {
        return parent :: count_ancestors($include_self, $condition);
    }

    /**
     * Retrieve the parents of the object, recursivly, every parent will be counted, even those who are not directly
     * connected with parent_id
     * 
     * @param boolean $include_self - if put on true, the current object will be included in the parents list
     */
    public function get_parents($include_self = true, $condition = null)
    {
        return parent :: get_ancestors($include_self, $condition);
    }

    /**
     * Check if the current object is a child of the given object
     * 
     * @param NestedTreeNode $node - the possible parent node
     */
    public function is_child_of($node)
    {
        return parent :: is_descendant_of($node);
    }

    /**
     * Check if the object is the parent of the given object
     * 
     * @param NestedTreeNode $node - the possible child node
     */
    public function is_parent_of($node)
    {
        return parent :: is_ancestor_of($node);
    }

    /**
     * Move the object to another place in the tree (either with parent id or previous node id)
     * 
     * @param int $new_parent_id - the new parent_id
     * @param int $new_previous_id - the previous node id where you want to add the object
     */
    public function move($new_parent_id = 0, $new_previous_id = 0)
    {
        if ($new_previous_id != 0)
        {
            return parent :: move(self :: AS_NEXT_SIBLING_OF, $new_previous_id);
        }
        else
        {
            if ($new_parent_id == 0)
                $new_parent_id = $this->get_parent_id();
            
            return parent :: move(self :: AS_LAST_CHILD_OF, $new_parent_id);
        }
    }

    /**
     * Create the object in the database (either with parent id or previous node id)
     * 
     * @param int $previous_id - the previous node id where you want to add the object
     */
    public function create($previous_id = 0, $create_in_batch = false)
    {
        $parent_id = $this->get_parent_id();
        
        if ($previous_id)
        {
            return parent :: create(parent :: AS_NEXT_SIBLING_OF, $previous_id);
        }
        else
        {
            return parent :: create(parent :: AS_LAST_CHILD_OF, $parent_id);
        }
    }
}
