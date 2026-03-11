<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition;

use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Service\Condition\OrConditionTranslator;

/**
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition
 */
class OrCondition extends MultipleAggregateCondition implements ConditionInterface
{
    public const string OPERATOR = ' OR ';

    public function getConditionTranslatorClass(): string
    {
        return OrConditionTranslator::class;
    }

    public function getOperator(): string
    {
        return self::OPERATOR;
    }
}
