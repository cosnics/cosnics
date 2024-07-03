<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\Condition\EndsWithCondition;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EndsWithConditionTranslator extends PatternMatchConditionTranslator
{
    public const CONDITION_CLASS = EndsWithCondition::class;
}
