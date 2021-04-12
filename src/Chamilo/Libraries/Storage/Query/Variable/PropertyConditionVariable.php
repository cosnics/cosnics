<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A ConditionVariable that describes a regular DataClass property
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be> - Refactoring to extension of PropertiesConditionVariable
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertyConditionVariable extends PropertiesConditionVariable
{

    /**
     * The property name of the DataClass object
     *
     * @var string
     */
    private $property;

    /**
     * Constructor
     *
     * @param string $class
     * @param string $property
     *
     * @throws \Exception
     */
    public function __construct($class, $property)
    {
        parent::__construct($class);

        $this->property = $property;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable::getHashParts()
     */
    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        $hashParts[] = $this->get_class();
        $hashParts[] = $this->get_property();

        return $hashParts;
    }

    /**
     * Get the property name of the DataClass object
     *
     * @return string
     */
    public function get_property()
    {
        return $this->property;
    }

    /**
     * Set the property name of the DataClass object
     *
     * @param string $property
     */
    public function set_property($property)
    {
        $this->property = $property;
    }
}
