<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Variable;

use Chamilo\Libraries\Storage\Cache\ConditionVariableCache;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FunctionConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Variable\ConditionVariableTranslator::translate()
     */
    public function translate()
    {
        if (! ConditionVariableCache :: exists($this->get_condition_variable()))
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

            if ($this->get_condition_variable()->get_function() !== FunctionConditionVariable :: DISTINCT)
            {
                $strings[] = '(';
            }
            else
            {
                $strings[] = ' ';
            }

            $strings[] = static :: render($this->get_condition_variable()->get_condition_variable());

            if ($this->get_condition_variable()->get_function() !== FunctionConditionVariable :: DISTINCT)
            {
                $strings[] = ')';
            }

            if ($this->get_condition_variable()->get_alias())
            {
                $value = implode('', $strings) . ' AS ' . $this->get_condition_variable()->get_alias();
            }
            else
            {
                $value = implode('', $strings);
            }

            ConditionVariableCache :: set_cache($this->get_condition_variable(), $value);
        }

        return ConditionVariableCache :: get($this->get_condition_variable());
    }
}
