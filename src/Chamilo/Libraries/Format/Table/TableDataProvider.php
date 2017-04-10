<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

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
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $order_property
     *
     * @return ResultSet
     */
    abstract public function retrieve_data($condition, $offset, $count, $order_property = null);

    /**
     * Counts the data
     * 
     * @param Condition $condition
     *
     * @return int
     */
    abstract public function count_data($condition);
}
