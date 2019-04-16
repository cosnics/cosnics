<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SubselectConditionTranslator extends ConditionTranslator
{

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate($enableAliasing = true)
    {
        $string = array();

        $string[] = $this->getConditionPartTranslatorService()->translate(
            $this->getDataClassDatabase(), $this->getCondition()->get_name(), $enableAliasing
        );

        $string[] = 'IN (';
        $string[] = 'SELECT';

        $string[] = $this->getConditionPartTranslatorService()->translate(
            $this->getDataClassDatabase(), $this->getCondition()->get_value(), $enableAliasing
        );

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
            $string[] = $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $this->getCondition()->get_condition(), $enableAliasing
            );
        }

        $string[] = ')';

        return implode(' ', $string);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\SubselectCondition
     */
    public function getCondition()
    {
        return parent::getCondition();
    }
}