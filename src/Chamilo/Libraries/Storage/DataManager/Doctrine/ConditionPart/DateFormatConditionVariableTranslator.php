<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DateFormatConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\DateFormatConditionVariable
     */
    public function getConditionVariable()
    {
        return parent::getConditionVariable();
    }

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate(bool $enableAliasing = true)
    {
        $strings = array();

        $strings[] = 'FROM_UNIXTIME';

        $strings[] = '(';

        $strings[] = $this->getConditionPartTranslatorService()->translate(
            $this->getDataClassDatabase(), $this->getConditionVariable()->get_condition_variable(), $enableAliasing
        );
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
