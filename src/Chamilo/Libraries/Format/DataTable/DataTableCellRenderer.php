<?php
namespace Chamilo\Libraries\Format\DataTable;

use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class DataTableCellRenderer
{

    /**
     * Renders a single cell
     *
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn $column
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $data_class
     * @return string
     */
    public function renderCell($column, DataClass $dataClass)
    {
        if ($column instanceof ActionsTableColumn && $this instanceof TableCellRendererActionsColumnSupport)
        {
            return $this->getActions($dataClass);
        }

        return $dataClass->get_default_property($column->getName());
    }
}
