<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DistinctConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\DistinctConditionVariable
     */
    public function getConditionVariable(): ConditionPart
    {
        return parent::getConditionVariable();
    }

    public function translate(?bool $enableAliasing = true): string
    {
        $distinctConditionVariable = $this->getConditionVariable();

        if (!$distinctConditionVariable->hasConditionVariables())
        {
            throw new Exception('A DistinctConditionVariable needs to have one or more ConditionVariables');
        }

        $strings = [];

        $strings[] = 'DISTINCT';

        $distinctStrings = [];

        foreach ($distinctConditionVariable->getConditionVariables() as $conditionVariable)
        {
            $distinctStrings[] = $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $conditionVariable, $enableAliasing
            );
        }

        $strings[] = implode(', ', $distinctStrings);

        return implode(' ', $strings);
    }
}
