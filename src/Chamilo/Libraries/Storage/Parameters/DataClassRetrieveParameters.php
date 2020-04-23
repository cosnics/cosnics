<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassRetrieveParameters extends DataClassParameters
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     */
    public function __construct(Condition $condition = null, $orderBy = array(), Joins $joins = null)
    {
        DataClassParameters::__construct($condition, $joins, null, $orderBy);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy[]
     * @deprecated Use getOrderBy() now
     */
    public function get_order_by()
    {
        return $this->getOrderBy();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     *
     * @deprecated Use setOrderBy() now
     */
    public function set_order_by($orderBy = array())
    {
        $this->setOrderBy($orderBy);
    }
}
