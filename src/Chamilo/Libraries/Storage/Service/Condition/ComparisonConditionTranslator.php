<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

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
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function translate(
        QueryBuilder $querybuilder, ComparisonCondition $comparisonCondition, ?bool $enableAliasing = true
    ): string
    {
        $string = [];

        $rightConditionVariable = $comparisonCondition->getRightConditionVariable();

        $string[] = $this->getConditionVariableTranslatorCollection()->translate(
            $querybuilder, $comparisonCondition->getLeftConditionVariable(), $enableAliasing
        );

        if ($comparisonCondition->getOperator() == ComparisonCondition::EQUAL && is_null($rightConditionVariable)) {
            $string[] = 'IS NULL';

            return implode(' ', $string);
        }

        $string[] = $this->translateOperator($comparisonCondition->getOperator());

        $string[] = $this->getConditionVariableTranslatorCollection()->translate(
            $querybuilder, $rightConditionVariable, $enableAliasing
        );

        return implode(' ', $string);
    }

    private function translateOperator(int $conditionOperator): string
    {
        switch ($conditionOperator) {
            case ComparisonCondition::GREATER_THAN :
                $translatedOperator = '>';
                break;
            case ComparisonCondition::GREATER_THAN_OR_EQUAL :
                $translatedOperator = '>=';
                break;
            case ComparisonCondition::LESS_THAN :
                $translatedOperator = '<';
                break;
            case ComparisonCondition::LESS_THAN_OR_EQUAL :
                $translatedOperator = '<=';
                break;
            case ComparisonCondition::EQUAL :
                $translatedOperator = '=';
                break;
            default :
                die('Unknown operator for Comparison condition');
        }

        return $translatedOperator;
    }
}
