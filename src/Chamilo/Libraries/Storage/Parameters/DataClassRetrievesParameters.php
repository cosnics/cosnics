<?php

namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
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
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $count
     * @param int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     * @param boolean $distinct
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $havingCondition
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $groupBy
     */
    public function __construct(
        Condition $condition = null, $count = null, $offset = null, $orderBy = array(), Joins $joins = null,
        $distinct = false, GroupBy $groupBy = null, Condition $havingCondition = null
    )
    {
        DataClassParameters::__construct(
            $condition, $joins, null, $orderBy, $groupBy, $havingCondition, $count, $offset, $distinct
        );
    }

    /**
     *
     * @return integer
     * @deprecated Use getCount() now
     */
    public function get_count()
    {
        return $this->getCount();
    }

    /**
     *
     * @param integer $count
     *
     * @deprecated Use getCount() now
     */
    public function set_count($count)
    {
        $this->setCount($count);
    }

    /**
     *
     * @return integer
     * @deprecated Use getOffset() now
     */
    public function get_offset()
    {
        return $this->getOffset();
    }

    /**
     *
     * @param integer $offset
     *
     * @deprecated Use setOffset() now
     */
    public function set_offset($offset)
    {
        $this->setOffset($offset);
    }

    /**
     *
     * @return boolean
     * @deprecated Use getDistinct() now
     */
    public function get_distinct()
    {
        return $this->getDistinct();
    }

    /**
     *
     * @param boolean $distinct
     *
     * @deprecated Use setDistinct() now
     */
    public function set_distinct($distinct)
    {
        $this->setDistinct($distinct);
    }

    /**
     * Generate an instance based on the input or throw an exception if no compatible input was found
     *
     * @param mixed $parameter
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters
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
