<?php
namespace Chamilo\Libraries\Format\Table\Extension\RecordTable;

use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\TableCellRenderer;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class represents a cell renderer for a record table
 *
 * @package Chamilo\Libraries\Format\Table\Extension\RecordTable
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class RecordTableCellRenderer extends TableCellRenderer
{

    /**
     * Renders a single cell
     *
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn $column
     * @param string[] $record
     *
     * @return string
     */
    public function renderCell(TableColumn $column, $record): string
    {
        if ($column instanceof ActionsTableColumn)
        {
            return parent::renderCell($column, $record);
        }

        return $record[$column->get_name()];
    }

    /**
     * Define the unique identifier for the row needed for e.g.
     * checkboxes
     *
     * @param string[] $row
     */
    public function renderIdentifierCell($row): string
    {
        return $row[DataClass::PROPERTY_ID];
    }
}
