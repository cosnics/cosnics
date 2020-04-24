<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Variable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Replaced by the ConditionPartTranslators and related service and factory
 */
class DateFormatConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     * @return string
     */
    public function translate()
    {
        $strings = array();

        $strings[] = 'FROM_UNIXTIME';

        $strings[] = '(';

        $strings[] = static::render($this->get_condition_variable()->get_condition_variable());
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
