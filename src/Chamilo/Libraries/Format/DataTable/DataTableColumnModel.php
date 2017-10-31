<?php
namespace Chamilo\Libraries\Format\DataTable;

use Chamilo\Libraries\Format\DataTable\Column\ActionsDataTableColumn;
use Chamilo\Libraries\Format\DataTable\Column\DataTableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
abstract class DataTableColumnModel
{

    /**
     * The columns in the table.
     *
     * @var \Chamilo\Libraries\Format\DataTable\Column\DataTableColumn[]
     */
    private $columns;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initializeColumns();

        if ($this instanceof TableColumnModelActionsColumnSupport)
        {
            $this->addActionColumn();
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\DataTable\Column\DataTableColumn[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\DataTable\Column\DataTableColumn[] $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * Returns the number of columns in the model.
     *
     * @return integer
     */
    public function getNumberOfColumns()
    {
        return count($this->getColumns());
    }

    /**
     * Gets the column at the given index in the model.
     *
     * @param integer $index
     * @return \Chamilo\Libraries\Format\DataTable\Column\DataTableColumn
     */
    public function getColumn($index)
    {
        return $this->columns[$index];
    }

    /**
     * Adds the given column at a given index or the end of the table.
     *
     * @param \Chamilo\Libraries\Format\DataTable\Column\DataTableColumn $column
     * @param integer $index
     */
    public function addColumn(DataTableColumn $column, $index = null)
    {
        if (is_null($index))
        {
            $this->columns[] = $column;
        }
        else
        {
            array_splice($this->columns, $index, 0, array($column));
        }
    }

    /**
     *
     * @param integer $columnIndex
     */
    public function deleteColumn($columnIndex)
    {
        unset($this->columns[$columnIndex]);

        $this->columns = array_values($this->columns);
    }

    /**
     * Initializes the columns for the table
     */
    abstract public function initializeColumns();

    /**
     * Adds the action column only if the action column is not yet added
     */
    protected function addActionColumn()
    {
        foreach ($this->getColumns() as $column)
        {
            if ($column instanceof ActionsDataTableColumn)
            {
                return;
            }
        }

        $this->addColumn(new ActionsDataTableColumn());
    }
}
