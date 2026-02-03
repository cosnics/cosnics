<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariableTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\ConditionVariable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class OperationConditionVariableTranslator extends ConditionVariableTranslator
    implements ConditionVariableTranslatorInterface
{
    public function getConditionVariableClassName(): string
    {
        return OperationConditionVariable::class;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function translate(
        QueryBuilder $querybuilder, OperationConditionVariable $operationConditionVariable, ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        $strings[] = '(';
        $strings[] = $this->getConditionVariableTranslatorCollection()->translate(
            $querybuilder, $operationConditionVariable->getLeftConditionVariable(), $enableAliasing
        );

        switch ($operationConditionVariable->getOperator()) {
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

        $strings[] = $this->getConditionVariableTranslatorCollection()->translate(
            $querybuilder, $operationConditionVariable->getRightConditionVariable(), $enableAliasing
        );
        $strings[] = ')';

        return implode(' ', $strings);
    }
}
