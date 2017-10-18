<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

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
     */
    public function __construct(Condition $condition = null, $count = null, $offset = null, $orderBy = array(), Joins $joins = null,
        $distinct = false)
    {
        DataClassParameters::__construct($condition, $joins, null, $orderBy, null, null, $count, $offset, $distinct);
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
     * @deprecated Use setDistinct() now
     */
    public function set_distinct($distinct)
    {
        $this->setDistinct($distinct);
    }
}
