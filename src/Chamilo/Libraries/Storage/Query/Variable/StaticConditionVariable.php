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

    private bool $quote;

    /**
     * @var mixed $value
     */
    private $value;

    public function __construct($value, ?bool $quote = true)
    {
        $this->value = $value;
        $this->quote = $quote;
    }

    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        $hashParts[] = $this->getValue();
        $hashParts[] = $this->getQuote();

        return $hashParts;
    }

    public function getQuote(): bool
    {
        return $this->quote;
    }

    public function setQuote(bool $quote): StaticConditionVariable
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): StaticConditionVariable
    {
        $this->value = $value;

        return $this;
    }
}
