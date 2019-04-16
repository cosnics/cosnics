<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class AggregateConditionTranslator extends ConditionTranslator
{

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate($enableAliasing = true)
    {
        $string = '';

        $conditionTranslations = array();
        $count = 0;

        foreach ($this->getCondition()->get_conditions() as $key => $condition)
        {
            $count ++;
            $translation = $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $condition, $enableAliasing
            );

            if (!empty($translation))
            {
                $conditionTranslations[] = $translation;
            }
        }

        if (count($conditionTranslations) > 0)
        {
            $string = '(' . implode($this->getCondition()->get_operator(), $conditionTranslations) . ')';
        }

        return $string;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\MultipleAggregateCondition
     */
    public function getCondition()
    {
        return parent::getCondition();
    }
}
