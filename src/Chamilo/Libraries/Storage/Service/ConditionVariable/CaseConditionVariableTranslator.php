<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\CaseConditionVariable;
use Chamilo\Libraries\Storage\Service\ConditionVariableTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseConditionVariableTranslator extends ConditionVariableTranslator
{
    public const CONDITION_CLASS = CaseConditionVariable::class;

    public function translate(
        QueryBuilder $querybuilder, CaseConditionVariable $caseConditionVariable, ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        $strings[] = 'CASE ';

        foreach ($caseConditionVariable->get() as $caseElement)
        {
            $strings[] = $this->getConditionPartTranslatorService()->translate(
                $querybuilder, $caseElement, $enableAliasing
            );
        }

        $strings[] = ' END';

        if ($caseConditionVariable->getAlias())
        {
            $value = implode(' ', $strings) . ' AS ' . $caseConditionVariable->getAlias();
        }
        else
        {
            $value = implode(' ', $strings);
        }

        return $value;
    }
}
