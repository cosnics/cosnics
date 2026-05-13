<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Enum\ComparisonTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionTranslator;
use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class ComparisonConditionTranslator extends ConditionTranslator implements ConditionTranslatorInterface
{
    public function getConditionClassName(): string
    {
        return ComparisonCondition::class;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        DBALQueryBuilder|ORMQueryBuilder $querybuilder, ComparisonCondition $comparisonCondition,
        ?bool $enableAliasing = true
    ): string
    {
        $string = [];

        $rightConditionVariable = $comparisonCondition->getRightConditionVariable();

        $string[] = $this->conditionVariableTranslatorRegistry->translate(
            $querybuilder, $comparisonCondition->getLeftConditionVariable(), $enableAliasing
        );

        if ($comparisonCondition->getOperator() == ComparisonTypeEnum::EQUAL && is_null($rightConditionVariable)) {
            $string[] = 'IS NULL';

            return implode(' ', $string);
        }

        $string[] = $comparisonCondition->getOperator()->toString();

        $string[] = $this->conditionVariableTranslatorRegistry->translate(
            $querybuilder, $rightConditionVariable, $enableAliasing
        );

        return implode(' ', $string);
    }
}
