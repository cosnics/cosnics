<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubselectConditionTranslator extends ConditionTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        $string = array();

        $string[] = $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $this->getCondition()->get_name());

        $string[] = 'IN (';
        $string[] = 'SELECT';

        $string[] = $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $this->getCondition()->get_value());

        $string[] = 'FROM';

        $class = $this->getCondition()->get_value()->get_class();
        $table = $class::get_table_name();

        $alias = $this->getDataClassDatabase()->getAlias($table);

        $string[] = $table;

        $string[] = 'AS';
        $string[] = $alias;

        if ($this->getCondition()->get_condition())
        {
            $string[] = 'WHERE ';
            $string[] = $this->getConditionPartTranslatorService()->translateConditionPart(
                $this->getDataClassDatabase(),
                $this->getCondition()->get_condition(),
                $alias);
        }

        $string[] = ')';

        return implode(' ', $string);
    }
}