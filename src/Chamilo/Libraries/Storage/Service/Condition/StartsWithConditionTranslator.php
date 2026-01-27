<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\StartsWithCondition;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StartsWithConditionTranslator extends PatternMatchConditionTranslator
{
    public const CONDITION_CLASS = StartsWithCondition::class;
}
