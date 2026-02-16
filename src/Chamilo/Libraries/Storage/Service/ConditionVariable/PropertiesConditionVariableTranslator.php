<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariableTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

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
        QueryBuilder $querybuilder, PropertiesConditionVariable $propertiesConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $className = $propertiesConditionVariable->getDataClassName();

        if ($enableAliasing) {
            return $this->getStorageAliasGenerator()->getDataClassAlias($className) . '.*';
        }
        else {
            return '*';
        }
    }
}
