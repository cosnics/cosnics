<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;

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
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[] $dataClassProperties
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     */
    public function __construct(
        Condition $condition = null, DataClassProperties $dataClassProperties = null, Joins $joins = null,
        $orderBy = array()
    )
    {
        DataClassParameters::__construct($condition, $joins, $dataClassProperties, $orderBy);
    }

    /**
     * Get the property of the DataClass object to be used as a parameter
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     * @deprecated Use DataClassProperties and getDataClassProperties() now
     */
    public function get_property()
    {
        return $this->getDataClassProperties();
    }

    /**
     * Set the property of the DataClass object to be used as a parameter
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[] $properties
     *
     * @deprecated Use DataClassProperties and setDataClassProperties() now
     */
    public function set_property($properties)
    {
        $this->setDataClassProperties($properties);
    }
}
