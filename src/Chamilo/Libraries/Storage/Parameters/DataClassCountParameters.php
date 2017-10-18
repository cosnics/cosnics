<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassCountParameters extends DataClassParameters
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $property
     */
    public function __construct($condition = null, Joins $joins = null, $property)
    {
        DataClassParameters::__construct($condition, $joins, new DataClassProperties(array($property)));
    }

    /**
     * Get the property of the DataClass object to be used as a parameter
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     * @deprecated Use DataClassProperties and getDataClassProperties() now
     */
    public function get_property()
    {
        $dataClassProperties = $this->getDataClassProperties()->get();
        return array_shift($dataClassProperties);
    }

    /**
     * Set the property of the DataClass object to be used as a parameter
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $property
     * @deprecated Use DataClassProperties and setDataClassProperties() now
     */
    public function set_property($property)
    {
        $this->getDataClassProperties()->add($property);
    }
}
