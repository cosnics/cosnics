<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassRetrieveParameters extends DataClassParameters
{

    public function __construct(?Condition $condition = null, ?OrderBy $orderBy = null, ?Joins $joins = null)
    {
        parent::__construct($condition, $joins, null, $orderBy);
    }

    /**
     * @deprecated Use getOrderBy() now
     */
    public function get_order_by(): ?OrderBy
    {
        return $this->getOrderBy();
    }

    /**
     * @deprecated Use setOrderBy() now
     */
    public function set_order_by(?OrderBy $orderBy = null)
    {
        $this->setOrderBy($orderBy);
    }
}
