<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
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
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        if ($this->getCondition()->get_operator() == ComparisonCondition::EQUAL &&
             is_null($this->getCondition()->get_value()))
        {
            return $this->translateEqualityConditionWithEmptyValue($this->getCondition());
        }
        else
        {
            $operatorString = $this->translateOperator($this->getCondition()->get_operator());

            return $this->translateCondition($this->getCondition(), $operatorString);
        }
    }

    /**
     * Translates the operator to the correct string
     *
     * @param integer $condition_operator
     * @return string
     */
    private function translateOperator($conditionOperator)
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

    /**
     * Translates an equality condition with an empty value
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return string
     */
    private function translateEqualityConditionWithEmptyValue($condition)
    {
        return $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $condition->get_name()) . ' IS NULL';
    }

    /**
     * Translates the (in)equalitycondition with the given operator_string
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param string $operator_string
     * @return string
     */
    private function translateCondition($condition, $operatorString)
    {
        return $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $condition->get_name()) . ' ' . $operatorString . ' ' .
             $this->getConditionPartTranslatorService()->translateConditionPart(
                $this->getDataClassDatabase(),
                $condition->get_value());
    }
}
