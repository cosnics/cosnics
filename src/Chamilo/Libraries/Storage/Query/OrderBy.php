<?php
namespace Chamilo\Libraries\Storage\Query;

/**
 * Describes the order by functionality of a query.
 * Uses ConditionVariable to define the property
 * 
 * @package Chamilo\Libraries\Storage\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class OrderBy
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    private $property;

    /**
     *
     * @var integer
     */
    private $direction;

    /**
     * Constructor
     * 
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $property
     * @param integer $direction
     */
    public function __construct($property, $direction = SORT_ASC)
    {
        $this->property = $property;
        $this->direction = $direction;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    public function get_property()
    {
        return $this->property;
    }

    /**
     *
     * @return integer
     */
    public function get_direction()
    {
        return $this->direction;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $property
     */
    public function set_property($property)
    {
        $this->property = $property;
    }

    /**
     *
     * @param integer $direction
     */
    public function set_direction($direction)
    {
        $this->direction = $direction;
    }
}
