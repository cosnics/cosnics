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
 * @deprecated Use DataClassIterator, RecordIterator or ArrayIterator now
 */
abstract class ResultSet
{
    const POSITION_FIRST = 1;
    const POSITION_LAST = 2;
    const POSITION_SINGLE = 3;
    const POSITION_MIDDLE = 4;
    const POSITION_INVALID = 5;

    /**
     * Retrieves next item from this result set
     *
     * @param boolean $mapToObject
     * @return mixed
     */
    abstract public function next_result($mapToObject = true);

    /**
     * Retrieves the number of items in this result set.
     *
     * @return integer
     */
    abstract public function size();

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

    /**
     * Returns an array representation of this result set.
     *
     * @return mixed[]
     */
    public function as_array()
    {
        $array = array();

        while ($result = $this->next_result())
        {
            $array[] = $result;
        }

        return $array;
    }

    /**
     * Get the current position in the ResultSet
     *
     * @return integer
     */
    public function position()
    {
        if ($this->size() == 1)
        {
            return self::POSITION_SINGLE;
        }
        elseif ($this->size() > 1 && $this->current() == $this->size())
        {
            return self::POSITION_LAST;
        }
        elseif ($this->size() > 1 && $this->current() == 1)
        {
            return self::POSITION_FIRST;
        }
        elseif ($this->current() == 0 || $this->current() > $this->size())
        {
            return self::POSITION_INVALID;
        }
        else
        {
            return self::POSITION_MIDDLE;
        }
    }

    /**
     * Is the result on the given position in the ResultSet?
     *
     * @param integer $position
     * @return boolean
     */
    public function is_position($position)
    {
        return ($this->position() == $position || $this->is_single());
    }

    /**
     * Is the result the first one in the ResultSet?
     *
     * @return boolean
     */
    public function is_first()
    {
        return $this->is_position(self::POSITION_FIRST);
    }

    /**
     * Is the result the last one in the ResultSet?
     *
     * @return boolean
     */
    public function is_last()
    {
        return $this->is_position(self::POSITION_LAST);
    }

    /**
     * Is the result in the middle of the ResultSet.
     * That is not the first, not the last and not the only result?
     *
     * @return boolean
     */
    public function is_middle()
    {
        return $this->is_position(self::POSITION_MIDDLE);
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
     * Process the record returned by the storage layer
     *
     * @param string[] $record
     * @return string[]
     */
    public function process_record($record)
    {
        return DataManager::process_record($record);
    }
}
