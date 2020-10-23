<?php
namespace Chamilo\Libraries\Storage\ResultSet;

use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 * This class represents a result set and it allows you to create an abstract representation of a remote set of data
 *
 * @package Chamilo\Libraries\Storage\ResultSet
 * @author Tim De Pauw
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use DataClassIterator or ArrayIterator now
 */
abstract class ResultSet
{

    /**
     * Returns an array representation of this result set.
     *
     * @return array
     */
    public function as_array()
    {
        $array = array();

        foreach($this as $result)
        {
            $array[] = $result;
        }

        return $array;
    }

    /**
     * Checks whether this result set is empty.
     * The default implementation of this method checks whether the size()
     * function returns 0.
     *
     * @return boolean
     */
    public function is_empty()
    {
        return ($this->size() == 0);
    }

    /**
     * Is the result the only one?
     *
     * @return boolean
     */
    public function is_single()
    {
        return $this->size() == 1;
    }

    /**
     * Retrieves next item from this result set
     *
     * @param boolean $mapToObject
     *
     * @return mixed
     */
    abstract public function next_result($mapToObject = true);

    /**
     * Process the record returned by the storage layer
     *
     * @param string[] $record
     *
     * @return string[]
     */
    public function process_record($record)
    {
        return DataManager::process_record($record);
    }

    /**
     * Retrieves the number of items in this result set.
     *
     * @return integer
     */
    abstract public function size();

    /**
     * Skips a number of items.
     * The default implementation of this method merely discards the output of the
     * next_result() function $count times.
     *
     * @param integer $count
     */
    public function skip($count)
    {
        for ($i = 0; $i < $count; $i ++)
        {
            $this->next_result();
        }
    }
}
