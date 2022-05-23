<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassDistinctParameters extends DataClassParameters
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $dataClassProperties
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     * @param \Chamilo\Libraries\Storage\Query\OrderProperty[] $orderBy
     */
    public function __construct(
        Condition $condition = null, DataClassProperties $dataClassProperties = null, Joins $joins = null, $orderBy = []
    )
    {
        parent::__construct($condition, $joins, $dataClassProperties, $orderBy);
    }

    /**
     * Get the property of the DataClass object to be used as a parameter
     *
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
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
