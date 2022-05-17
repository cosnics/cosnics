<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertyConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    public function getConditionVariable()
    {
        return parent::getConditionVariable();
    }

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate(bool $enableAliasing = true)
    {
        $className = $this->getConditionVariable()->get_class();

        if ($enableAliasing)
        {
            $alias = $this->getDataClassDatabase()->getAlias($className::getTableName());
        }
        else
        {
            $alias = null;
        }

        return $this->getDataClassDatabase()->escapeColumnName(
            $this->getConditionVariable()->get_property(), $alias
        );
    }
}
