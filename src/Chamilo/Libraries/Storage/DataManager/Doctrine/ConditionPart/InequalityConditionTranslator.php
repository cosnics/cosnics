<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class InequalityConditionTranslator extends ConditionTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        switch ($this->getCondition()->get_operator())
        {
            case ComparisonCondition::GREATER_THAN :
                $operator = '>';
                break;
            case ComparisonCondition::GREATER_THAN_OR_EQUAL :
                $operator = '>=';
                break;
            case ComparisonCondition::LESS_THAN :
                $operator = '<';
                break;
            case ComparisonCondition::LESS_THAN_OR_EQUAL :
                $operator = '<=';
                break;
            default :
                die('Unknown operator for inequality condition');
        }

        return $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $this->getCondition()->get_name()) . ' ' . $operator . ' ' . $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $this->getCondition()->get_value());
    }
}