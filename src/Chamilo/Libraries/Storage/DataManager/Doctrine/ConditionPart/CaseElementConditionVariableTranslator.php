<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\ConditionPart;
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
     * @return \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable
     */
    public function getConditionVariable(): ConditionPart
    {
        return parent::getConditionVariable();
    }

    public function translate(?bool $enableAliasing = true): string
    {
        $strings = [];

        $conditionVariable = $this->getConditionVariable();

        if ($conditionVariable->getCondition() instanceof Condition)
        {
            $strings[] = 'WHEN ';
            $strings[] = $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $conditionVariable->getCondition(), $enableAliasing
            );
            $strings[] = ' THEN ';
        }
        else
        {
            $strings[] = ' ELSE ';
        }

        $strings[] = $conditionVariable->getStatement();

        return implode('', $strings);
    }
}
