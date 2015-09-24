<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Variable;

use Chamilo\Libraries\Storage\DataManager\Mdb2\Condition\ConditionTranslator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseElementConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     * Translates the condition variable
     *
     * @return string
     */
    public function translate()
    {
        $strings = array();

        $condition_variable = $this->get_condition_variable();

        if ($condition_variable->get_condition() instanceof Condition)
        {
            $strings[] = 'WHEN ';
            $strings[] = ConditionTranslator :: render($condition_variable->get_condition());
            $strings[] = ' THEN ';
        }
        else
        {
            $strings[] = ' ELSE ';
        }

        $strings[] = $condition_variable->get_statement();

        return implode('', $strings);
    }
}
