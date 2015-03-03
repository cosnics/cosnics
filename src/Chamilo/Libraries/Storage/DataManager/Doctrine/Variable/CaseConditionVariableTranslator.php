<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Variable;

use Chamilo\Libraries\Storage\Cache\ConditionVariableCache;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Variable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseConditionVariableTranslator extends ConditionVariableTranslator
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

            $strings[] = 'CASE ';

            foreach ($this->get_condition_variable()->get_case_elements() as $case_element)
            {
                $strings[] = static :: render($case_element);
            }

            $strings[] = ' END';

            if ($this->get_condition_variable()->get_alias())
            {
                $value = implode(' ', $strings) . ' AS ' . $this->get_condition_variable()->get_alias();
            }
            else
            {
                $value = implode(' ', $strings);
            }

            ConditionVariableCache :: set_cache($this->get_condition_variable(), $value);
        }

        return ConditionVariableCache :: get($this->get_condition_variable());
    }
}
