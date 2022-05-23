<?php

namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\GroupBy;
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
class DataClassRetrievesParameters extends DataClassParameters
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $count
     * @param int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderProperty[] $orderBy
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     * @param boolean $distinct
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $havingCondition
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $groupBy
     */
    public function __construct(
        Condition $condition = null, $count = null, $offset = null, $orderBy = [], Joins $joins = null,
        $distinct = false, GroupBy $groupBy = null, Condition $havingCondition = null
    )
    {
        parent::__construct(
            $condition, $joins, null, $orderBy, $groupBy, $havingCondition, $count, $offset, $distinct
        );
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
        elseif (is_object($parameter) && $parameter instanceof Condition)
        {
            return new self($parameter);
        }
        elseif (is_integer($parameter))
        {
            return new self(null, $parameter);
        }
        elseif (is_object($parameter) && $parameter instanceof OrderBy)
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
            throw new Exception('Illegal parameter passed to the DataManager::retrieves() method.');
        }
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
     * @return boolean
     * @deprecated Use getDistinct() now
     */
    public function get_distinct()
    {
        return $this->getDistinct();
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
     * @param boolean $distinct
     *
     * @deprecated Use setDistinct() now
     */
    public function set_distinct($distinct)
    {
        $this->setDistinct($distinct);
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
}
