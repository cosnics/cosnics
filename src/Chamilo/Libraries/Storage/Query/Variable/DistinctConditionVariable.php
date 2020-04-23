<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DistinctConditionVariable extends ConditionVariable
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    private $conditionVariables;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[] $conditionVariables
     */
    public function __construct($conditionVariables)
    {
        $this->conditionVariables = $conditionVariables;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     */
    public function addConditionVariable($conditionVariable)
    {
        $this->conditionVariables[] = $conditionVariable;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    public function getConditionVariables()
    {
        return $this->conditionVariables;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[] $conditionVariables
     */
    public function setConditionVariables($conditionVariables)
    {
        $this->conditionVariables = $conditionVariables;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = ConditionVariable::getHashParts();

        $variableParts = array();

        foreach ($this->getConditionVariables() as $conditionVariable)
        {
            $variableParts[] = $conditionVariable->getHashParts();
        }

        $hashParts[] = $variableParts;

        return $hashParts;
    }

    public function hasConditionVariables()
    {
        return count($this->getConditionVariables()) > 0;
    }
}
