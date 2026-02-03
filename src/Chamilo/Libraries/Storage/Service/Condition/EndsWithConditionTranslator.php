<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EndsWithCondition;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;

/**
 * @package Chamilo\Libraries\Storage\Service\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EndsWithConditionTranslator extends PatternMatchConditionTranslator implements ConditionTranslatorInterface
{
    public function getConditionClassName(): string
    {
        return EndsWithCondition::class;
    }
}
