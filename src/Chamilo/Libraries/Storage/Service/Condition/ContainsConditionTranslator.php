<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ContainsCondition;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContainsConditionTranslator extends PatternMatchConditionTranslator
{
    public const CONDITION_CLASS = ContainsCondition::class;
}
