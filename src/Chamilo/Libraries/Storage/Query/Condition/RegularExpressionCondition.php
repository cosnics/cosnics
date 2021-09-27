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

    private ?bool $isAlias;

    private string $regularExpression;

    private ?string $storageUnit;

    public function __construct(
        ConditionVariable $conditionVariable, string $regularExpression, string $storageUnit = null,
        bool $isAlias = false
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->regularExpression = $regularExpression;
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
        $hashParts[] = $this->getRegularExpression();
        $hashParts[] = $this->getStorageUnit();
        $hashParts[] = $this->isAlias();

        return $hashParts;
    }

    public function getRegularExpression(): string
    {
        return $this->regularExpression;
    }

    public function getStorageUnit(): ?string
    {
        return $this->storageUnit;
    }

    public function isAlias(): ?bool
    {
        return $this->isAlias;
    }
}
