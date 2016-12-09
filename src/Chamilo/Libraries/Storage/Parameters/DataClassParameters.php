<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DataClassParameters implements Hashable
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;
    use \Chamilo\Libraries\Architecture\Traits\HashableTrait;

    /**
     * The condition to be applied to the action
     *
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    private $condition;

    /**
     * The joins to be applied to the action
     *
     * @var \Chamilo\Libraries\Storage\Query\Joins
     */
    private $joins;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     *
     * @throws \Exception
     */
    public function __construct($condition = null, Joins $joins = null)
    {
        if(!is_null($condition) && !$condition instanceof Condition)
        {
            throw new \Exception(
                sprintf(
                    'The given parameter $condition should be of type ' .
                    '\Chamilo\Libraries\Storage\Query\Condition\Condition but an object of type %s was given',
                    gettype($condition)
                )
            );
        }

        if(!is_null($joins) && !$joins instanceof Joins)
        {
            throw new \Exception(
                sprintf(
                    'The given parameter $joins should be of type ' .
                    '\Chamilo\Libraries\Storage\Query\Joins but an object of type %s was given',
                    gettype($joins)
                )
            );
        }

        $this->condition = $condition;
        $this->joins = $joins;
    }

    /**
     * Get the condition to be applied to the action
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_condition()
    {
        return $this->condition;
    }

    /**
     * Set the condition to be applied to the action
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public function set_condition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * Get the join data classes to be applied to the action
     *
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    public function get_joins()
    {
        return $this->joins;
    }

    /**
     * Set the join data classes to be applied to the action
     *
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     */
    public function set_joins($joins)
    {
        $this->joins = $joins;
    }

    public function getHashParts()
    {
        $hashParts = array();

        $hashParts[] = static :: class_name();
        $hashParts[] = ($this->get_condition() instanceof Condition ? $this->get_condition()->getHashParts() : null);
        $hashParts[] = ($this->get_joins() instanceof Joins ? $this->get_joins()->getHashParts() : null);

        return $hashParts;
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static :: context();
    }
}
