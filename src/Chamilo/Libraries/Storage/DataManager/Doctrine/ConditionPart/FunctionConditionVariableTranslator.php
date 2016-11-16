<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FunctionConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        $strings = array();
        switch ($this->getConditionVariable()->get_function())
        {
            case FunctionConditionVariable::SUM :
                $strings[] = 'SUM';
                break;
            case FunctionConditionVariable::COUNT :
                $strings[] = 'COUNT';
                break;
            case FunctionConditionVariable::MIN :
                $strings[] = 'MIN';
                break;
            case FunctionConditionVariable::MAX :
                $strings[] = 'MAX';
                break;
            case FunctionConditionVariable::DISTINCT :
                $strings[] = 'DISTINCT';
                break;
            case FunctionConditionVariable::AVERAGE :
                $strings[] = 'AVG';
                break;
        }
        
        if ($this->getConditionVariable()->get_function() !== FunctionConditionVariable::DISTINCT)
        {
            $strings[] = '(';
        }
        else
        {
            $strings[] = ' ';
        }
        
        $strings[] = $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(), 
            $this->getConditionVariable()->get_condition_variable());
        
        if ($this->getConditionVariable()->get_function() !== FunctionConditionVariable::DISTINCT)
        {
            $strings[] = ')';
        }
        
        if ($this->getConditionVariable()->get_alias())
        {
            $value = implode('', $strings) . ' AS ' . $this->getConditionVariable()->get_alias();
        }
        else
        {
            $value = implode('', $strings);
        }
        
        return $value;
    }
}
