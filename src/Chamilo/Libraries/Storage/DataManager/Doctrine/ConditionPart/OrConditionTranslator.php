<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\Condition\OrCondition;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OrConditionTranslator extends MultipleAggregateConditionTranslator
{
    public const CONDITION_CLASS = OrCondition::class;
}
