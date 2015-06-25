<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Variable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     * Translates the condition variable
     * 
     * @return string
     */
    public function translate()
    {
        $strings = array();
        
        $strings[] = 'CASE ';
        
        foreach ($this->get_condition_variable()->get_case_elements() as $case_element)
        {
            $strings[] = static :: render($case_element);
        }
        
        $strings[] = ' END';
        
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
