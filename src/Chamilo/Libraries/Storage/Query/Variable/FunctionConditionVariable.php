<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A ConditionVariable that describes a function on another ConditionVariable
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FunctionConditionVariable extends ConditionVariable
{
    const AVERAGE = 6;

    /**
     * A constant defining a count
     *
     * @var integer
     */
    const COUNT = 2;

    /**
     * A constant defining a distinct
     *
     * @var integer
     */
    const DISTINCT = 5;

    /**
     * A constant defining a maximum
     *
     * @var integer
     */
    const MAX = 4;

    /**
     * A constant defining a minimum
     *
     * @var integer
     */
    const MIN = 3;

    /**
     * A constant defining a sum
     *
     * @var integer
     */
    const SUM = 1;

    /**
     * The ConditionVariable
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    private $condition_variable;

    /**
     * The function to apply to the ConditionVariable
     *
     * @var integer
     */
    private $function;

    /**
     *
     * @var string
     */
    private $alias;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     * @param integer $function
     * @param string $alias
     */
    public function __construct($function, $conditionVariable, $alias = null)
    {
        $this->condition_variable = $conditionVariable;
        $this->function = $function;
        $this->alias = $alias;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = ConditionVariable::getHashParts();

        $hashParts[] = $this->get_condition_variable()->getHashParts();
        $hashParts[] = $this->get_function();
        $hashParts[] = $this->get_alias();

        return $hashParts;
    }

    /**
     *
     * @return string
     */
    public function get_alias()
    {
        return $this->alias;
    }

    /**
     *
     * @param string $alias
     */
    public function set_alias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Get the ConditionVariable on the condition_variable side of the operation
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function get_condition_variable()
    {
        return $this->condition_variable;
    }

    /**
     * Set the ConditionVariable on the condition_variable side of the operation
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     */
    public function set_condition_variable($conditionVariable)
    {
        $this->condition_variable = $conditionVariable;
    }

    /**
     * @return integer
     */
    public function get_function()
    {
        return $this->function;
    }

    /**
     *
     * @param integer $function
     */
    public function set_function($function)
    {
        $this->function = $function;
    }
}
