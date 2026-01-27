<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\CaseElementConditionVariable;
use Chamilo\Libraries\Storage\Service\ConditionVariableTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseElementConditionVariableTranslator extends ConditionVariableTranslator
{
    public const CONDITION_CLASS = CaseElementConditionVariable::class;

    public function translate(
        QueryBuilder $querybuilder, CaseElementConditionVariable $caseElementConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        if ($caseElementConditionVariable->getCondition() instanceof Condition)
        {
            $strings[] = 'WHEN ';
            $strings[] = $this->getConditionPartTranslatorService()->translate(
                $querybuilder, $caseElementConditionVariable->getCondition(), $enableAliasing
            );
            $strings[] = ' THEN ';
        }
        else
        {
            $strings[] = ' ELSE ';
        }

        $strings[] = $this->getConditionPartTranslatorService()->translate(
            $querybuilder, $caseElementConditionVariable->getStatement(), $enableAliasing
        );

        return implode('', $strings);
    }
}
