<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A ConditionVariable that describes a regular DataClass property with a fixed alias
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FixedPropertyConditionVariable extends PropertyConditionVariable
{
    /**
     * @var string
     */
    private $alias;

    /**
     * Constructor
     *
     * @param string $class
     * @param string $property
     * @param string $alias
     *
     * @throws \Exception
     */
    public function __construct($class, $property, $alias)
    {
        parent::__construct($class, $property);
        $this->alias = $alias;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->get_alias();

        return $hashParts;
    }

    /**
     * Get the alias of the DataClass object the property belongs to
     *
     * @return string
     */
    public function get_alias()
    {
        return $this->alias;
    }

    /**
     * Set the alias of the DataClass object the property belongs to
     *
     * @param string $alias
     */
    public function set_alias($alias)
    {
        $this->alias = $alias;
    }
}
