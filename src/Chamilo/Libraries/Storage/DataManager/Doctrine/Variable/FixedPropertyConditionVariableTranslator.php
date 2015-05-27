<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Variable;

use Chamilo\Libraries\Storage\Cache\ConditionVariableCache;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Variable
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
        if (! ConditionVariableCache :: exists($this->get_condition_variable()))
        {
            $class_name = $this->get_condition_variable()->get_class();

            $table_alias = \Chamilo\Libraries\Storage\DataManager\DataManager :: get_instance()->get_alias(
                $class_name :: get_table_name());

            $value = Database :: escape_column_name($this->get_condition_variable()->get_property(), $table_alias) .
                 ' AS ' . $this->get_condition_variable()->get_alias();

            ConditionVariableCache :: set_cache($this->get_condition_variable(), $value);
        }

        return ConditionVariableCache :: get($this->get_condition_variable());
    }
}
