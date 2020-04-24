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
     * @return \Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition
     */
    public function getCondition()
    {
        return parent::getCondition();
    }

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate(bool $enableAliasing = true)
    {
        if ($this->getCondition()->get_operator() == ComparisonCondition::EQUAL &&
            is_null($this->getCondition()->get_value()))
        {
            return $this->translateEqualityConditionWithEmptyValue($this->getCondition(), $enableAliasing);
        }
        else
        {
            $operatorString = $this->translateOperator($this->getCondition()->get_operator());

            return $this->translateCondition($this->getCondition(), $operatorString, $enableAliasing);
        }
    }

    /**
     * Translates the (in)equalitycondition with the given operator_string
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition $condition
     * @param string $operatorString
     *
     * @return string
     */
    private function translateCondition(ComparisonCondition $condition, $operatorString, bool $enableAliasing = true)
    {
        return $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $condition->get_name(), $enableAliasing
            ) . ' ' . $operatorString . ' ' . $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $condition->get_value(), $enableAliasing
            );
    }

    /**
     * Translates an equality condition with an empty value
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition $condition
     * @param boolean $enableAliasing
     *
     * @return string
     */
    private function translateEqualityConditionWithEmptyValue(
        ComparisonCondition $condition, bool $enableAliasing = true
    )
    {
        return $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $condition->get_name(), $enableAliasing
            ) . ' IS NULL';
    }

    /**
     * Translates the operator to the correct string
     *
     * @param integer $conditionOperator
     *
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
}
