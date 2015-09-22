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

    /**
     *
     * @var string
     */
    private $hash;

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
     */
    public function __construct($condition = null, Joins $joins = null)
    {
        $this->condition = $condition;
        $this->joins = $joins;
    }

    /**
     *
     * @return string
     */
    function get_hash()
    {
        return $this->hash;
    }

    /**
     *
     * @param string $hash
     */
    function set_hash($hash)
    {
        $this->hash = $hash;
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

    /**
     *
     * @param string[] $hash_parts
     * @return string
     */
    public function hash($hash_parts = array())
    {
        $hash_parts[] = self :: class_name();
        $hash_parts[] = ($this->get_condition() instanceof Condition ? $this->get_condition()->hash() : null);
        $hash_parts[] = ($this->get_joins() instanceof Joins ? $this->get_joins()->hash() : null);

        return md5(json_encode($hash_parts));
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
