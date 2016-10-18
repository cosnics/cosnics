<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

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
     * @see \Chamilo\Libraries\Storage\DataManager\Doctrine\Variable\PropertyConditionVariableTranslator::translate()
     */
    public function translate()
    {
        $class_name = $this->getConditionVariable()->get_class();

        $table_alias = \Chamilo\Libraries\Storage\DataManager\DataManager::get_instance()->get_alias(
            $class_name::get_table_name());

        return Database::escape_column_name($this->getConditionVariable()->get_property(), $table_alias) . ' AS ' .
             $this->getConditionVariable()->get_alias();
    }
}
