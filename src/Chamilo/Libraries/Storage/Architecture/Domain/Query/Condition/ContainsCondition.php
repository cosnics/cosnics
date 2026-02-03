<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition;

use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\Condition\ContainsConditionTranslator;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContainsCondition extends PatternMatchCondition implements ConditionInterface
{
    public function __construct(ConditionVariableInterface $conditionVariable, string $pattern)
    {
        parent::__construct($conditionVariable, '*' . $pattern . '*');
    }

    public function getConditionTranslatorClass(): string
    {
        return ContainsConditionTranslator::class;
    }
}
