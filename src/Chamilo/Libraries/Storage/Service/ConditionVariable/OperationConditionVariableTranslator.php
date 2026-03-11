<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Enum\OperationTypeEnum;
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
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
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
            case OperationTypeEnum::ADDITION :
                $strings[] = '+';
                break;
            case OperationTypeEnum::DIVISION :
                $strings[] = '/';
                break;
            case OperationTypeEnum::MINUS :
                $strings[] = '-';
                break;
            case OperationTypeEnum::MULTIPLICATION :
                $strings[] = '*';
                break;
            case OperationTypeEnum::BITWISE_AND :
                $strings[] = '&';
                break;
            case OperationTypeEnum::BITWISE_OR :
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
