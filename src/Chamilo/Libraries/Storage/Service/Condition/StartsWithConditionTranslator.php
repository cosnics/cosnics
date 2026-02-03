<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\StartsWithCondition;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;

/**
 * @package Chamilo\Libraries\Storage\Service\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StartsWithConditionTranslator extends PatternMatchConditionTranslator implements ConditionTranslatorInterface
{
    public function getConditionClassName(): string
    {
        return StartsWithCondition::class;
    }
}
