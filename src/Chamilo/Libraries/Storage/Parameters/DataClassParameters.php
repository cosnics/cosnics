<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\GroupBy;
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
    use ClassContext;
    use HashableTrait;

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
     * The property of the DataClass object to be used as a parameter
     *
     * @var \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     */
    private $dataClassProperties;

    /**
     * The ordering of the DataClass objects to be applied to the result set
     *
     * @var \Chamilo\Libraries\Storage\Query\OrderBy[]
     */
    private $orderBy;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\GroupBy
     */
    private $groupBy;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    private $havingCondition;

    /**
     *
     * @var integer
     */
    private $count;

    /**
     *
     * @var integer
     */
    private $offset;

    /**
     *
     * @var boolean
     */
    private $distinct;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $dataClassProperties
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $groupBy
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $havingCondition
     * @param integer $count
     * @param integer $offset
     * @param boolean $distinct
     */
    public function __construct(
        Condition $condition = null, Joins $joins = null, DataClassProperties $dataClassProperties = null,
        $orderBy = array(), GroupBy $groupBy = null, Condition $havingCondition = null, $count = null, $offset = null,
        $distinct = false
    )
    {
        $this->setCondition($condition);
        $this->setJoins($joins);

        $this->setDataClassProperties($dataClassProperties);
        $this->setOrderBy($orderBy);
        $this->setGroupBy($groupBy);
        $this->setHavingCondition($havingCondition);
        $this->setCount($count);
        $this->setOffset($offset);
        $this->setDistinct($distinct);
    }

    /**
     * Get the condition to be applied to the action
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     * @deprecated User getCondition() now
     */
    public function get_condition()
    {
        return $this->getCondition();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @deprecated User setCondition() now
     */
    public function set_condition($condition)
    {
        $this->setCondition($condition);
    }

    /**
     * Set the condition to be applied to the action
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public function setCondition(Condition $condition = null)
    {
        $this->condition = $condition;
    }

    /**
     * Get the number of results to return
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set the number of results to return
     *
     * @param integer $count
     */
    public function setCount($count)
    {
        $this->count = (int) $count;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     */
    public function getDataClassProperties()
    {
        return $this->dataClassProperties;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $dataClassProperties
     */
    public function setDataClassProperties($dataClassProperties = null)
    {
        $this->dataClassProperties = $dataClassProperties;
    }

    /**
     *
     * @return boolean
     */
    public function getDistinct()
    {
        return $this->distinct;
    }

    /**
     *
     * @param boolean $distinct
     */
    public function setDistinct($distinct)
    {
        $this->distinct = (boolean) $distinct;
    }

    /**
     * Returns the group by parameter
     *
     * @return \Chamilo\Libraries\Storage\Query\GroupBy
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * Sets the group by parameter
     *
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $groupBy
     */
    public function setGroupBy(GroupBy $groupBy = null)
    {
        $this->groupBy = $groupBy;
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\Hashable::getHashParts()
     */
    public function getHashParts(): array
    {
        $hashParts = array();

        $hashParts[] = static::class;
        $hashParts[] = ($this->getCondition() instanceof Condition ? $this->getCondition()->getHashParts() : null);
        $hashParts[] = ($this->getJoins() instanceof Joins ? $this->getJoins()->getHashParts() : null);
        $hashParts[] = ($this->getDataClassProperties() instanceof DataClassProperties ?
            $this->getDataClassProperties()->getHashParts() : null);
        $hashParts[] = $this->getOrderByHashParts();
        $hashParts[] = ($this->getGroupBy() instanceof GroupBy ? $this->getGroupBy()->getHashParts() : null);
        $hashParts[] =
            ($this->getHavingCondition() instanceof Condition ? $this->getHavingCondition()->getHashParts() : null);
        $hashParts[] = $this->getCount();
        $hashParts[] = $this->getOffset();
        $hashParts[] = $this->getDistinct();

        return $hashParts;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function getHavingCondition()
    {
        return $this->havingCondition;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $havingCondition
     */
    public function setHavingCondition(Condition $havingCondition = null)
    {
        $this->havingCondition = $havingCondition;
    }

    /**
     * Get the join data classes to be applied to the action
     *
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Joins
     * @deprecated Use getJoins() now
     */
    public function get_joins()
    {
        return $this->joins;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     *
     * @deprecated Use setJoins() now
     */
    public function set_joins($joins)
    {
        $this->joins = $joins;
    }

    /**
     * Set the join data classes to be applied to the action
     *
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     */
    public function setJoins(Joins $joins = null)
    {
        $this->joins = $joins;
    }

    /**
     * Get the offset of the result set relative to the first ordered result
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set the offset of the result set relative to the first ordered result
     *
     * @param integer $offset
     */
    public function setOffset($offset)
    {
        $this->offset = (int) $offset;
    }

    /**
     * Get the ordering of the DataClass objects to be applied to the result set
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy[]
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Set the ordering of the DataClass objects to be applied to the result set
     *
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     */
    public function setOrderBy($orderBy = array())
    {
        $this->orderBy = $orderBy;
    }

    /**
     *
     * @return string[]
     */
    protected function getOrderByHashParts()
    {
        $hashParts = array();

        foreach ($this->getOrderBy() as $orderBy)
        {
            $hashParts[] = $orderBy->getHashParts();
        }

        return $hashParts;
    }

    /**
     *
     * @return string
     * @throws \ReflectionException
     */
    public static function package()
    {
        return static::context();
    }
}
