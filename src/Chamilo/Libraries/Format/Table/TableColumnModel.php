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
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableColumnModel extends TableComponent
{
    /**
     * **************************************************************************************************************
     * Default Constants *
     * **************************************************************************************************************
     */
    const DEFAULT_ORDER_COLUMN_INDEX = 0;
    const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_ASC;

    const ORDER_COLUMN = 1;
    const ORDER_DIRECTION = 2;

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */

    /**
     * The columns in the table.
     *
     * @var TableColumn[]
     */
    private $columns;

    /**
     * The column by which the table is sorted by default.
     *
     * @var int
     */
    private $default_order_column;

    /**
     * The direction in which the table is sorted by default.
     *
     * @var int
     */
    private $default_order_direction;

    /**
     * The columns that are currently ordered, the index of the column is the key of the array, de direction is
     * the value
     *
     * @var int[]
     */
    protected $currentOrderedColumns;

    /**
     * **************************************************************************************************************
     * Constructor *
     * **************************************************************************************************************
     */

    /**
     * Constructor
     *
     * @param Table $table
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
     * Returns the columns
     *
     * @return TableColumn[]
     */
    public function get_columns()
    {
        return $this->columns;
    }

    /**
     * Sets the columns
     *
     * @param $columns TableColumn[]
     */
    public function set_columns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * Returns the index of the default column to order objects by
     *
     * @return int
     */
    public function get_default_order_column()
    {
        return $this->default_order_column;
    }

    /**
     * Sets the index of the default column to order objects by
     *
     * @param $column_index int
     */
    public function set_default_order_column($column_index)
    {
        $this->default_order_column = $column_index;
    }

    /**
     * Gets the default order direction.
     *
     * @return int - The direction. Either the PHP constant SORT_ASC or SORT_DESC.
     */
    public function get_default_order_direction()
    {
        return $this->default_order_direction;
    }

    /**
     * Sets the default order direction.
     *
     * @param $direction int - The direction. Either the PHP constant SORT_ASC or SORT_DESC.
     */
    public function set_default_order_direction($direction)
    {
        $this->default_order_direction = $direction;
    }

    /**
     * Returns the current ordered columns
     *
     * @return int[]
     */
    public function getCurrentOrderedColumns()
    {
        return $this->currentOrderedColumns;
    }

    /**
     * Sets the current ordered columns
     *
     * @param array $currentOrderedColumns
     */
    public function setCurrentOrderedColumns($currentOrderedColumns = array())
    {
        $this->currentOrderedColumns = $currentOrderedColumns;
    }

    /**
     * Adds a current ordered column to the list
     *
     * @param int $columnIndex
     * @param int $orderDirection
     */
    public function addCurrentOrderedColumn($columnIndex, $orderDirection = SORT_ASC)
    {
        $this->currentOrderedColumns[] = array(
            self::ORDER_COLUMN => $this->get_column($columnIndex), self::ORDER_DIRECTION => $orderDirection
        );
    }

    /**
     * **************************************************************************************************************
     * Delegation Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the component of the object table
     *
     * @return mixed <Application, SubManager>
     */
    public function get_component()
    {
        return $this->get_table()->get_component();
    }

    /**
     * **************************************************************************************************************
     * List Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the number of columns in the model.
     *
     * @return int
     */
    public function get_column_count()
    {
        return count($this->columns);
    }

    /**
     * Gets the column at the given index in the model.
     *
     * @param $index int
     *
     * @return TableColumn The column.
     */
    public function get_column($index)
    {
        return $this->columns[$index];
    }

    /**
     * Adds the given column at a given index or the end of the table.
     *
     * @param TableColumn $column
     * @param int $index - [OPTIONAL]
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
     * @param $column_index int
     */
    public function delete_column($column_index)
    {
        unset($this->columns[$column_index]);

        $this->columns = array_values($this->columns);
    }

    /**
     * **************************************************************************************************************
     * Public Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns an object table order object by a given column number and order direction
     *
     * @param $column_number
     * @param $order_direction
     *
     * @return ObjectTableOrder
     */
    public function get_column_object_table_order($column_number, $order_direction)
    {
        $column = $this->get_sortable_column($column_number);

        if ($column)
        {
            return new OrderBy($column->getConditionVariable(), $order_direction);
        }
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns a column by a given column index if it exists and is sortable, otherwise it returns the default column.
     *
     * @param $column_number int
     *
     * @return TableColumn
     */
    protected function get_sortable_column($column_number)
    {
        $column = $this->get_column($column_number);

        if (!$column instanceof TableColumn || !$column->is_sortable())
        {
            if ($column_number != $this->get_default_order_column())
            {
                return $this->get_sortable_column($this->get_default_order_column());
            }
        }
        else
        {
            return $column;
        }
    }

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

    /**
     * **************************************************************************************************************
     * Abstract Functionality *
     * **************************************************************************************************************
     */

    /**
     * Initializes the columns for the table
     */
    abstract public function initialize_columns();

    /**
     * Adds the action column only if the action column is not yet added
     */
    protected function addActionColumn()
    {
        foreach ($this->columns as $column)
        {
            if ($column instanceof ActionsTableColumn)
            {
                return;
            }
        }

        $this->add_column(new ActionsTableColumn());
    }
}
