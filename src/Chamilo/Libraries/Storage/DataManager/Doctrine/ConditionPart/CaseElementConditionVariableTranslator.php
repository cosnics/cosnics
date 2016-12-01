<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseElementConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        $strings = array();
        
        $conditionVariable = $this->getConditionVariable();
        
        if ($conditionVariable->get_condition() instanceof Condition)
        {
            $strings[] = 'WHEN ';
            $strings[] = $this->getConditionPartTranslatorService()->translateConditionPart(
                $this->getDataClassDatabase(), 
                $conditionVariable->get_condition());
            $strings[] = ' THEN ';
        }
        else
        {
            $strings[] = ' ELSE ';
        }
        
        $strings[] = $conditionVariable->get_statement();
        
        return implode('', $strings);
    }
}
