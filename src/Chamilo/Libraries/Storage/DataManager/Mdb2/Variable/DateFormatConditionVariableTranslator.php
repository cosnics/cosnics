<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Variable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DateFormatConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Variable\ConditionVariableTranslator::translate()
     */
    public function translate()
    {
        $strings = array();

        $strings[] = 'FROM_UNIXTIME';

        $strings[] = '(';

        $strings[] = static :: render($this->get_condition_variable()->get_condition_variable());
        $strings[] = ', ';
        $strings[] = "'" . $this->get_condition_variable()->get_format() . "'";
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
