<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class EqualityConditionTranslator extends ConditionTranslator
{

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate(bool $enableAliasing = true)
    {
        if (is_null($this->getCondition()->get_value()))
        {
            return $this->getConditionPartTranslatorService()->translate(
                    $this->getDataClassDatabase(), $this->getCondition()->get_name(), $enableAliasing
                ) . ' IS NULL';
        }

        return $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $this->getCondition()->get_name(), $enableAliasing
            ) . ' = ' . $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $this->getCondition()->get_value(), $enableAliasing
            );
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    public function getCondition()
    {
        return parent::getCondition();
    }
}
