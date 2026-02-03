<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;

/**
 * @package Chamilo\Libraries\Storage\Service\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContainsConditionTranslator extends PatternMatchConditionTranslator implements ConditionTranslatorInterface
{
    public function getConditionClassName(): string
    {
        return ContainsCondition::class;
    }
}
