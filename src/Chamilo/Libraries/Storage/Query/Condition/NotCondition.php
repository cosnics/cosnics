<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

/**
 * This type of aggregate condition negates a single condition, thus requiring that that condition not be met.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Query\Condition
 */
class NotCondition extends AggregateCondition
{

    private Condition $condition;

    public function __construct(Condition $condition)
    {
        $this->condition = $condition;
    }

    public function getCondition(): Condition
    {
        return $this->condition;
    }

    public function getHashParts(): array
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->getCondition()->getHashParts();

        return $hashParts;
    }
}
