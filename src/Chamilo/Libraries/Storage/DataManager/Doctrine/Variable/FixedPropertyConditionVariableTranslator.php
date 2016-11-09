<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Variable;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Replaced by the ConditionPartTranslators and related service and factory
 */
class FixedPropertyConditionVariableTranslator extends PropertyConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\Doctrine\Variable\PropertyConditionVariableTranslator::translate()
     */
    public function translate()
    {
        $class_name = $this->get_condition_variable()->get_class();

        $table_alias = \Chamilo\Libraries\Storage\DataManager\DataManager::getInstance()->get_alias(
            $class_name::get_table_name());

        return Database::escape_column_name($this->get_condition_variable()->get_property(), $table_alias) . ' AS ' .
             $this->get_condition_variable()->get_alias();
    }
}
