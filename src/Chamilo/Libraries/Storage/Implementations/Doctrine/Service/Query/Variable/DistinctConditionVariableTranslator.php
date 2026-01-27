<?php
namespace Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable;

use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Variable\DistinctConditionVariable;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DistinctConditionVariableTranslator extends ConditionVariableTranslator
{
    public const CONDITION_CLASS = DistinctConditionVariable::class;

    public function translate(
        QueryBuilder $querybuilder, DistinctConditionVariable $distinctConditionVariable, ?bool $enableAliasing = true
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
                    $querybuilder, $conditionVariable, $enableAliasing
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
