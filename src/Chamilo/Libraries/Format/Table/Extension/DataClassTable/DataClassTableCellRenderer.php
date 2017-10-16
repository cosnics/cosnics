<?php
namespace Chamilo\Libraries\Format\Table\Extension\DataClassTable;

use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\TableCellRenderer;

/**
 *
 * @package Chamilo\Libraries\Format\Table\Extension\DataClassTable
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop>
 */
abstract class DataClassTableCellRenderer extends TableCellRenderer
{

    /**
     * Renders a single cell
     *
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn $column
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $data_class
     * @return string
     */
    public function render_cell($column, $dataClass)
    {
        if ($column instanceof ActionsTableColumn)
        {
            return parent::render_cell($column, $dataClass);
        }

        return $dataClass->get_default_property($column->get_name());
    }

    /**
     * Define the unique identifier for the DataClass needed for e.g.
     * checkboxes
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     * @return integer
     */
    public function render_id_cell($dataClass)
    {
        return $dataClass->get_id();
    }
}
