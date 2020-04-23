<?php
namespace Chamilo\Libraries\Storage\ResultSet;

/**
 * This class allows you to wrap an array in a result set.
 * It does not offer any performance increase, but in select
 * cases, you will need it.
 *
 * @package Chamilo\Libraries\Storage\ResultSet
 * @author Tim De Pauw
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use DataClassIterator or ArrayIterator now
 */
class ArrayResultSet extends ResultSet
{
    const POSITION_FIRST = 1;
    const POSITION_INVALID = 5;
    const POSITION_LAST = 2;
    const POSITION_MIDDLE = 4;
    const POSITION_SINGLE = 3;

    /**
     * The data in this set
     *
     * @var mixed[]
     */
    private $data;

    /**
     * A pointer to the current element in the set
     *
     * @var integer
     */
    private $pointer;

    /**
     * Constructor
     *
     * @param mixed[]
     */
    public function __construct($array)
    {
        $this->data = $array;
        $this->pointer = 0;
    }

    /**
     * Returns an array representation of this result set.
     *
     * @return mixed[]
     */
    public function as_array()
    {
        return $this->data;
    }

    /**
     *
     * @return integer
     */
    public function current()
    {
        return $this->pointer;
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
     * Is the result on the given position in the ResultSet?
     *
     * @param integer $position
     *
     * @return boolean
     */
    public function is_position($position)
    {
        return ($this->position() == $position || $this->is_single());
    }

    /**
     * Retrieves next item from this result set
     *
     * @param boolean $mapToObject
     *
     * @return mixed
     */
    public function next_result($mapToObject = false)
    {
        if ($this->pointer < count($this->data))
        {
            $this->pointer ++;

            return $this->data[$this->pointer - 1];
        }

        return null;
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

    public function reset()
    {
        $this->pointer = 0;
    }

    /**
     * Retrieves the number of items in this result set.
     *
     * @return integer
     */
    public function size()
    {
        return count($this->data);
    }

    /**
     *
     * @param integer $count
     */
    public function skip($count)
    {
        $this->pointer += $count;
    }
}
