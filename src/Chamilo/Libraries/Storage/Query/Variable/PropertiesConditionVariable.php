<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A ConditionVariable that describes all the properties of a DataClass
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertiesConditionVariable extends ConditionVariable
{

    /**
     * The fully qualified class name of the DataClass object the property belongs to
     *
     * @var string
     */
    private $class;

    /**
     * Constructor
     *
     * @param string $class
     */
    public function __construct($class)
    {
        if (! class_exists($class))
        {
            throw new \Exception($class . ' does not exist');
        }
        $this->class = $class;
    }

    /**
     * Get the fully qualified class name of the DataClass object the property belongs to
     *
     * @return string
     */
    public function get_class()
    {
        return $this->class;
    }

    /**
     * Set the fully qualified class name of the DataClass object the property belongs to
     *
     * @param string $class
     */
    public function set_class($class)
    {
        $this->class = $class;
    }

    /**
     * Get an md5 representation of this object for identification purposes
     *
     * @param string[] $hash_parts
     *
     * @return string
     */
    public function hash($hash_parts = array())
    {
        if (! $this->get_hash())
        {
            $hash_parts[] = $this->class;

            $this->set_hash(parent :: hash($hash_parts));
        }

        return $this->get_hash();
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Determines the alias of the dataclass
     *
     * @return string
     */
    public function get_alias()
    {
        $class_name = $this->get_class();
        return \Chamilo\Libraries\Storage\DataManager\DataManager :: get_alias($class_name :: get_table_name());
    }
}
