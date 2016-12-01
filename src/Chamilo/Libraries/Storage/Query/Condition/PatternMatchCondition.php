<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * This class represents a selection condition that uses a pattern for matching.
 * An example of an instance would be a
 * condition that requires that the title of an object contains the word "math". The pattern is case insensitive and
 * supports two types of wildcard characters: an asterisk (*) must match any sequence of characters, and a question mark
 * (?) must match a single character.
 * 
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package common.libraries
 */
class PatternMatchCondition extends Condition
{

    /**
     * The DataClass property
     * 
     * @var string
     */
    private $name;

    /**
     * The pattern to apply to the Dataclass property value
     * 
     * @var string
     */
    private $pattern;

    /**
     * The storage unit of the DataClass
     * 
     * @var string
     */
    private $storage_unit;

    /**
     * Is the storage unit name already an alias?
     * 
     * @var boolean
     */
    private $is_alias;

    /**
     * Constructor
     * 
     * @param $name string
     * @param $pattern string
     * @param $storage_unit string
     * @param $is_alias boolean
     */
    public function __construct($name, $pattern, $storage_unit = null, $is_alias = false)
    {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->storage_unit = $storage_unit;
        $this->is_alias = $is_alias;
    }

    /**
     * Gets the DataClass property
     * 
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Gets the storage unit of the DataClass
     * 
     * @return string
     */
    public function get_storage_unit()
    {
        return $this->storage_unit;
    }

    /**
     * Gets the pattern to apply to the Dataclass property value
     * 
     * @return string
     */
    public function get_pattern()
    {
        return $this->pattern;
    }

    /**
     * Is the storage unit already an alias?
     * 
     * @return boolean
     */
    public function is_alias()
    {
        return $this->is_alias;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Condition\Condition::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent::getHashParts();
        
        $hashParts[] = $this->get_name() instanceof ConditionVariable ? $this->get_name()->getHashParts() : $this->get_name();
        $hashParts[] = $this->get_pattern();
        $hashParts[] = $this->get_storage_unit();
        $hashParts[] = $this->is_alias();
        
        return $hashParts;
    }
}
