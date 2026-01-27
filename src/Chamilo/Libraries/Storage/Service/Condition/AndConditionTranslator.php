<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AndConditionTranslator extends MultipleAggregateConditionTranslator
{
    public const CONDITION_CLASS = AndCondition::class;
}
