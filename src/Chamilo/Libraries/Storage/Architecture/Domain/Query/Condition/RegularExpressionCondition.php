<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\Condition\RegularExpressionConditionTranslator;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RegularExpressionCondition implements ConditionInterface
{
    use HashableTrait;

    private ConditionVariableInterface $conditionVariable;

    private string $regularExpression;

    public function __construct(
        ConditionVariableInterface $conditionVariable, string $regularExpression
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->regularExpression = $regularExpression;
    }

    public function getConditionTranslatorClass(): string
    {
        return RegularExpressionConditionTranslator::class;
    }

    public function getConditionVariable(): ConditionVariableInterface
    {
        return $this->conditionVariable;
    }

    public function getHashParts(): array
    {
        return [
            static::class,
            $this->getConditionVariable()->getHashParts(),
            $this->getRegularExpression()
        ];
    }

    public function getRegularExpression(): string
    {
        return $this->regularExpression;
    }
}
