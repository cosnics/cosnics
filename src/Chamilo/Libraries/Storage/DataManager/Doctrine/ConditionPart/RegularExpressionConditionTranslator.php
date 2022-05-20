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
class RegularExpressionConditionTranslator extends ConditionTranslator
{

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\RegularExpressionCondition
     */
    public function getCondition(): ConditionPart
    {
        return parent::getCondition();
    }

    public function translate(?bool $enableAliasing = true): string
    {
        return $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $this->getCondition()->getConditionVariable(), $enableAliasing
            ) . ' REGEXP ' . $this->getDataClassDatabase()->quote($this->getCondition()->getRegularExpression());
    }
}
