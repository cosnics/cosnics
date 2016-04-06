<?php
namespace Chamilo\Libraries\Format\Table\Extension\DataClassTable;

use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\TableCellRenderer;

/**
 *
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop>
 */
abstract class DataClassTableCellRenderer extends TableCellRenderer
{

    /**
     * **************************************************************************************************************
     * Implemented Functionality *
     * **************************************************************************************************************
     */

    /**
     * Renders a single cell
     *
     * @param DataClassColumn $column
     * @param DataClass $data_class
     *
     * @return string
     */
    public function render_cell($column, $data_class)
    {
        if ($column instanceof ActionsTableColumn)
        {
            return parent :: render_cell($column, $data_class);
        }

        return $data_class->get_default_property($column->get_name());
    }

    /**
     * Define the unique identifier for the DataClass needed for e.g.
     * checkboxes
     *
     * @param DataClass $data_class
     *
     * @return int
     */
    public function render_id_cell($data_class)
    {
        return $data_class->get_id();
    }
}
