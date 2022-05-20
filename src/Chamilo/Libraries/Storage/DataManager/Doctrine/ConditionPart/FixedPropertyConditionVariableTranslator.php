<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionPart;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FixedPropertyConditionVariableTranslator extends PropertyConditionVariableTranslator
{

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable
     */
    public function getConditionVariable(): ConditionPart
    {
        return parent::getConditionVariable();
    }

    /**
     * @throws \ReflectionException
     */
    public function translate(?bool $enableAliasing = true): string
    {
        $className = $this->getConditionVariable()->getDataClassName();

        if ($enableAliasing)
        {
            $tableAlias = $this->getDataClassDatabase()->getAlias($className::getTableName());
        }
        else
        {
            $tableAlias = null;
        }

        return $this->getDataClassDatabase()->escapeColumnName(
                $this->getConditionVariable()->get_property(), $tableAlias
            ) . ' AS ' . $this->getConditionVariable()->getAlias();
    }
}
