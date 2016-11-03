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
abstract class DataClassPropertyParameters extends DataClassParameters
{

    /**
     * The property of the DataClass object to be used as a parameter
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    private $property;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $property
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     */
    public function __construct($condition = null, $property = array(), Joins $joins = null)
    {
        parent :: __construct($condition, $joins);
        $this->property = $property;
    }

    /**
     * Get the property of the DataClass object to be used as a parameter
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function get_property()
    {
        return $this->property;
    }

    /**
     * Set the property of the DataClass object to be used as a parameter
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $property
     */
    public function set_property($property)
    {
        $this->property = $property;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Parameters\DataClassParameters::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent :: getHashParts();

        $hashParts[] = $this->get_property();

        return $hashParts;
    }

    /**
     * Generate an instance based on the input or throw an exception if no compatible input was found
     *
     * @param mixed $parameter
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassPropertyParameters
     *
     * @throws Exception
     */
    public static function generate($parameter)
    {
        // So you think you're being funny, eh? Right back at you ... you dog-blasted, ornery, no-account, long-eared
        // varmint!
        $class = self :: class_name();
        if (is_object($parameter) && $parameter instanceof $class)
        {
            return $parameter;
        }

        // If the parameter is a Condition, generate a new DataClassPropertyParameters instance using the Condition
        // provided by the context
        elseif (is_object($parameter) && $parameter instanceof Condition)
        {
            return new $class($parameter);
        }
        elseif (is_object($parameter) && $parameter instanceof Joins)
        {
            return new self(null, null, $parameter);
        }
        // If it's a string, generate a new DataClassPropertyParameters instance using the property
        // provided by the context
        elseif (is_string($parameter))
        {
            return new $class(null, $parameter);
        }
        else
        {
            static :: invalid();
        }
    }

    /**
     * Throw an exception if the DataClassPropertyParameters object is invalid
     *
     * @throws \Exception
     */
    public static function invalid()
    {
        throw new \Exception("invalid dataclass property parameters");
    }
}
