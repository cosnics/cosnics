<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Joins;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassDistinctParameters extends DataClassPropertyParameters
{

    /**
     * The ordering of the DataClass objects to be applied to the result set
     *
     * @var \Chamilo\Libraries\Storage\Query\OrderBy[]
     */
    private $orderBy;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $property
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     */
    public function __construct($condition = null, $property = array(), Joins $joins = null, $orderBy = array())
    {
        parent::__construct($condition, $property, $joins);
        $this->orderBy = $orderBy;
    }

    /**
     * Get the ordering of the DataClass objects to be applied to the result set
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy[]
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Set the ordering of the DataClass objects to be applied to the result set
     *
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_by
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Parameters\DataClassParameters::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->getOrderBy();

        return $hashParts;
    }

    /**
     * Throw an exception if the DataClassPropertyParameters object is invalid
     *
     * @throws \Exception
     */
    public static function invalid()
    {
        throw new Exception('Illegal parameter(s) passed to the DataManager :: distinct() method.');
    }
}
