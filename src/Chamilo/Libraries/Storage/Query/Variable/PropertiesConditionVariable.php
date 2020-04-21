<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Exception;

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
            throw new Exception($class . ' does not exist');
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
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = ConditionVariable::getHashParts();

        $hashParts[] = $this->get_class();

        return $hashParts;
    }

    /**
     * Determines the alias of the dataclass
     *
     * @return string
     * @deprecated DO NOT use this anymore!
     */
    public function get_alias()
    {
        $class_name = $this->get_class();
        return DataManager::get_alias($class_name::get_table_name());
    }
}
