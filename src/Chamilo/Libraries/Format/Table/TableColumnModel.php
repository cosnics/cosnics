<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * This class represents a column model for a table Refactoring from ObjectTable to split between a table based on a
 * record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableColumnModel extends TableComponent
{
    const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_ASC;
    const DEFAULT_ORDER_COLUMN_INDEX = 0;

    const ORDER_COLUMN = 1;
    const ORDER_DIRECTION = 2;

    /**
     * The columns that are currently ordered, the index of the column is the key of the array, de direction is
     * the value
     *
     * @var integer[]
     */
    protected $currentOrderedColumns;

    /**
     * The columns in the table.
     *
     * @var \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    private $columns;

    /**
     * The column by which the table is sorted by default.
     *
     * @var integer
     */
    private $default_order_column;

    /**
     * The direction in which the table is sorted by default.
     *
     * @var integer
     */
    private $default_order_direction;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Format\Table\Table $table
     */
    public function __construct($table)
    {
        parent::__construct($table);

        $this->initialize_columns();

        if ($this instanceof TableColumnModelActionsColumnSupport)
        {
            $this->addActionColumn();
        }

        $this->set_default_order_column(static::DEFAULT_ORDER_COLUMN_INDEX);
        $this->set_default_order_direction(static::DEFAULT_ORDER_COLUMN_DIRECTION);

        $this->currentOrderedColumns = array();
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     * Adds the action column only if the action column is not yet added
     */
    protected function addActionColumn()
    {
        foreach ($this->get_columns() as $column)
        {
            if ($column instanceof ActionsTableColumn)
            {
                return;
            }
        }

        $this->add_column(new ActionsTableColumn());
    }

    /**
     * Adds a current ordered column to the list
     *
     * @param integer $columnIndex
     * @param integer $orderDirection
     */
    public function addCurrentOrderedColumn($columnIndex, $orderDirection = SORT_ASC)
    {
        $this->currentOrderedColumns[] = array(
            self::ORDER_COLUMN => $this->get_column($columnIndex),
            self::ORDER_DIRECTION => $orderDirection
        );
    }

    /**
     * Adds the given column at a given index or the end of the table.
     *
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn $column
     * @param integer $index
     */
    public function add_column(TableColumn $column, $index = null)
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
     * Delete a column at a given index
     *
     * @param integer $columnIndex
     */
    public function delete_column($columnIndex)
    {
        unset($this->columns[$columnIndex]);

        $this->columns = array_values($this->columns);
    }

    /**
     * Returns the current ordered columns
     *
     * @return integer[][]
     */
    public function getCurrentOrderedColumns()
    {
        return $this->currentOrderedColumns;
    }

    /**
     * Sets the current ordered columns
     *
     * @param integer[][] $currentOrderedColumns
     */
    public function setCurrentOrderedColumns($currentOrderedColumns = array())
    {
        $this->currentOrderedColumns = $currentOrderedColumns;
    }

    /**
     * Gets the column at the given index in the model.
     *
     * @param integer $index
     *
     * @return \Chamilo\Libraries\Format\Table\Column\TableColumn
     */
    public function get_column($index)
    {
        return $this->columns[$index];
    }

    /**
     * Returns the number of columns in the model.
     *
     * @return integer
     */
    public function get_column_count()
    {
        return count($this->columns);
    }

    /**
     * Returns an object table order object by a given column number and order direction
     *
     * @param integer $columnNumber
     * @param integer $orderDirection
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy
     */
    public function get_column_object_table_order($columnNumber, $orderDirection)
    {
        $column = $this->get_sortable_column($columnNumber);

        if ($column)
        {
            return new OrderBy($column->getConditionVariable(), $orderDirection);
        }
    }

    /**
     * Returns the columns
     *
     * @return \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    public function get_columns()
    {
        return $this->columns;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $columns
     */
    public function set_columns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * Returns the component of the object table
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function get_component()
    {
        return $this->get_table()->get_component();
    }

    /**
     * Returns the index of the default column to order objects by
     *
     * @return integer
     */
    public function get_default_order_column()
    {
        return $this->default_order_column;
    }

    /**
     * Sets the index of the default column to order objects by
     *
     * @param integer $columnIndex
     */
    public function set_default_order_column($columnIndex)
    {
        $this->default_order_column = $columnIndex;
    }

    /**
     * Gets the default order direction.
     *
     * @return integer The direction. Either the PHP constant SORT_ASC or SORT_DESC.
     */
    public function get_default_order_direction()
    {
        return $this->default_order_direction;
    }

    /**
     * Sets the default order direction.
     *
     * @param integer $direction The direction. Either the PHP constant SORT_ASC or SORT_DESC.
     */
    public function set_default_order_direction($direction)
    {
        $this->default_order_direction = $direction;
    }

    /**
     * Returns a column by a given column index if it exists and is sortable, otherwise it returns the default column.
     *
     * @param integer $columnNumber
     *
     * @return \Chamilo\Libraries\Format\Table\Column\TableColumn
     */
    protected function get_sortable_column($columnNumber)
    {
        $column = $this->get_column($columnNumber);

        if (!$column instanceof TableColumn || !$column->is_sortable())
        {
            if ($columnNumber != $this->get_default_order_column())
            {
                return $this->get_sortable_column($this->get_default_order_column());
            }
        }
        else
        {
            return $column;
        }
    }

    /**
     * Initializes the columns for the table
     */
    abstract public function initialize_columns();

    /**
     *
     * @param string $type
     *
     * @return boolean
     */
    public function is_order_column_type($type)
    {
        $current_column = $this->get_column($this->get_default_order_column());

        if ($current_column instanceof $type)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
