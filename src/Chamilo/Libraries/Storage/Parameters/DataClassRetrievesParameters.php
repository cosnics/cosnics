<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassRetrievesParameters extends DataClassRetrieveParameters
{

    /**
     * The number of results to return
     *
     * @var integer
     */
    private $count;

    /**
     * The offset of the result set relative to the first ordered result
     *
     * @var integer
     */
    private $offset;

    /**
     *
     * @var boolean
     */
    private $distinct;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $count
     * @param int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_by
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     */
    public function __construct($condition = null, $count = null, $offset = null, $order_by = array(), Joins $joins = null, $distinct = false)
    {
        parent :: __construct($condition, $order_by, $joins);

        $this->count = $count;
        $this->offset = $offset;
        $this->distinct = $distinct;
    }

    /**
     * Get the number of results to return
     *
     * @return integer
     */
    public function get_count()
    {
        return $this->count;
    }

    /**
     * Set the number of results to return
     *
     * @param integer $count
     */
    public function set_count($count)
    {
        $this->count = $count;
    }

    /**
     * Get the offset of the result set relative to the first ordered result
     *
     * @return integer
     */
    public function get_offset()
    {
        return $this->offset;
    }

    /**
     * Set the offset of the result set relative to the first ordered result
     *
     * @param integer $offset
     */
    public function set_offset($offset)
    {
        $this->offset = $offset;
    }

    /**
     *
     * @return boolean
     */
    public function get_distinct()
    {
        return $this->distinct;
    }

    /**
     *
     * @param boolean $distinct
     */
    public function set_distinct($distinct)
    {
        $this->distinct = $distinct;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Parameters\DataClassParameters::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent :: getHashParts();

        $hashParts[] = $this->get_count();
        $hashParts[] = $this->get_offset();
        $hashParts[] = $this->get_distinct();

        return $hashParts;
    }

    /**
     * Generate an instance based on the input or throw an exception if no compatible input was found
     *
     * @param mixed $parameter
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters
     *
     * @throws Exception
     */
    public static function generate($parameter = null)
    {
        if (is_object($parameter) && $parameter instanceof DataClassRetrievesParameters)
        {
            return $parameter;
        }

        // If the parameter is a Condition, generate a new DataClassRetrievesParameters instance using the Condition
        // provided by the context
        elseif (is_object($parameter) && $parameter instanceof Condition)
        {
            return new self($parameter);
        }

        // If it's an integer, assume it will be the count and generate a new DataClassRetrievesParameters
        elseif (is_integer($parameter))
        {
            return new self(null, $parameter);
        }

        // If the parameter is an array, determine whether it's an array of ObjectTableOrder objects and if so generate
        // a DataClassResultParameters
        elseif (is_array($parameter) && count($parameter) > 0 && $parameter[0] instanceof OrderBy)
        {
            return new self(null, null, null, $parameter);
        }
        elseif (is_object($parameter) && $parameter instanceof Joins)
        {
            return new self(null, null, null, null, $parameter);
        }
        elseif (is_null($parameter))
        {
            return new self();
        }
        else
        {
            throw new Exception('Illegal parameter passed to the DataManager :: retrieves() method.');
        }
    }
}
