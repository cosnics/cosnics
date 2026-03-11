<?php
namespace Chamilo\Libraries\Storage\Service\ConditionVariable;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\DistinctConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariableTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\ConditionVariable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DistinctConditionVariableTranslator extends ConditionVariableTranslator
    implements ConditionVariableTranslatorInterface
{
    public function getConditionVariableClassName(): string
    {
        return DistinctConditionVariable::class;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        QueryBuilder $querybuilder, DistinctConditionVariable $distinctConditionVariable, ?bool $enableAliasing = true
    ): string
    {
        $strings = [];

        $strings[] = 'DISTINCT';

        $distinctStrings = [];

        if ($distinctConditionVariable->hasConditionVariables()) {
            foreach ($distinctConditionVariable->get() as $conditionVariable) {
                $distinctStrings[] = $this->getConditionVariableTranslatorCollection()->translate(
                    $querybuilder, $conditionVariable, $enableAliasing
                );
            }
        }
        else {
            $strings[] = '*';
        }

        $strings[] = implode(', ', $distinctStrings);

        return implode(' ', $strings);
    }
}
