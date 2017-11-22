<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A ConditionVariable that describes an operation on two other ConditionVariables
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class OperationConditionVariable extends ConditionVariable
{
    /**
     * A constant defining an addition
     *
     * @var int
     */
    const ADDITION = 1;

    /**
     * A constant defining a subtraction
     *
     * @var int
     */
    const MINUS = 2;

    /**
     * A constant defining a multiplication
     *
     * @var int
     */
    const MULTIPLICATION = 3;

    /**
     * A constant defining a division
     *
     * @var int
     */
    const DIVISION = 4;

    /**
     * Bits that are set in both $a and $b are set
     *
     * @var int
     */
    const BITWISE_AND = 5;

    /**
     * Bits that are set in either $a or $b are set
     *
     * @var int
     */
    const BITWISE_OR = 6;

    /**
     * The ConditionVariable on the left side of the operation
     *
     * @var \libraries\storage\ConditionVariable
     */
    private $left;

    /**
     * The operator that connects both ConditionVariables
     *
     * @var int
     */
    private $operator;

    /**
     * The ConditionVariable on the right side of the operation
     *
     * @var \libraries\storage\ConditionVariable
     */
    private $right;

    /**
     * Constructor
     *
     * @param string $context
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $left
     * @param integer $operator
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $right
     */
    public function __construct($left, $operator, $right)
    {
        $this->left = $left;
        $this->operator = $operator;
        $this->right = $right;
    }

    /**
     * Get the ConditionVariable on the left side of the operation
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function get_left()
    {
        return $this->left;
    }

    /**
     * Set the ConditionVariable on the left side of the operation
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $left
     */
    public function set_left($left)
    {
        $this->left = $left;
    }

    /**
     * Get the operator that connects both ConditionVariables
     *
     * @return int
     */
    public function get_operator()
    {
        return $this->operator;
    }

    /**
     * Set the operator that connects both ConditionVariables
     *
     * @param $operator int
     */
    public function set_operator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * Get the ConditionVariable on the right side of the operation
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function get_right()
    {
        return $this->right;
    }

    /**
     * Set the ConditionVariable on the right side of the operation
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $right
     */
    public function set_right($right)
    {
        $this->right = $right;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = ConditionVariable::getHashParts();

        $parts = array();
        $parts[] = $this->get_left()->getHashParts();
        $parts[] = $this->get_right()->getHashParts();

        if ($this->get_operator() != self::DIVISION)
        {
            sort($parts);
        }

        foreach ($parts as $part)
        {
            $hashParts[] = $part;
        }

        $hashParts[] = $this->get_operator();

        return $hashParts;
    }
}
