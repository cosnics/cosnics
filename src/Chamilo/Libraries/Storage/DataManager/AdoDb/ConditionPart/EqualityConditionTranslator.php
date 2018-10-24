<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
            return $this->getConditionPartTranslatorService()->translateConditionPart(
                $this->getDataClassDatabase(),
                $this->getCondition()->get_name()) . ' IS NULL';
        }

        return $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $this->getCondition()->get_name()) . ' = ' . $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $this->getCondition()->get_value());
    }
}
