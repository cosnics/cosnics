<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\Condition\StartsWithCondition;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StartsWithConditionTranslator extends PatternMatchConditionTranslator
{
    public const CONDITION_CLASS = StartsWithCondition::class;
}
