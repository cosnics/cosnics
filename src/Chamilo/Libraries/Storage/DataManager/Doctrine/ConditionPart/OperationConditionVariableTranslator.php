<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class OperationConditionVariableTranslator extends ConditionVariableTranslator
{
    public const CONDITION_CLASS = OperationConditionVariable::class;

    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, OperationConditionVariable $operationConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        $strings[] = '(';
        $strings[] = $this->getConditionPartTranslatorService()->translate(
            $dataClassDatabase, $operationConditionVariable->getLeftConditionVariable(), $enableAliasing
        );

        switch ($operationConditionVariable->getOperator())
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
            $dataClassDatabase, $operationConditionVariable->getRightConditionVariable(), $enableAliasing
        );
        $strings[] = ')';

        return implode(' ', $strings);
    }
}
