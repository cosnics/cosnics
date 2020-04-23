<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * This class represents a condition that requires an inequality.
 * An example would be requiring that a number be greater
 * than 4.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Query\Condition
 */
class ComparisonCondition extends Condition
{
    /**
     * Constant defining "="
     *
     * @var int
     */
    const EQUAL = 5;

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
     * Gets the DataClass property
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
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
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
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
     * @param $storageUnit string
     * @param $isAlias boolean
     */
    public function __construct($name, $operator, $value, $storageUnit = null, $isAlias = false)
    {
        $this->name = $name;
        $this->operator = $operator;
        $this->value = $value;
        $this->storage_unit = $storageUnit;
        $this->is_alias = $isAlias;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->get_operator();

        switch ($this->get_operator())
        {
            case self::LESS_THAN :
            case self::LESS_THAN_OR_EQUAL :
                $hashParts[] = $this->get_value() instanceof ConditionVariable ? $this->get_value()->getHashParts() :
                    $this->get_value();
                $hashParts[] = $this->get_name() instanceof ConditionVariable ? $this->get_name()->getHashParts() :
                    $this->get_name();
                break;
            case self::EQUAL :
                $parts = array();
                $parts[] = $this->get_name() instanceof ConditionVariable ? $this->get_name()->getHashParts() :
                    $this->get_name();
                $parts[] = $this->get_value() instanceof ConditionVariable ? $this->get_value()->getHashParts() :
                    $this->get_value();

                sort($parts);

                foreach ($parts as $part)
                {
                    $hashParts[] = $part;
                }

                break;
            default :
                $hashParts[] = $this->get_name() instanceof ConditionVariable ? $this->get_name()->getHashParts() :
                    $this->get_name();
                $hashParts[] = $this->get_value() instanceof ConditionVariable ? $this->get_value()->getHashParts() :
                    $this->get_value();
                break;
        }

        $hashParts[] = $this->get_storage_unit();
        $hashParts[] = $this->is_alias();

        return $hashParts;
    }

    /**
     * Gets the DataClass property
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
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
     * Gets the storage unit of the DataClass
     *
     * @return string
     */
    public function get_storage_unit()
    {
        return $this->storage_unit;
    }

    /**
     * Gets the value against which we're comparing
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function get_value()
    {
        return $this->value;
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
}
