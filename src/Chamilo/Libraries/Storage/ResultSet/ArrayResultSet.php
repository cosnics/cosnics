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
 * @deprecated Use DataClassIterator, RecordIterator or ArrayIterator now
 */
class ArrayResultSet extends ResultSet
{

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
     * Retrieves next item from this result set
     *
     * @param boolean $mapToObject
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
     * Returns an array representation of this result set.
     *
     * @return mixed[]
     */
    public function as_array()
    {
        return $this->data;
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
     * @return integer
     */
    public function current()
    {
        return $this->pointer;
    }

    /**
     *
     * @param integer $count
     */
    public function skip($count)
    {
        $this->pointer += $count;
    }

    public function reset()
    {
        $this->pointer = 0;
    }
}
