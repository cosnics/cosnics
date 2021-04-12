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
class DateFormatConditionVariable extends ConditionVariable
{

    /**
     * The ConditionVariable
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    private $conditionVariable;

    /**
     * The DateFormat string
     *
     * @var string
     */
    private $format;

    /**
     *
     * @var string
     */
    private $alias;

    /**
     *
     * @param string $format
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     * @param string $alias
     */
    public function __construct($format, $conditionVariable, $alias = null)
    {
        $this->conditionVariable = $conditionVariable;
        $this->format = $format;
        $this->alias = $alias;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        $hashParts[] = $this->get_condition_variable()->getHashParts();
        $hashParts[] = $this->get_format();
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
        return $this->conditionVariable;
    }

    /**
     * Set the ConditionVariable on the condition_variable side of the operation
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     */
    public function set_condition_variable($conditionVariable)
    {
        $this->conditionVariable = $conditionVariable;
    }

    /**
     *
     * @return string
     */
    public function get_format()
    {
        return $this->format;
    }

    /**
     *
     * @param string $format
     */
    public function set_format($format)
    {
        $this->format = $format;
    }
}
