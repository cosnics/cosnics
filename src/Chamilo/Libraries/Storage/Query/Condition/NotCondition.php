<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

/**
 * This type of aggregate condition negates a single condition, thus requiring that that condition not be met.
 * 
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package common.libraries
 */
class NotCondition extends AggregateCondition
{

    /**
     * The condition to negate
     * 
     * @var \libraries\storage\Condition
     */
    private $condition;

    /**
     * Constructor
     * 
     * @param $condition \libraries\storage\Condition
     */
    public function __construct(Condition $condition)
    {
        $this->condition = $condition;
    }

    /**
     * Gets the condition to negate
     * 
     * @return \libraries\storage\Condition
     */
    public function get_condition()
    {
        return $this->condition;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Condition\Condition::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent::getHashParts();
        
        $hashParts[] = $this->get_condition()->getHashParts();
        
        return $hashParts;
    }
}
