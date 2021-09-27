<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A ConditionVariable that describes a static value
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class StaticConditionVariable extends ConditionVariable
{

    /**
     * Whether or not the variable should be quoted
     *
     * @var boolean
     */
    private $quote;

    /**
     * A static value that should remain unchanged in the Condition
     *
     * @var string|int
     */
    private $value;

    /**
     *
     * @param string|int $value
     * @param boolean $quote
     */
    public function __construct($value, $quote = true)
    {
        $this->value = $value;
        $this->quote = $quote;
    }

    /**
     * @return string[]
     */
    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        $hashParts[] = $this->get_value();
        $hashParts[] = $this->get_quote();

        return $hashParts;
    }

    /**
     *
     * @return boolean
     */
    public function get_quote()
    {
        return $this->quote;
    }

    /**
     *
     * @param boolean $quote
     */
    public function set_quote($quote)
    {
        $this->quote = $quote;
    }

    /**
     *
     * @return string|int
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     *
     * @param string|int $value
     */
    public function set_value($value)
    {
        $this->value = $value;
    }
}
