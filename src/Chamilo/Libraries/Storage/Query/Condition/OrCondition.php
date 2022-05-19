<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

/**
 * This type of condition requires that one or more of its aggregated conditions be met.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Query\Condition
 */
class OrCondition extends MultipleAggregateCondition
{
    const OPERATOR = ' OR ';

    public function getOperator(): string
    {
        return self::OPERATOR;
    }
}
