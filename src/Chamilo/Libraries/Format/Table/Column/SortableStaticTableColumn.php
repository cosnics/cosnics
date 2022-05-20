<?php
namespace Chamilo\Libraries\Format\Table\Column;

use Chamilo\Libraries\Storage\Query\Variable\StaticColumnConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents a column for a table that is not sortable Refactoring from ObjectTable to split between a table
 * based on a record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table\Column
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SortableStaticTableColumn extends TableColumn
{

    /**
     *
     * @param string $name
     * @param string $title - [OPTIONAL] default null - translation of the column name
     * @param string $headerCssClasses
     * @param string $contentCssClasses
     */
    public function __construct($name, $title = null, $headerCssClasses = null, $contentCssClasses = null)
    {
        parent::__construct($name, $title, true, $headerCssClasses, $contentCssClasses);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable
     */
    public function getConditionVariable()
    {
        return new StaticConditionVariable($this->get_name(), false);
    }
}
