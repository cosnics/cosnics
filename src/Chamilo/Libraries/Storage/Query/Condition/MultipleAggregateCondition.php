<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

/**
 * This class represents a condition that consists of multiple aggregated conditions.
 * Thus, it is used to model a single
 * relationship (AND, OR and perhaps others) between its aggregated conditions.
 * 
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package common.libraries
 */
abstract class MultipleAggregateCondition extends AggregateCondition
{

    /**
     * The aggregated conditions
     * 
     * @var Condition[]
     */
    private $conditions;

    /**
     * Constructor
     * 
     * @param Condition[] $conditions
     */
    public function __construct($conditions)
    {
        $this->conditions = (is_array($conditions) ? $conditions : func_get_args());
    }

    /**
     * Gets the aggregated conditions
     * 
     * @return Condition[]
     */
    public function get_conditions()
    {
        return $this->conditions;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Condition\Condition::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent::getHashParts();
        
        $hashParts[] = $this->get_operator();
        
        $aggregateParts = array();
        
        foreach ($this->get_conditions() as $condition)
        {
            $aggregateParts[] = $condition->getHashParts();
        }
        
        sort($aggregateParts);
        
        $hashParts[] = $aggregateParts;
        
        return $hashParts;
    }

    /**
     * Gets the operator of this MultipleAggregateCondition
     * 
     * @return string
     */
    abstract public function get_operator();
}
