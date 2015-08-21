<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Variable;

use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FunctionConditionVariableTranslator extends ConditionVariableTranslator
{

    public function translate()
    {
        $strings = array();
        switch ($this->get_condition_variable()->get_function())
        {
            case FunctionConditionVariable :: SUM :
                $strings[] = 'SUM';
                break;
            case FunctionConditionVariable :: COUNT :
                $strings[] = 'COUNT';
                break;
            case FunctionConditionVariable :: MIN :
                $strings[] = 'MIN';
                break;
            case FunctionConditionVariable :: MAX :
                $strings[] = 'MAX';
                break;
            case FunctionConditionVariable :: DISTINCT :
                $strings[] = 'DISTINCT';
                break;
        }

        $strings[] = '(';
        $strings[] = static :: render(
            $this->get_condition_variable()->get_condition_variable());
        $strings[] = ')';
        if ($this->get_condition_variable()->get_alias())
        {
            return implode('', $strings) . ' AS ' . $this->get_condition_variable()->get_alias();
        }
        else
        {
            return implode('', $strings);
        }
    }
}
