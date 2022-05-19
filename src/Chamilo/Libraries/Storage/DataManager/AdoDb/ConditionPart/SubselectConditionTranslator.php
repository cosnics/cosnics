<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SubselectConditionTranslator extends ConditionTranslator
{

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\SubselectCondition
     */
    public function getCondition()
    {
        return parent::getCondition();
    }

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate(bool $enableAliasing = true)
    {
        $string = [];

        $string[] = $this->getConditionPartTranslatorService()->translate(
            $this->getDataClassDatabase(), $this->getCondition()->getConditionVariable(), $enableAliasing
        );

        $string[] = 'IN (';
        $string[] = 'SELECT';

        $string[] = $this->getConditionPartTranslatorService()->translate(
            $this->getDataClassDatabase(), $this->getCondition()->getSubselectConditionVariable(), $enableAliasing
        );

        $string[] = 'FROM';

        $class = $this->getCondition()->getSubselectConditionVariable()->get_class();
        $table = $class::getTableName();

        $alias = $this->getDataClassDatabase()->getAlias($table);

        $string[] = $table;

        $string[] = 'AS';
        $string[] = $alias;

        if ($this->getCondition()->getCondition())
        {
            $string[] = 'WHERE ';
            $string[] = $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $this->getCondition()->getCondition(), $enableAliasing
            );
        }

        $string[] = ')';

        return implode(' ', $string);
    }
}