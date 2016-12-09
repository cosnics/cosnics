<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Condition;

use Chamilo\Libraries\Storage\DataManager\Mdb2\Variable\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Condition
 * @author Pieterjan Broekaert <pieterjan.broekaert@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ComparisonConditionTranslator extends ConditionTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Condition\ConditionTranslator::translate()
     */
    public function translate()
    {
        $operator_string = $this->translate_operator($this->get_condition()->get_operator());
        
        // if the operator is an equality operator and no value is given, translate to 'is null' equality condition
        if ($this->get_condition()->get_operator() == ComparisonCondition::EQUAL &&
             is_null($this->get_condition()->get_value()))
        {
            return $this->translate_equality_condition_with_empty_value($this->get_condition());
        }
        else
        {
            return $this->translate_condition($this->get_condition(), $operator_string);
        }
    }

    /**
     * Translates the operator to the correct string
     * 
     * @param integer $condition_operator
     * @return string
     */
    private function translate_operator($condition_operator)
    {
        switch ($condition_operator)
        {
            case ComparisonCondition::GREATER_THAN :
                $translated_operator = '>';
                break;
            case ComparisonCondition::GREATER_THAN_OR_EQUAL :
                $translated_operator = '>=';
                break;
            case ComparisonCondition::LESS_THAN :
                $translated_operator = '<';
                break;
            case ComparisonCondition::LESS_THAN_OR_EQUAL :
                $translated_operator = '<=';
                break;
            case ComparisonCondition::EQUAL :
                $translated_operator = '=';
                break;
            default :
                die('Unknown operator for Comparison condition');
        }
        
        return $translated_operator;
    }

    /**
     * Translates an equality condition with an empty value
     * 
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return string
     */
    private function translate_equality_condition_with_empty_value($condition)
    {
        return ConditionVariableTranslator::render($condition->get_name()) . ' IS NULL';
    }

    /**
     * Translates the (in)equalitycondition with the given operator_string
     * 
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param string $operator_string
     * @return string
     */
    private function translate_condition($condition, $operator_string)
    {
        return ConditionVariableTranslator::render($condition->get_name()) . ' ' . $operator_string . ' ' .
             ConditionVariableTranslator::render($condition->get_value());
    }
}
