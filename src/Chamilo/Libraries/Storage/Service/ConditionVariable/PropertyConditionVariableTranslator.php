<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariableTranslator;
use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\ConditionVariable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertyConditionVariableTranslator extends ConditionVariableTranslator
    implements ConditionVariableTranslatorInterface
{
    public function getConditionVariableClassName(): string
    {
        return PropertyConditionVariable::class;
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function translate(
        DBALQueryBuilder|ORMQueryBuilder $querybuilder, PropertyConditionVariable $propertyConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        if ($enableAliasing) {
            $alias = $propertyConditionVariable->getDataClassName()::getAlias();
        }
        else {
            $alias = null;
        }

        $translationParts = [];

        if (!empty($alias)) {
            $translationParts[] = $alias . '.' . $propertyConditionVariable->getPropertyName();
        }
        else {
            $translationParts[] = $propertyConditionVariable->getPropertyName();
        }

        if ($propertyConditionVariable->getAlias()) {
            $translationParts[] = 'AS';
            $translationParts[] = $propertyConditionVariable->getAlias();
        }

        return implode(' ', $translationParts);
    }
}
