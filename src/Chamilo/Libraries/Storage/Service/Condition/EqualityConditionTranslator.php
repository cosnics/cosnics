<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EqualityConditionTranslator extends ComparisonConditionTranslator
{
    public const CONDITION_CLASS = EqualityCondition::class;
}
