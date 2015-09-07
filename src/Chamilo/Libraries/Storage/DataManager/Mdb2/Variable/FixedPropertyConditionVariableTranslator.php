<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Variable;

use Chamilo\Libraries\Storage\DataManager\Mdb2\Database;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FixedPropertyConditionVariableTranslator extends ConditionVariableTranslator
{

    public function translate()
    {
        $class_name = $this->get_condition_variable()->get_class();

        $data_manager = $class_name . '\\DataManager';

        $table_alias = $data_manager :: get_instance()->get_alias($class_name :: get_table_name());

        return Database :: escape_column_name($this->get_condition_variable()->get_property(), $table_alias) . ' AS ' .
             $this->get_condition_variable()->get_alias();
    }
}
