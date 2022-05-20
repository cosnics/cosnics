<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\Query\Condition
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StartsWithCondition extends PatternMatchCondition
{
    public function __construct(
        ConditionVariable $conditionVariable, string $pattern
    )
    {
        parent::__construct($conditionVariable, $pattern . '*');
    }

}
