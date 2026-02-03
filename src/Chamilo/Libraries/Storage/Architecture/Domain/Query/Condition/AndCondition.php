<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition;

use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Service\Condition\AndConditionTranslator;

/**
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition
 */
class AndCondition extends MultipleAggregateCondition implements ConditionInterface
{
    public const OPERATOR = ' AND ';

    public function getConditionTranslatorClass(): string
    {
        return AndConditionTranslator::class;
    }

    public function getOperator(): string
    {
        return self::OPERATOR;
    }
}
