<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Condition;

use Chamilo\Libraries\Storage\DataManager\Mdb2\Variable\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class InequalityConditionTranslator extends ConditionTranslator
{

    public function translate()
    {
        switch ($this->get_condition()->get_operator())
        {
            case InequalityCondition :: GREATER_THAN :
                $operator = '>';
                break;
            case InequalityCondition :: GREATER_THAN_OR_EQUAL :
                $operator = '>=';
                break;
            case InequalityCondition :: LESS_THAN :
                $operator = '<';
                break;
            case InequalityCondition :: LESS_THAN_OR_EQUAL :
                $operator = '<=';
                break;
            default :
                die('Unknown operator for inequality condition');
        }

        return ConditionVariableTranslator :: render($this->get_condition()->get_name()) . ' ' .
             $operator . ' ' .
             ConditionVariableTranslator :: render($this->get_condition()->get_value());
    }
}
