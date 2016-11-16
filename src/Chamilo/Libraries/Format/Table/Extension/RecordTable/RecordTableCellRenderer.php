<?php
namespace Chamilo\Libraries\Format\Table\Extension\RecordTable;

use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\TableCellRenderer;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class represents a cell renderer for a record table Refactoring from ObjectTable to split between a table based
 * on a record and based on an object
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class RecordTableCellRenderer extends TableCellRenderer
{

    /**
     * **************************************************************************************************************
     * Implemented Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Renders a single cell
     * 
     * @param RecordTableColumn $column
     * @param string[] $row
     *
     * @return String
     */
    public function render_cell($column, $row)
    {
        if ($column instanceof ActionsTableColumn)
        {
            return parent::render_cell($column, $row);
        }
        
        return $row[$column->get_name()];
    }

    /**
     * Define the unique identifier for the row needed for e.g.
     * checkboxes
     * 
     * @param string[] $row
     *
     * @return int
     */
    public function render_id_cell($row)
    {
        return $row[DataClass::PROPERTY_ID];
    }
}
