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
     * @var multitype:\common\libraries\storage\Condition
     */
    private $conditions;

    /**
     * Constructor
     * 
     * @param $conditions multitype:\common\libraries\storage\Condition
     */
    public function __construct($conditions)
    {
        $this->conditions = (is_array($conditions) ? $conditions : func_get_args());
    }

    /**
     * Gets the aggregated conditions
     * 
     * @return multitype:\common\libraries\storage\Condition
     */
    public function get_conditions()
    {
        return $this->conditions;
    }
    
    /*
     * (non-PHPdoc) @see common\libraries.Condition::hash()
     */
    public function hash()
    {
        if (! $this->get_hash())
        {
            $hashes = array();
            
            $hashes[] = $this->get_operator();
            foreach ($this->conditions as $condition)
            {
                $hashes[] = $condition->hash();
            }
            
            sort($hashes);
            
            $this->set_hash(parent :: hash($hashes));
        }
        
        return $this->get_hash();
    }

    /**
     * Gets the operator of this MultipleAggregateCondition
     * 
     * @return string
     */
    abstract public function get_operator();
}
