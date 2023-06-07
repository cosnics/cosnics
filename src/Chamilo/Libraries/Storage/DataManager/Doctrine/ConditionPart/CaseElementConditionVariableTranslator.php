<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseElementConditionVariableTranslator extends ConditionVariableTranslator
{
    public const CONDITION_CLASS = CaseElementConditionVariable::class;

    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, CaseElementConditionVariable $caseElementConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        if ($caseElementConditionVariable->getCondition() instanceof Condition)
        {
            $strings[] = 'WHEN ';
            $strings[] = $this->getConditionPartTranslatorService()->translate(
                $dataClassDatabase, $caseElementConditionVariable->getCondition(), $enableAliasing
            );
            $strings[] = ' THEN ';
        }
        else
        {
            $strings[] = ' ELSE ';
        }

        $strings[] = $this->getConditionPartTranslatorService()->translate(
            $dataClassDatabase, $caseElementConditionVariable->getStatement(), $enableAliasing
        );

        return implode('', $strings);
    }
}
