<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * This class describes a storage unit you want to join with
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Join implements Hashable
{
    /**
     * **************************************************************************************************************
     * Join Types *
     * **************************************************************************************************************
     */
    const TYPE_NORMAL = 1;
    const TYPE_LEFT = 2;
    const TYPE_RIGHT = 3;

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */

    /**
     * The classname of the data_class you want to join with
     *
     * @var string
     */
    private $data_class;

    /**
     * The join type
     *
     * @var integer
     */
    private $type;

    /**
     * The join condition
     *
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    private $condition;

    /**
     *
     * @param string $data_class
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $type
     */
    public function __construct($data_class, Condition $condition = null, $type = self :: TYPE_NORMAL)
    {
        $this->set_data_class($data_class);
        $this->set_condition($condition);
        $this->set_type($type);
    }

    /**
     * Returns the data class name
     *
     * @return string
     */
    public function get_data_class()
    {
        return $this->data_class;
    }

    /**
     * Sets the data class name
     *
     * @param string $data_class
     */
    public function set_data_class($data_class)
    {
        $this->data_class = $data_class;
    }

    /**
     * Returns the condition
     *
     * @return \libraries\storage\Condition
     */
    public function get_condition()
    {
        return $this->condition;
    }

    /**
     * Sets the condition
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public function set_condition(Condition $condition)
    {
        $this->condition = $condition;
    }

    /**
     * Returns the type
     *
     * @return integer
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param integer $type
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @param string[] $hash_parts
     * @return string
     */
    public function hash($hash_parts = array())
    {
        $hash_parts[] = $this->data_class;
        $hash_parts[] = $this->condition->hash();
        $hash_parts[] = $this->type;

        return md5(serialize($hash_parts));
    }
}
