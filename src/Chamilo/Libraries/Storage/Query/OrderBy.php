<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * Describes the order by functionality of a query.
 * Uses ConditionVariable to define the property
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class OrderBy implements Hashable
{
    use HashableTrait;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    private $conditionVariable;

    /**
     *
     * @var integer
     */
    private $direction;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     * @param integer $direction
     */
    public function __construct(ConditionVariable $conditionVariable, $direction = SORT_ASC)
    {
        $this->conditionVariable = $conditionVariable;
        $this->direction = $direction;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function getConditionVariable()
    {
        return $this->conditionVariable;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     */
    public function setConditionVariable(ConditionVariable $conditionVariable)
    {
        $this->conditionVariable = $conditionVariable;
    }

    /**
     *
     * @return integer
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     *
     * @return integer
     * @deprecated User getDirection() now
     */
    public function get_direction()
    {
        return $this->getDirection();
    }

    /**
     *
     * @param integer $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    /**
     *
     * @param integer $direction
     *
     * @deprecated User setDirection() now
     */
    public function set_direction($direction)
    {
        $this->setDirection($direction);
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\Hashable::getHashParts()
     */
    public function getHashParts(): array
    {
        return array($this->getConditionVariable()->getHashParts(), $this->getDirection());
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     * @deprecated Use getConditionVariable() now
     */
    public function get_property()
    {
        return $this->getConditionVariable();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     *
     * @deprecated Use getConditionVariable() now
     */
    public function set_property(ConditionVariable $conditionVariable)
    {
        $this->setConditionVariable($conditionVariable);
    }
}
