<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DistinctConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        $distinctConditionVariable = $this->getDistinctConditionVariable();

        if (! $distinctConditionVariable->hasConditionVariables())
        {
            throw new \Exception('A DistinctConditionVariable needs to have one or more ConditionVariables');
        }

        $strings = array();

        $strings[] = 'DISTINCT';

        $distinctStrings = array();

        foreach ($distinctConditionVariable->getConditionVariables() as $conditionVariable)
        {
            $distinctStrings[] = $this->getConditionPartTranslatorService()->translateConditionPart(
                $this->getDataClassDatabase(),
                $conditionVariable);
        }

        $strings[] = implode(', ', $distinctStrings);

        return implode(' ', $strings);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\DistinctConditionVariable
     */
    public function getDistinctConditionVariable()
    {
        return $this->getConditionPart();
    }
}
