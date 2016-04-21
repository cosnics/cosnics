<?php
namespace Chamilo\Libraries\Format\Table;

/**
 * This class represents a data provider for a table
 * Refactoring from ObjectTable to split between a table based on a record and based on an object
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableDataProvider extends TableComponent
{

    /**
     * **************************************************************************************************************
     * Abstract functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the data as a resultset
     * 
     * @abstract
     *
     *
     * @param \libraries\storage\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \libraries\storage\ResultSet
     */
    abstract public function retrieve_data($condition, $offset, $count, $order_property = null);

    /**
     * Counts the data
     * 
     * @abstract
     *
     *
     * @param \libraries\storage\Condition $condition
     *
     * @return int
     */
    abstract public function count_data($condition);
}
