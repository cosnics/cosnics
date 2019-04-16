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
     * @param boolean $enableAliasing
     *
     * @return string
     * @throws \Exception
     */
    public function translate(bool $enableAliasing = true)
    {
        $distinctConditionVariable = $this->getConditionVariable();

        if (!$distinctConditionVariable->hasConditionVariables())
        {
            throw new \Exception('A DistinctConditionVariable needs to have one or more ConditionVariables');
        }

        $strings = array();

        $strings[] = 'DISTINCT';

        $distinctStrings = array();

        foreach ($distinctConditionVariable->getConditionVariables() as $conditionVariable)
        {
            $distinctStrings[] = $this->getConditionPartTranslatorService()->translate(
                $this->getDataClassDatabase(), $conditionVariable, $enableAliasing
            );
        }

        $strings[] = implode(', ', $distinctStrings);

        return implode(' ', $strings);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\DistinctConditionVariable
     */
    public function getConditionVariable()
    {
        return parent::getConditionVariable();
    }
}
