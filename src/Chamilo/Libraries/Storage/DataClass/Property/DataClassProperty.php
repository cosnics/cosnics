<?php
namespace Chamilo\Libraries\Storage\DataClass\Property;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * This class describes a single property for a data class with the name and the value This class can be used in query
 * structures
 * 
 * @package Chamilo\Libraries\Storage\DataClass\Property
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassProperty implements Hashable
{
    
    use \Chamilo\Libraries\Architecture\Traits\HashableTrait;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    private $property;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    private $value;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $property
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $value
     */
    public function __construct($property, $value)
    {
        $this->set_property($property);
        $this->set_value($value);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function get_property()
    {
        return $this->property;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $property
     */
    public function set_property(ConditionVariable $property)
    {
        $this->property = $property;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $value
     */
    public function set_value($value)
    {
        $this->value = $value;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Parameters\DataClassParameters::getHashParts()
     */
    public function getHashParts()
    {
        return array(__CLASS__, $this->get_property(), $this->get_value());
    }
}
