<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertiesConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable
     */
    public function getConditionVariable(): ConditionPart
    {
        return parent::getConditionVariable();
    }

    public function translate(?bool $enableAliasing = true): string
    {
        $className = $this->getConditionVariable()->getDataClassName();

        if ($enableAliasing)
        {
            return $this->getDataClassDatabase()->getAlias($className::getTableName()) . '.*';
        }
        else
        {
            return '*';
        }
    }
}
