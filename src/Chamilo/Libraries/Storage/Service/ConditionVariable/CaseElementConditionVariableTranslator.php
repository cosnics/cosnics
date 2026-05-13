<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\CaseElementConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
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
class CaseElementConditionVariableTranslator extends ConditionVariableTranslator
    implements ConditionVariableTranslatorInterface
{
    public function getConditionVariableClassName(): string
    {
        return CaseElementConditionVariable::class;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        DBALQueryBuilder|ORMQueryBuilder $querybuilder, CaseElementConditionVariable $caseElementConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        if ($caseElementConditionVariable->getCondition() instanceof ConditionInterface) {
            $strings[] = 'WHEN';
            $strings[] = $this->conditionTranslatorRegistry->translate(
                $querybuilder, $caseElementConditionVariable->getCondition(), $enableAliasing
            );
            $strings[] = 'THEN';
        }
        else {
            $strings[] = 'ELSE';
        }

        $strings[] = $this->conditionVariableTranslatorRegistry->translate(
            $querybuilder, $caseElementConditionVariable->getStatement(), $enableAliasing
        );

        return implode(' ', $strings);
    }
}
