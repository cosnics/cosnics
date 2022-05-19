<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

/**
 * This type of condition requires that one or more of its aggregated conditions be met.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Query\Condition
 */
class AndCondition extends MultipleAggregateCondition
{
    const OPERATOR = ' AND ';

    public function getOperator(): string
    {
        return self::OPERATOR;
    }
}
