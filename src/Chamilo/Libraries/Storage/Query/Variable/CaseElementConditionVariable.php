<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * A case element condition variable that describes a single element of a case in a select query
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseElementConditionVariable extends ConditionVariable
{

    /**
     * The condition used after the WHEN statement.
     * If empty the case element is an ELSE statement.
     *
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    private $condition;

    /**
     * The Statement
     *
     * @var string
     */
    private $statement;

    /**
     * Constructor
     *
     * @param string $statement
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public function __construct($statement, $condition)
    {
        $this->statement = $statement;
        $this->condition = $condition;
    }

    /**
     * Get the condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public function get_condition()
    {
        return $this->condition;
    }

    /**
     * Set the condition
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition $condition
     */
    public function set_condition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * Get the statement
     *
     * @return string
     */
    public function get_statement()
    {
        return $this->statement;
    }

    /**
     * Set the statement
     *
     * @param string $statement
     */
    public function set_statement($statement)
    {
        $this->statement = $statement;
    }

    /**
     * Get an md5 representation of this object for identification purposes
     *
     * @param string[] $hash_parts
     *
     * @return string
     */
    public function hash($hash_parts = array())
    {
        if (! $this->get_hash())
        {
            if ($this->condition instanceof Condition)
            {
                $hash_parts[] = $this->condition->hash();
            }

            $hash_parts[] = $this->statement;

            $this->set_hash(parent :: hash($hash_parts));
        }

        return $this->get_hash();
    }
}
