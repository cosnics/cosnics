<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionPart;
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
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    public function getCondition(): ConditionPart
    {
        return parent::getCondition();
    }

    public function translate(?bool $enableAliasing = true): string
    {
        if (is_null($this->getCondition()->getRightConditionVariable()))
        {
            return $this->getConditionPartTranslatorService()->translate(
                    $this->getDataClassDatabase(), $this->getCondition()->getLeftConditionVariable(), $enableAliasing
                ) . ' IS NULL';
        }

        return $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $this->getCondition()->getLeftConditionVariable(), $enableAliasing
            ) . ' = ' . $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $this->getCondition()->getRightConditionVariable(), $enableAliasing
            );
    }
}
