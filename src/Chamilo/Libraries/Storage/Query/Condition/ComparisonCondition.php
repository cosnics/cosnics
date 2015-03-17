<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * This class represents a condition that requires an inequality. An example would be requiring that a number be greater
 * than 4.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package common.libraries
 */
class ComparisonCondition extends Condition
{
    /**
     * Constant defining "<"
     *
     * @var int
     */
    const LESS_THAN = 1;

    /**
     * Constant defining "<="
     *
     * @var int
     */
    const LESS_THAN_OR_EQUAL = 2;

    /**
     * Constant defining ">"
     *
     * @var int
     */
    const GREATER_THAN = 3;

    /**
     * Constant defining ">="
     *
     * @var int
     */
    const GREATER_THAN_OR_EQUAL = 4;

    /**
     * Constant defining "="
     *
     * @var int
     */
    const EQUAL = 5;

    /**
     * Gets the DataClass property
     *
     * @var string
     */
    private $name;

    /**
     * The condition inequality operator
     *
     * @var int
     */
    private $operator;

    /**
     * The value against which we're comparing
     *
     * @var string
     */
    private $value;

    /**
     * Gets the storage unit of the DataClass
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
     * @param $operator int
     * @param $value string
     * @param $storage_unit string
     * @param $is_alias boolean
     */
    public function __construct($name, $operator, $value, $storage_unit = null, $is_alias = false)
    {
        $this->name = $name;
        $this->operator = $operator;
        $this->value = $value;
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
     * Gets the operator
     *
     * @return int
     */
    public function get_operator()
    {
        return $this->operator;
    }

    /**
     * Gets the value against which we're comparing
     *
     * @return string
     */
    public function get_value()
    {
        return $this->value;
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
     * Is the storage unit already an alias?
     *
     * @return boolean
     */
    public function is_alias()
    {
        return $this->is_alias;
    }

    public function hash()
    {
        if (! $this->get_hash())
        {
            $hashes = array();
            switch ($this->operator)
            {
                case self :: LESS_THAN :
                    $hashes[] = $this->value instanceof ConditionVariable ? $this->value->hash() : $this->value;
                    $hashes[] = $this->name instanceof ConditionVariable ? $this->name->hash() : $this->name;
                    $hashes[] = self :: GREATER_THAN;
                    break;
                case self :: LESS_THAN_OR_EQUAL :
                    $hashes[] = $this->value instanceof ConditionVariable ? $this->value->hash() : $this->value;
                    $hashes[] = $this->name instanceof ConditionVariable ? $this->name->hash() : $this->name;
                    $hashes[] = self :: GREATER_THAN_OR_EQUAL;
                    break;
                case self :: EQUAL :
                    $parts = array();
                    $parts[] = $this->name instanceof ConditionVariable ? $this->name->hash() : $this->name;
                    $parts[] = $this->value instanceof ConditionVariable ? $this->value->hash() : $this->value;
                    sort($parts);
                    foreach ($parts as $part)
                    {
                        $hashes[] = $part;
                    }
                    $hashes[] = $this->operator;
                    break;
                default :
                    $hashes[] = $this->name instanceof ConditionVariable ? $this->name->hash() : $this->name;
                    $hashes[] = $this->value instanceof ConditionVariable ? $this->value->hash() : $this->value;
                    $hashes[] = $this->operator;
                    break;
            }

            $hashes[] = $this->storage_unit;
            $hashes[] = $this->is_alias;

            $this->set_hash(parent :: hash($hashes));
        }

        return $this->get_hash();
    }
}
