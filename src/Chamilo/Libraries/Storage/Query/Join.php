<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
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
    use HashableTrait;

    const TYPE_LEFT = 2;
    const TYPE_NORMAL = 1;
    const TYPE_RIGHT = 3;

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
     * @param string $dataClass
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $type
     */
    public function __construct($dataClass, Condition $condition = null, $type = self::TYPE_NORMAL)
    {
        $this->set_data_class($dataClass);
        $this->set_condition($condition);
        $this->set_type($type);
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\Hashable::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = array();

        $hashParts[] = $this->get_data_class();
        $hashParts[] = $this->get_condition()->getHashParts();
        $hashParts[] = $this->get_type();

        return $hashParts;
    }

    /**
     * Returns the condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
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
    public function set_condition(Condition $condition = null)
    {
        $this->condition = $condition;
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
     * @param string $dataClass
     */
    public function set_data_class($dataClass)
    {
        $this->data_class = $dataClass;
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
}
