<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Condition;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Variable\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Replaced by the ConditionPartTranslators and related service and factory
 */
class SubselectConditionTranslator extends ConditionTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Condition\ConditionTranslator::translate()
     */
    public function translate()
    {
        $string = array();
        
        $string[] = ConditionVariableTranslator::render($this->get_condition()->get_name());
        
        $string[] = 'IN (';
        $string[] = 'SELECT';
        
        $string[] = ConditionVariableTranslator::render($this->get_condition()->get_value());
        
        $string[] = 'FROM';
        
        $class = $this->get_condition()->get_value()->get_class();
        $table = $class::get_table_name();
        
        $alias = DataManager::get_alias($table);
        
        $string[] = $table;
        
        $string[] = 'AS';
        $string[] = $alias;
        
        if ($this->get_condition()->get_condition())
        {
            $string[] = 'WHERE ';
            $string[] = ConditionTranslator::render($this->get_condition()->get_condition(), $alias);
        }
        
        $string[] = ')';
        
        return implode(' ', $string);
    }
}