<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Enum\ComparisonTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\Condition\EqualityConditionTranslator;

/**
 * This class represents a selection condition that requires an equality.
 * An example of an instance would be a condition
 * that requires that the id of a DataClass be the number 4.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition
 */
class EqualityCondition extends ComparisonCondition implements ConditionInterface
{
    public function __construct(
        ConditionVariableInterface $leftConditionVariable, ?ConditionVariableInterface $rightConditionVariable
    )
    {
        parent::__construct($leftConditionVariable, ComparisonTypeEnum::EQUAL, $rightConditionVariable);
    }

    public function getConditionTranslatorClass(): string
    {
        return EqualityConditionTranslator::class;
    }
}
