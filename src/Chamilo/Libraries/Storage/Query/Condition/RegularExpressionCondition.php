<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\Query\Condition
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RegularExpressionCondition extends Condition
{

    private ConditionVariable $conditionVariable;

    private string $regularExpression;

    public function __construct(
        ConditionVariable $conditionVariable, string $regularExpression
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->regularExpression = $regularExpression;
    }

    public function getConditionVariable(): ConditionVariable
    {
        return $this->conditionVariable;
    }

    public function getHashParts(): array
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->getConditionVariable()->getHashParts();
        $hashParts[] = $this->getRegularExpression();

        return $hashParts;
    }

    public function getRegularExpression(): string
    {
        return $this->regularExpression;
    }
}
