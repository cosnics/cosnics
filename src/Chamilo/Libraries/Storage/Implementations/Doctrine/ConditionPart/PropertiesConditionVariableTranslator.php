<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Architecture\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertiesConditionVariableTranslator extends ConditionVariableTranslator
{
    public const CONDITION_CLASS = PropertiesConditionVariable::class;

    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, PropertiesConditionVariable $propertiesConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $className = $propertiesConditionVariable->getDataClassName();

        if ($enableAliasing)
        {
            return $this->getStorageAliasGenerator()->getDataClassAlias($className) . '.*';
        }
        else
        {
            return '*';
        }
    }
}
