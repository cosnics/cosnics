<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OperationConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        $strings = array();

        $strings[] = '(';
        $strings[] = $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $this->getConditionVariable()->get_left());

        switch ($this->getConditionVariable()->get_operator())
        {
            case OperationConditionVariable::ADDITION :
                $strings[] = '+';
                break;
            case OperationConditionVariable::DIVISION :
                $strings[] = '/';
                break;
            case OperationConditionVariable::MINUS :
                $strings[] = '-';
                break;
            case OperationConditionVariable::MULTIPLICATION :
                $strings[] = '*';
                break;
            case OperationConditionVariable::BITWISE_AND :
                $strings[] = '&';
                break;
            case OperationConditionVariable::BITWISE_OR :
                $strings[] = '|';
                break;
        }

        $strings[] = $this->getConditionPartTranslatorService()->translateConditionPart(
            $this->getDataClassDatabase(),
            $this->getConditionVariable()->get_right());
        $strings[] = ')';

        return implode(' ', $strings);
    }
}
