<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

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
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate(bool $enableAliasing = true)
    {
        $className = $this->getConditionVariable()->get_class();

        if ($enableAliasing)
        {
            $tableAlias = $this->getDataClassDatabase()->getAlias($className::get_table_name());
        }
        else
        {
            $tableAlias = null;
        }

        return $this->getDataClassDatabase()->escapeColumnName(
                $this->getConditionVariable()->get_property(), $tableAlias
            ) . ' AS ' . $this->getConditionVariable()->get_alias();
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable
     */
    public function getConditionVariable()
    {
        return parent::getConditionVariable();
    }
}
