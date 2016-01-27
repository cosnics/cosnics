<?php
namespace Chamilo\Libraries\Format\Table\Column;

use Chamilo\Libraries\Storage\Query\Variable\StaticColumnConditionVariable;

/**
 * This class represents a column for a table that is not sortable Refactoring from ObjectTable to split between a table
 * based on a record and based on an object (@TODO: Used New in the name because of the fact that there is currently a
 * class with the name TableColumn)
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SortableStaticTableColumn extends TableColumn
{

    /**
     * **************************************************************************************************************
     * Constructor *
     * **************************************************************************************************************
     */

    /**
     * Constructor
     *
     * @param string $name
     * @param string $title - [OPTIONAL] default null - translation of the column name
     */
    public function __construct($name, $title = null)
    {
        parent :: __construct($name, $title, true);
    }

    public function getConditionVariable()
    {
        return new StaticColumnConditionVariable($this->get_name());
    }
}
