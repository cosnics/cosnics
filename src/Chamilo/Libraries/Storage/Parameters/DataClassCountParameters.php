<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;
use Exception;

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
     * The property of the DataClass object to be used as a parameter
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    private $property;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $property
     */
    public function __construct($condition = null, Joins $joins = null, $property = array())
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

        $hashParts[] = ($this->get_condition() instanceof Condition ? $this->get_condition()->getHashParts() : null);
        $hashParts[] = ($this->get_property() instanceof ConditionVariable ? $this->get_property()->getHashParts() : null);

        return $hashParts;
    }

    /**
     * Generate an instance based on the input or throw an exception if no compatible input was found
     *
     * @param mixed $parameter
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters
     *
     * @throws Exception
     */
    public static function generate($parameter)
    {
        // So you think you're being funny, eh? Right back at you ... you dog-blasted, ornery, no-account, long-eared
        // varmint!
        if (is_object($parameter) && $parameter instanceof DataClassCountParameters)
        {
            return $parameter;
        }

        // If the parameter is a Condition, generate a new DataClassCountParameters instance using the Condition
        // provided by the context
        elseif (is_object($parameter) && $parameter instanceof Condition)
        {
            return new self($parameter);
        }
        elseif (is_object($parameter) && $parameter instanceof Joins)
        {
            return new self(null, $parameter);
        }
        elseif (is_null($parameter))
        {
            return new self();
        }
        else
        {
            throw new Exception('Illegal parameter passed to the DataManager :: count() method.');
        }
    }
}
