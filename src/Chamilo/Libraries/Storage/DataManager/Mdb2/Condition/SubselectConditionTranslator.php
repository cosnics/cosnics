<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\Condition;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\DataManager\Mdb2\Variable\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubselectConditionTranslator extends ConditionTranslator
{

    public function translate()
    {
        $string = array();

        $string[] = ConditionVariableTranslator :: render($this->get_condition()->get_name());

        $string[] = 'IN (';

        $string[] = 'SELECT';

        $string[] = ConditionVariableTranslator :: render($this->get_condition()->get_value());

        $string[] = 'FROM';

        $class = $this->get_condition()->get_value()->get_class();
        $table = $class :: get_table_name();

        $namespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname($class);
        $datamanager_class = $namespace . '\\DataManager';
        $alias = $datamanager_class :: get_instance()->get_alias($table);

        $string[] = $table;

        $string[] = 'AS';
        $string[] = $alias;

        if ($this->get_condition()->get_condition())
        {
            $string[] = 'WHERE ';
            $string[] = ConditionTranslator :: render(
                $this->get_condition()->get_condition(),
                $alias);
        }

        $string[] = ')';

        return implode(' ', $string);
    }
}
