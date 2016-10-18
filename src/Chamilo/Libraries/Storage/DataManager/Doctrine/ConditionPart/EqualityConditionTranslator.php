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
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        if (is_null($this->getCondition()->get_value()))
        {
            return $this->getConditionPartTranslatorService()->translateConditionPart($this->getCondition()->get_name()) .
                 ' IS NULL';
        }

        return $this->getConditionPartTranslatorService()->translateConditionPart($this->getCondition()->get_name()) .
             ' = ' . $this->getConditionPartTranslatorService()->translateConditionPart(
                $this->getCondition()->get_value());
    }
}
