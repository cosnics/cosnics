<?php
namespace Chamilo\Libraries\Format\Table;

/**
 * This class represents a data provider for a table
 * Refactoring from ObjectTable to split between a table based on a record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableDataProvider extends TableComponent
{
    /**
     * @var \Chamilo\Libraries\Storage\Parameters\FilterParameters
     */
    protected $filterParameters;

    /**
     * Returns the data as a resultset
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    abstract public function retrieve_data($condition, $offset, $count, $orderProperties = array());

    /**
     * Counts the data
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return integer
     */
    abstract public function count_data($condition);

    /**
     * @return \Chamilo\Libraries\Storage\Parameters\FilterParameters
     */
    public function getFilterParameters(): \Chamilo\Libraries\Storage\Parameters\FilterParameters
    {
        return $this->filterParameters;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     */
    public function setFilterParameters(\Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters): void
    {
        $this->filterParameters = $filterParameters;
    }
}
