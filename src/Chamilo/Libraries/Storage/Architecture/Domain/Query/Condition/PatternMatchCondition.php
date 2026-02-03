<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\Condition\PatternMatchConditionTranslator;

/**
 * This class represents a selection condition that uses a pattern for matching.
 * An example of an instance would be a  condition that requires that the title of an object contains the word "math".
 * The pattern is case insensitive and supports two types of wildcard characters: an asterisk (*) must match any
 * sequence of characters, and a question mark (?) must match a single character.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition
 */
class PatternMatchCondition implements ConditionInterface
{
    use HashableTrait;

    private ConditionVariableInterface $conditionVariable;

    private string $pattern;

    public function __construct(
        ConditionVariableInterface $conditionVariable, string $pattern
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->pattern = $pattern;
    }

    public function getConditionTranslatorClass(): string
    {
        return PatternMatchConditionTranslator::class;
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
            $this->getPattern()
        ];
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }
}
