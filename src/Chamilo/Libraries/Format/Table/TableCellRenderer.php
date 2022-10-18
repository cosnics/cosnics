<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Exception;

/**
 * This class represents a cell renderer for a table Refactoring from ObjectTable to split between a table based on a
 * record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableCellRenderer extends TableComponent
{

    /**
     * @throws \Exception
     */
    public function __construct(Table $table)
    {
        parent::__construct($table);

        if ($table->getTableColumnModel() instanceof TableColumnModelActionsColumnSupport)
        {
            if (!$this instanceof TableCellRendererActionsColumnSupport)
            {
                throw new Exception(
                    get_class($this) .
                    'doesn\'t support action. Please implement the TableCellRendererActionsColumnSupport interface'
                );
            }
        }
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn $column
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|array $result
     *
     * @return string
     */
    public function renderCell(TableColumn $column, $result): string
    {
        if ($column instanceof ActionsTableColumn && $this instanceof TableCellRendererActionsColumnSupport)
        {
            return $this->get_actions($result);
        }

        return '';
    }

    /**
     * Define the unique identifier for the row needed for e.g.
     * checkboxes
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|array $result
     */
    abstract public function renderIdentifierCell($result): string;
}
