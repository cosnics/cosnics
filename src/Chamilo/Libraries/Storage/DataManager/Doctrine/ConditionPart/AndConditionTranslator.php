<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\Condition\AndCondition;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AndConditionTranslator extends MultipleAggregateConditionTranslator
{
    public const CONDITION_CLASS = AndCondition::class;
}
