<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NotConditionTranslator extends ConditionTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        $string = array();

        $string[] = 'NOT (';
        $string[] = $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $this->getCondition()->get_condition());
        $string[] = ')';

        return implode('', $string);
    }
}
