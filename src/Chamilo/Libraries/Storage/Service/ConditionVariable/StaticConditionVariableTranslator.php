<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariableTranslator;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\ConditionVariable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class StaticConditionVariableTranslator extends ConditionVariableTranslator
    implements ConditionVariableTranslatorInterface
{
    public function getConditionVariableClassName(): string
    {
        return StaticConditionVariable::class;
    }

    public function translate(
        DBALQueryBuilder|ORMQueryBuilder $querybuilder, StaticConditionVariable $staticConditionVariable
    ): string
    {
        if ($querybuilder instanceof DBALQueryBuilder) {
            $type = $staticConditionVariable->getType() ?: ParameterType::STRING;
        }
        else {
            $type = $staticConditionVariable->getType();
        }

        return $querybuilder->createNamedParameter($staticConditionVariable->getValue(), $type);
    }
}
