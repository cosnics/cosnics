<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Enum\FunctionTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariableTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\ConditionVariable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class FunctionConditionVariableTranslator extends ConditionVariableTranslator
    implements ConditionVariableTranslatorInterface
{
    public function getConditionVariableClassName(): string
    {
        return FunctionConditionVariable::class;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        QueryBuilder $querybuilder, FunctionConditionVariable $functionConditionVariable, ?bool $enableAliasing = true
    ): string
    {
        $strings = [];
        switch ($functionConditionVariable->getFunction()) {
            case FunctionTypeEnum::SUM :
                $strings[] = 'SUM';
                break;
            case FunctionTypeEnum::COUNT :
                $strings[] = 'COUNT';
                break;
            case FunctionTypeEnum::MIN :
                $strings[] = 'MIN';
                break;
            case FunctionTypeEnum::MAX :
                $strings[] = 'MAX';
                break;
            case FunctionTypeEnum::DISTINCT :
                $strings[] = 'DISTINCT';
                break;
            case FunctionTypeEnum::AVERAGE :
                $strings[] = 'AVG';
                break;
        }

        if ($functionConditionVariable->getFunction() !== FunctionTypeEnum::DISTINCT) {
            $strings[] = '(';
        }
        else {
            $strings[] = ' ';
        }

        $strings[] = $this->getConditionVariableTranslatorCollection()->translate(
            $querybuilder, $functionConditionVariable->getConditionVariable(), $enableAliasing
        );

        if ($functionConditionVariable->getFunction() !== FunctionTypeEnum::DISTINCT) {
            $strings[] = ')';
        }

        if ($functionConditionVariable->getAlias()) {
            $value = implode('', $strings) . ' AS ' . $functionConditionVariable->getAlias();
        }
        else {
            $value = implode('', $strings);
        }

        return $value;
    }
}
