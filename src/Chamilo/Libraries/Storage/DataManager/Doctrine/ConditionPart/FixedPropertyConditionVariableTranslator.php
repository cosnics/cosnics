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
     *
     * @see \Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\PropertyConditionVariableTranslator::translate()
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
