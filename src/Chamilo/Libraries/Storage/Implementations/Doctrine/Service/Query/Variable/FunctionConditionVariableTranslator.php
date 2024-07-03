<?php
namespace Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable;

use Chamilo\Libraries\Storage\Architecture\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class FunctionConditionVariableTranslator extends ConditionVariableTranslator
{
    public const CONDITION_CLASS = FunctionConditionVariable::class;

    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, FunctionConditionVariable $functionConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $strings = [];
        switch ($functionConditionVariable->getFunction())
        {
            case FunctionConditionVariable::SUM :
                $strings[] = 'SUM';
                break;
            case FunctionConditionVariable::COUNT :
                $strings[] = 'COUNT';
                break;
            case FunctionConditionVariable::MIN :
                $strings[] = 'MIN';
                break;
            case FunctionConditionVariable::MAX :
                $strings[] = 'MAX';
                break;
            case FunctionConditionVariable::DISTINCT :
                $strings[] = 'DISTINCT';
                break;
            case FunctionConditionVariable::AVERAGE :
                $strings[] = 'AVG';
                break;
        }

        if ($functionConditionVariable->getFunction() !== FunctionConditionVariable::DISTINCT)
        {
            $strings[] = '(';
        }
        else
        {
            $strings[] = ' ';
        }

        $strings[] = $this->getConditionPartTranslatorService()->translate(
            $dataClassDatabase, $functionConditionVariable->getConditionVariable(), $enableAliasing
        );

        if ($functionConditionVariable->getFunction() !== FunctionConditionVariable::DISTINCT)
        {
            $strings[] = ')';
        }

        if ($functionConditionVariable->getAlias())
        {
            $value = implode('', $strings) . ' AS ' . $functionConditionVariable->getAlias();
        }
        else
        {
            $value = implode('', $strings);
        }

        return $value;
    }
}
