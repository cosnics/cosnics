<?php
namespace Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition;

use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EqualityConditionTranslator extends ComparisonConditionTranslator
{
    public const CONDITION_CLASS = EqualityCondition::class;
}
