<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RegularExpressionConditionTranslator extends ConditionTranslator
{

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
        return $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $this->getCondition()->getConditionVariable(), $enableAliasing
            ) . ' REGEXP ' .
            $this->getDataClassDatabase()->quote($this->getCondition()->getRegularExpression());
    }
}
