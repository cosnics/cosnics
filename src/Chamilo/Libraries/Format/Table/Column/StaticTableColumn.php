<?php
namespace Chamilo\Libraries\Format\Table\Column;

/**
 * This class represents a column for a table that is not sortable
 * Refactoring from ObjectTable to split between a table based on a record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table\Column
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StaticTableColumn extends TableColumn
{

    /**
     *
     * @param string $name
     * @param string $title - [OPTIONAL] default null - translation of the column name
     * @param string $headerCssClasses
     * @param string $contentCssClasses
     */
    public function __construct($name = null, $title = null, $headerCssClasses = null, $contentCssClasses = null)
    {
        parent::__construct($name, $title, false, $headerCssClasses, $contentCssClasses);
    }
}
