<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Variable\DistinctConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DistinctConditionVariableTranslator extends ConditionVariableTranslator
{
    public const CONDITION_CLASS = DistinctConditionVariable::class;

    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, DistinctConditionVariable $distinctConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        $strings[] = 'DISTINCT';

        $distinctStrings = [];

        if ($distinctConditionVariable->hasConditionVariables())
        {
            foreach ($distinctConditionVariable->get() as $conditionVariable)
            {
                $distinctStrings[] = $this->getConditionPartTranslatorService()->translate(
                    $dataClassDatabase, $conditionVariable, $enableAliasing
                );
            }
        }
        else
        {
            $strings[] = '*';
        }

        $strings[] = implode(', ', $distinctStrings);

        return implode(' ', $strings);
    }
}
