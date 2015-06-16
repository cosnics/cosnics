<?php
namespace Chamilo\Libraries\Storage\ResultSet;

/**
 * Defines an empty resultset
 * Must be used when you know that the result from the query will be empty and thus the resultset will be empty.
 * Never use "return null or false".
 * 
 * @package Chamilo\Libraries\Storage\ResultSet
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EmptyResultSet extends ResultSet
{

    /**
     * Retrieves next item from this result set
     * 
     * @param boolean $map_to_object
     * @return mixed
     */
    public function next_result($map_to_object = true)
    {
        return null;
    }

    /**
     * Retrieves the number of items in this result set.
     * 
     * @return integer
     */
    public function size()
    {
        return 0;
    }
}