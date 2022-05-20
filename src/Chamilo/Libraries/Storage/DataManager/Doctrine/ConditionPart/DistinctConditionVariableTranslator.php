<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Variable\DistinctConditionVariable;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DistinctConditionVariableTranslator extends ConditionVariableTranslator
{
    public function translate(
        ConditionPartTranslatorService $conditionPartTranslatorService, DataClassDatabaseInterface $dataClassDatabase,
        DistinctConditionVariable $distinctConditionVariable, ?bool $enableAliasing = true
    ): string
    {
        if (!$distinctConditionVariable->hasConditionVariables())
        {
            throw new Exception('A DistinctConditionVariable needs to have one or more ConditionVariables');
        }

        $strings = [];

        $strings[] = 'DISTINCT';

        $distinctStrings = [];

        foreach ($distinctConditionVariable->getConditionVariables() as $conditionVariable)
        {
            $distinctStrings[] = $conditionPartTranslatorService->translate(
                $dataClassDatabase, $conditionVariable, $enableAliasing
            );
        }

        $strings[] = implode(', ', $distinctStrings);

        return implode(' ', $strings);
    }
}
