<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * This class represents a selection condition that uses a pattern for matching.
 * An example of an instance would be a
 * condition that requires that the title of an object contains the word "math". The pattern is case insensitive and
 * supports two types of wildcard characters: an asterisk (*) must match any sequence of characters, and a question mark
 * (?) must match a single character.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Query\Condition
 */
class PatternMatchCondition extends Condition
{
    private ConditionVariable $conditionVariable;

    private bool $isAlias;

    private string $pattern;

    private ?string $storageUnit;

    public function __construct(
        ConditionVariable $conditionVariable, string $pattern, ?string $storageUnit = null, ?bool $isAlias = false
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->pattern = $pattern;
        $this->storageUnit = $storageUnit;
        $this->isAlias = $isAlias;
    }

    public function getConditionVariable(): ConditionVariable
    {
        return $this->conditionVariable;
    }

    public function getHashParts(): array
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->getConditionVariable()->getHashParts();
        $hashParts[] = $this->getPattern();
        $hashParts[] = $this->getStorageUnit();
        $hashParts[] = $this->isAlias();

        return $hashParts;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getStorageUnit(): ?string
    {
        return $this->storageUnit;
    }

    public function isAlias(): bool
    {
        return $this->isAlias;
    }
}
