<?php
namespace Chamilo\Libraries\Storage\Parameters;

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
     * The ordering of the DataClass objects to be applied to the result set
     *
     * @var \Chamilo\Libraries\Storage\Query\OrderBy[]
     */
    private $order_by;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_by
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     */
    public function __construct($condition = null, $order_by = array(), Joins $joins = null)
    {
        parent :: __construct($condition, $joins);
        $this->order_by = $order_by;
    }

    /**
     * Get the ordering of the DataClass objects to be applied to the result set
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy[]
     */
    public function get_order_by()
    {
        return $this->order_by;
    }

    /**
     * Set the ordering of the DataClass objects to be applied to the result set
     *
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_by
     */
    public function set_order_by($order_by)
    {
        $this->order_by = $order_by;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Parameters\DataClassParameters::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent :: getHashParts();

        $hashParts[] = $this->get_order_by();

        return $hashParts;
    }
}
