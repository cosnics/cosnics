<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class OperationConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate(bool $enableAliasing = true)
    {
        $strings = array();

        $strings[] = '(';
        $strings[] = $this->getConditionPartTranslatorService()->translate(
            $this->getDataClassDatabase(), $this->getConditionVariable()->get_left(), $enableAliasing
        );

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

        $strings[] = $this->getConditionPartTranslatorService()->translate(
            $this->getDataClassDatabase(), $this->getConditionVariable()->get_right(), $enableAliasing
        );
        $strings[] = ')';

        return implode(' ', $strings);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable
     */
    public function getConditionVariable()
    {
        return parent::getConditionVariable();
    }
}
