<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DateFormatConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        $strings = array();

        $strings[] = 'FROM_UNIXTIME';

        $strings[] = '(';

        $strings[] = $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $this->getConditionVariable()->get_condition_variable());
        $strings[] = ', ';
        $strings[] = "'" . $this->getConditionVariable()->get_format() . "'";
        $strings[] = ')';

        if ($this->getConditionVariable()->get_alias())
        {
            return implode('', $strings) . ' AS ' . $this->getConditionVariable()->get_alias();
        }
        else
        {
            return implode('', $strings);
        }
    }
}
