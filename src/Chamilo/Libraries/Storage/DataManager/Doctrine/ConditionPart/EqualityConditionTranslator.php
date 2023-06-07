<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EqualityConditionTranslator extends ComparisonConditionTranslator
{
    public const CONDITION_CLASS = EqualityCondition::class;
}
