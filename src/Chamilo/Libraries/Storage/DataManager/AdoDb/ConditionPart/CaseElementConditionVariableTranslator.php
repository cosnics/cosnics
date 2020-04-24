<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseElementConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable
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

        $conditionVariable = $this->getConditionVariable();

        if ($conditionVariable->get_condition() instanceof Condition)
        {
            $strings[] = 'WHEN ';
            $strings[] = $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $conditionVariable->get_condition(), $enableAliasing
            );
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
