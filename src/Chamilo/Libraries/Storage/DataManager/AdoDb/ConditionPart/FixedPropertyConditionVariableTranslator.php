<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FixedPropertyConditionVariableTranslator extends PropertyConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart\PropertyConditionVariableTranslator::translate()
     */
    public function translate()
    {
        $class_name = $this->getConditionVariable()->get_class();

        $table_alias = $this->getDataClassDatabase()->getAlias($class_name::get_table_name());

        return $this->getDataClassDatabase()->escapeColumnName(
            $this->getConditionVariable()->get_property(),
            $table_alias) . ' AS ' . $this->getConditionVariable()->get_alias();
    }
}
