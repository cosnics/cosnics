<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\Query\Condition
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EndsWithCondition extends PatternMatchCondition
{

    public function __construct(
        ConditionVariable $conditionVariable, string $pattern, ?string $storageUnit = null, ?bool $isAlias = false
    )
    {
        parent::__construct($conditionVariable, '*' . $pattern, $storageUnit, $isAlias);
    }
}
