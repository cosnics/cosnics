<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\DoctrineEntityInterface;
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
class PropertiesConditionVariableTranslator extends ConditionVariableTranslator
    implements ConditionVariableTranslatorInterface
{
    public function getConditionVariableClassName(): string
    {
        return PropertiesConditionVariable::class;
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function translate(
        DBALQueryBuilder|ORMQueryBuilder $querybuilder, PropertiesConditionVariable $propertiesConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        if (is_subclass_of($propertiesConditionVariable->getDataClassName(), DoctrineEntityInterface::class)) {
            return $propertiesConditionVariable->getDataClassName()::getAlias();
        }
        elseif ($enableAliasing) {
            return $propertiesConditionVariable->getDataClassName()::getAlias() . '.*';
        }
        else {
            return '*';
        }
    }
}
