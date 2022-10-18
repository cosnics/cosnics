<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Table\Column\AbstractSortableTableColumn;
use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Column\OrderedTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Storage\Query\OrderProperty;

/**
 * This class represents a column model for a table Refactoring from ObjectTable to split between a table based on a
 * record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableColumnModel extends TableComponent
{
    public const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_ASC;
    public const DEFAULT_ORDER_COLUMN_INDEX = 0;

    /**
     * The column that is currently ordered
     */
    protected ?OrderedTableColumn $currentOrderedColumn;

    /**
     * @var \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    private array $columns;

    private int $defaultOrderColumnIndex;

    private int $defaultOrderDirection;

    public function __construct(Table $table)
    {
        parent::__construct($table);

        $this->initializeColumns();

        if ($this instanceof TableColumnModelActionsColumnSupport)
        {
            $this->addActionColumn();
        }

        $this->defaultOrderColumnIndex = static::DEFAULT_ORDER_COLUMN_INDEX;
        $this->defaultOrderDirection = static::DEFAULT_ORDER_COLUMN_DIRECTION;
    }

    /**
     * Adds the action column only if the action column is not yet added
     */
    protected function addActionColumn()
    {
        foreach ($this->getColumns() as $column)
        {
            if ($column instanceof ActionsTableColumn)
            {
                return;
            }
        }

        $this->addColumn(new ActionsTableColumn());
    }

    public function addColumn(TableColumn $column, ?int $index = null)
    {
        if (is_null($index))
        {
            $this->columns[] = $column;
        }
        else
        {
            array_splice($this->columns, $index, 0, [$column]);
        }
    }

    /**
     * Adds a current ordered column to the list
     */
    public function addCurrentOrderedColumnForColumnIndexAndOrderDirection(
        int $columnIndex, ?int $orderDirection = SORT_ASC
    )
    {
        $this->currentOrderedColumn = new OrderedTableColumn(
            $this->getColumn($columnIndex), $orderDirection
        );
    }

    /**
     * Gets the column at the given index in the model.
     */
    public function getColumn(int $index): ?TableColumn
    {
        return $this->columns[$index];
    }

    public function getColumnCount(): int
    {
        return count($this->columns);
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * Returns the current ordered column
     */
    public function getCurrentOrderedColumn(): ?OrderedTableColumn
    {
        return $this->currentOrderedColumn;
    }

    public function setCurrentOrderedColumn(OrderedTableColumn $orderedTableColumn)
    {
        $this->currentOrderedColumn = $orderedTableColumn;
    }

    public function getDefaultOrderColumnIndex(): int
    {
        return $this->defaultOrderColumnIndex;
    }

    public function setDefaultOrderColumnIndex(int $columnIndex)
    {
        $this->defaultOrderColumnIndex = $columnIndex;
    }

    public function getDefaultOrderDirection(): int
    {
        return $this->defaultOrderDirection;
    }

    /**
     * @param int $direction The direction. Either the PHP constant SORT_ASC or SORT_DESC.
     */
    public function setDefaultOrderDirection(int $direction)
    {
        $this->defaultOrderDirection = $direction;
    }

    /**
     * Returns an object table order object by a given column number and order direction
     */
    public function getOrderProperty(int $columnNumber, int $orderDirection): ?OrderProperty
    {
        $column = $this->getSortableColumn($columnNumber);

        if ($column instanceof AbstractSortableTableColumn)
        {
            return new OrderProperty($column->getConditionVariable(), $orderDirection);
        }

        return null;
    }

    /**
     * Returns a column by a given column index if it exists and is sortable, otherwise it returns the default column.
     */
    protected function getSortableColumn(int $columnNumber): ?AbstractSortableTableColumn
    {
        $column = $this->getColumn($columnNumber);

        if (!$column instanceof AbstractSortableTableColumn || (!$column->is_sortable()))
        {
            if ($columnNumber != $this->getDefaultOrderColumnIndex())
            {
                return $this->getSortableColumn($this->getDefaultOrderColumnIndex());
            }
        }
        else
        {
            return $column;
        }

        return null;
    }

    /**
     * Initializes the columns for the table
     */
    abstract public function initializeColumns();
}
