<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\CaseConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariableTranslator;
use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\ConditionVariable
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseConditionVariableTranslator extends ConditionVariableTranslator
    implements ConditionVariableTranslatorInterface
{
    public function getConditionVariableClassName(): string
    {
        return CaseConditionVariable::class;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        DBALQueryBuilder|ORMQueryBuilder $querybuilder, CaseConditionVariable $caseConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        $strings[] = 'CASE ';

        foreach ($caseConditionVariable->get() as $caseElement) {
            $strings[] = $this->conditionVariableTranslatorRegistry->translate(
                $querybuilder, $caseElement, $enableAliasing
            );
        }

        $strings[] = ' END';

        if ($caseConditionVariable->getAlias()) {
            $value = implode(' ', $strings) . ' AS ' . $caseConditionVariable->getAlias();
        }
        else {
            $value = implode(' ', $strings);
        }

        return $value;
    }
}
