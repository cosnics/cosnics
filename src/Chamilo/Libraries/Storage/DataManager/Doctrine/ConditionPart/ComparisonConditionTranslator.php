<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ComparisonConditionTranslator extends ConditionTranslator
{

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition
     */
    public function getCondition(): ConditionPart
    {
        return parent::getCondition();
    }

    public function translate(?bool $enableAliasing = true): string
    {
        if ($this->getCondition()->getOperator() == ComparisonCondition::EQUAL &&
            is_null($this->getCondition()->getRightConditionVariable()))
        {
            return $this->translateEqualityConditionWithEmptyValue($this->getCondition(), $enableAliasing);
        }
        else
        {
            $operatorString = $this->translateOperator($this->getCondition()->getOperator());

            return $this->translateCondition($this->getCondition(), $operatorString, $enableAliasing);
        }
    }

    private function translateCondition(
        ComparisonCondition $condition, int $operatorString, ?bool $enableAliasing = true
    ): string
    {
        return $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $condition->getLeftConditionVariable(), $enableAliasing
            ) . ' ' . $operatorString . ' ' . $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $condition->getRightConditionVariable(), $enableAliasing
            );
    }

    private function translateEqualityConditionWithEmptyValue(
        ComparisonCondition $condition, ?bool $enableAliasing = true
    ): string
    {
        return $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $condition->getLeftConditionVariable(), $enableAliasing
            ) . ' IS NULL';
    }
    
    private function translateOperator(int $conditionOperator): string
    {
        switch ($conditionOperator)
        {
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
