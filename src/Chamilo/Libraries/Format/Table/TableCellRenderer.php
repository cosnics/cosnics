<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class represents a cell renderer for a table Refactoring from ObjectTable to split between a table based on a
 * record and based on an object
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableCellRenderer extends TableComponent
{

    /**
     * **************************************************************************************************************
     * Constructor *
     * **************************************************************************************************************
     */

    /**
     * Constructor
     *
     * @param Table $table
     *
     * @throws \Exception
     */
    public function __construct($table)
    {
        parent :: __construct($table);

        if ($table->get_column_model() instanceof TableColumnModelActionsColumnSupport)
        {
            if (! $this instanceof TableCellRendererActionsColumnSupport)
            {
                throw new \Exception(Translation :: get('ActionsColumnSupportError'));
            }
        }
    }

    /**
     * **************************************************************************************************************
     * Abstract Functionality *
     * **************************************************************************************************************
     */

    /**
     * Renders a single cell
     *
     * @param TableColumn $column
     * @param mixed $result
     * @return string
     */
    public function render_cell($column, $result)
    {
        if ($column instanceof ActionsTableColumn && $this instanceof TableCellRendererActionsColumnSupport)
        {
            return $this->get_actions($result);
        }
    }

    public function is_order_column_type($type)
    {
        return $this->get_table()->get_column_model()->is_order_column_type($type);
    }

    /**
     * Define the unique identifier for the row needed for e.g.
     * checkboxes
     *
     * @param mixed $result
     * @return int
     */
    abstract public function render_id_cell($result);
}
