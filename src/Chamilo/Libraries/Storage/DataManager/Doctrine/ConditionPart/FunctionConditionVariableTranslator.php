<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FunctionConditionVariableTranslator extends ConditionVariableTranslator
{

    public function translate(
        ConditionPartTranslatorService $conditionPartTranslatorService, DataClassDatabaseInterface $dataClassDatabase,
        FunctionConditionVariable $functionConditionVariable, ?bool $enableAliasing = true
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

        $strings[] = $conditionPartTranslatorService->translate(
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
