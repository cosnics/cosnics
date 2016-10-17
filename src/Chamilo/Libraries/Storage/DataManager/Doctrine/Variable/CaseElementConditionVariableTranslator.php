<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Variable;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Condition\ConditionTranslator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Variable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseElementConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Variable\ConditionVariableTranslator::translate()
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
