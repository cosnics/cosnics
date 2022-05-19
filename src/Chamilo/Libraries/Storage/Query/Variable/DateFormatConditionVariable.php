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
    private ?string $alias;

    private ConditionVariable $conditionVariable;

    private string $format;

    public function __construct(string $format, ConditionVariable $conditionVariable, ?string $alias = null)
    {
        $this->conditionVariable = $conditionVariable;
        $this->format = $format;
        $this->alias = $alias;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias)
    {
        $this->alias = $alias;
    }

    public function getConditionVariable(): ConditionVariable
    {
        return $this->conditionVariable;
    }

    public function setConditionVariable(ConditionVariable $conditionVariable): DateFormatConditionVariable
    {
        $this->conditionVariable = $conditionVariable;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): DateFormatConditionVariable
    {
        $this->format = $format;

        return $this;
    }

    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        $hashParts[] = $this->getConditionVariable()->getHashParts();
        $hashParts[] = $this->getFormat();
        $hashParts[] = $this->getAlias();

        return $hashParts;
    }
}
