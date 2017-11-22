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
    /**
     * The operator
     *
     * @var string
     */
    const OPERATOR = ' AND ';

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Condition\MultipleAggregateCondition::get_operator()
     */
    public function get_operator()
    {
        return self::OPERATOR;
    }
}
