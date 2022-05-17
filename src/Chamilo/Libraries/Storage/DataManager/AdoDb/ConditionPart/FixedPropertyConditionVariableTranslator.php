<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FixedPropertyConditionVariableTranslator extends PropertyConditionVariableTranslator
{

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable
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
            $tableAlias = $this->getDataClassDatabase()->getAlias($className::getTableName());
        }
        else
        {
            $tableAlias = null;
        }

        return $this->getDataClassDatabase()->escapeColumnName(
                $this->getConditionVariable()->get_property(), $tableAlias
            ) . ' AS ' . $this->getConditionVariable()->get_alias();
    }
}
