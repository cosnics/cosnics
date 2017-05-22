<?php

namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableAjaxSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableMultiColumnSortSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TablePageSelectionProhibition;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupportedSearchFormInterface;
use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class represents a table with the use of a column model, a data provider and a cell renderer Refactoring from
 * ObjectTable to split between a table based on a record and based on an object
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Table
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    /**
     * **************************************************************************************************************
     * Constants *
     * **************************************************************************************************************
     */

    /**
     * The default row count
     */
    const DEFAULT_ROW_COUNT = 20;

    /**
     * Suffix for checkbox name when using actions on selected learning objects.
     */
    const CHECKBOX_NAME_SUFFIX = '_id';

    /**
     * The identifier for the table (used for table actions)
     */
    const TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */

    /**
     * Application or submanager component calling the Table
     *
     * @var mixed <Application, SubManager>
     */
    private $component;

    /**
     * The column model assigned to this table
     */
    private $column_model;

    /**
     * The data provider assigned to this table
     */
    private $data_provider;

    /**
     * The cell renderer assigned to this table
     */
    private $cell_renderer;

    /**
     * Caching of form actions
     *
     * @var TableFormActions
     */
    private $form_actions;

    /**
     * The search form that supports this table
     *
     * @var TableSupportedSearchFormInterface
     */
    protected $searchForm;

    /**
     * The sortable table implementation
     *
     * @var SortableTable
     */
    protected $table;

    /**
     * **************************************************************************************************************
     * Constructor *
     * **************************************************************************************************************
     */

    /**
     * Constructor
     *
     * @param mixed $component The parent component
     *
     * @throws \Exception
     */
    public function __construct($component)
    {
        if (!$component instanceof TableSupport)
        {
            throw new \Exception(
                ClassnameUtilities::getInstance()->getClassnameFromObject($component) .
                " doesn't seem to support object tables, please implement the TableSupport interface"
            );
        }

        $interface_class = $this->get_class('Interface');

        if (interface_exists($interface_class))
        {
            if (!$component instanceof $interface_class)
            {
                throw new \Exception(
                    ClassnameUtilities::getInstance()->getClassnameFromObject($component) . ' must implement ' .
                    $interface_class
                );
            }
        }

        $this->component = $component;

        $this->constructTable();
    }

    /**
     * **************************************************************************************************************
     * Render Functionality *
     * **************************************************************************************************************
     */

    /**
     * Creates an HTML representation of the table.
     *
     * @return string The HTML.
     */
    public function as_html()
    {
        $this->initialize_table();

        return $this->table->toHtml();
    }

    /**
     * **************************************************************************************************************
     * Render Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Constructs the sortable table
     */
    protected function constructTable()
    {
        $this->table = new SortableTable(
            $this->get_name(),
            array($this, 'countData'),
            array($this, 'getData'),
            $this->get_column_model()->get_default_order_column() + ($this->has_form_actions() ? 1 : 0),
            $this->get_default_row_count(),
            $this->get_column_model()->get_default_order_direction(),
            !$this->prohibits_page_selection(),
            true,
            $this->get_column_model() instanceof TableMultiColumnSortSupport
        );

        $this->table->setAdditionalParameters($this->get_parameters());
    }

    /**
     * Initializes the table
     */
    protected function initialize_table()
    {
        if ($this->has_form_actions())
        {
            $this->table->setTableFormActions($this->get_form_actions());
        }

        // refactored the column model out of the loop.
        $column_model = &$this->get_column_model();
        $column_count = $column_model->get_column_count();

        for ($i = 0; $i < $column_count; $i ++)
        {
            $column = $column_model->get_column($i);

            $headerAttributes = $contentAttributes = array();

            $cssClasses = $column->getCssClasses();

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]))
            {
                $headerAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER];
            }

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT]))
            {
                $contentAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT];
            }

            $this->table->setColumnHeader(
                ($this->has_form_actions() ? $i + 1 : $i),
                Security::remove_XSS($column->get_title()),
                $column->is_sortable(),
                $headerAttributes,
                $contentAttributes
            );
        }

        // store the actual direction of the sortable table in the table column
        // model, to be used for a correct mover action implementation.
        // The prefix 'default_' is not relevant.
        $direction = intval($this->table->getOrderDirection());
        $column_model->set_default_order_direction($direction);

        $column_model->set_default_order_column($this->table->getOrderColumn());
    }

    /**
     * **************************************************************************************************************
     * Data Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the data from the data provider, parses the data through the cell renderer and returns the data as an
     * array
     *
     * @param int $offset
     * @param int $count
     * @param int $order_column
     * @param string $order_direction
     *
     * @return string[][]
     */
    public function getData($offset, $count, $orderColumns, $orderDirections)
    {
        $resultSet = $this->get_data_provider()->retrieve_data(
            $this->get_condition(),
            $offset,
            $count,
            $this->determineOrderProperties($orderColumns, $orderDirections)
        );

        $tableData = array();

        if ($resultSet)
        {
            while ($result = $resultSet->next_result())
            {
                $this->handle_result($tableData, $result);
            }
        }

        return $tableData;
    }

    protected function determineOrderProperties($orderColumns, $orderDirections)
    {
        $orderProperties = array();

        foreach ($orderColumns as $index => $orderColumn)
        {
            // Calculates the order column on whether or not the table uses form actions (because sortable
            // table uses data arrays)
            $calculatedOrderColumn = $orderColumn - ($this->has_form_actions() ? 1 : 0);
            $orderProperty = $this->get_order_property($calculatedOrderColumn, $orderDirections[$index]);

            if ($orderProperty)
            {
                $orderProperties[] = $orderProperty;
            }
        }

        return $orderProperties;
    }

    /**
     * Counts the number of rows that a full retrieve would provide
     *
     * @return int
     */
    public function countData()
    {
        return $this->get_data_provider()->count_data($this->get_condition());
    }

    /**
     * **************************************************************************************************************
     * Data Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the order property as ObjectTableOrder
     *
     * @param int $order_index
     * @param int $order_direction
     *
     * @return ObjectTableOrder
     */
    protected function get_order_property($order_index, $order_direction)
    {
        $column_model = $this->get_column_model();
        $column_model->addCurrentOrderedColumn($order_index, $order_direction);

        return $column_model->get_column_object_table_order($order_index, $order_direction);
    }

    /**
     * Handles a single result of the data and adds it to the table data
     *
     * @param $table_data
     * @param $result
     */
    protected function handle_result(&$table_data, $result)
    {
        $column_count = $this->get_column_model()->get_column_count();

        $row_data = array();

        if ($this->has_form_actions())
        {
            $row_data[] = $this->get_cell_renderer()->render_id_cell($result);
        }

        for ($i = 0; $i < $column_count; $i ++)
        {
            $row_data[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $result);
        }

        $table_data[] = $row_data;
    }

    /**
     * **************************************************************************************************************
     * Table action functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the selected ids
     *
     * @return int[]
     */
    public static function get_selected_ids()
    {
        $selected_ids = Request::post(static::get_name() . self::CHECKBOX_NAME_SUFFIX);

        if (empty($selected_ids))
        {
            $selected_ids = array();
        }
        elseif (!is_array($selected_ids))
        {
            $selected_ids = array($selected_ids);
        }

        return $selected_ids;
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     * Gets the table's data provider or builds one if it is not set
     *
     * @return TableDataProvider The data provider
     */
    public function get_data_provider()
    {
        if (!isset($this->data_provider))
        {
            $classname = $this->get_class('DataProvider');
            $this->data_provider = new $classname($this);
        }

        return $this->data_provider;
    }

    /**
     * Sets the data provider
     *
     * @param TableDataProvider $data_provider
     */
    public function set_data_provider($data_provider)
    {
        $this->data_provider = $data_provider;
    }

    /**
     * Gets the table's column model or builds one if it is not set
     *
     * @return TableColumnModel The column model
     */
    public function get_column_model()
    {
        if (!isset($this->column_model))
        {
            $classname = $this->get_class('ColumnModel');
            $this->column_model = new $classname($this);
        }

        return $this->column_model;
    }

    /**
     * Sets the column model
     *
     * @param TableColumnModel $column_model
     */
    public function set_column_model($column_model)
    {
        $this->column_model = $column_model;
    }

    /**
     * Gets the table's cell renderer or builds one if it is not set
     *
     * @return TableCellRenderer The cell renderer
     */
    public function get_cell_renderer()
    {
        if (!isset($this->cell_renderer))
        {
            $classname = $this->get_class('CellRenderer');
            $this->cell_renderer = new $classname($this);
        }

        return $this->cell_renderer;
    }

    /**
     * Sets the cell renderer
     *
     * @param TableCellRenderer $cell_renderer
     */
    public function set_cell_renderer($cell_renderer)
    {
        $this->cell_renderer = $cell_renderer;
    }

    /**
     * Returns the component
     *
     * @return mixed
     */
    public function get_component()
    {
        return $this->component;
    }

    /**
     * Sets the component
     *
     * @param mixed $component
     */
    public function set_component($component)
    {
        $this->component = $component;
    }

    /**
     * Gets the actions for the mass-update form at the bottom of the table.
     *
     * @return TableFormActions The actions as an associative array.
     */
    public function get_form_actions()
    {
        if (!isset($this->form_actions))
        {
            $this->form_actions = $this->get_implemented_form_actions();
        }

        return $this->form_actions;
    }

    /**
     * Connects a table supported search form to this table to share the parameters of the search form and the
     * table
     *
     * @param TableSupportedSearchFormInterface $searchForm
     */
    public function setSearchForm(TableSupportedSearchFormInterface $searchForm)
    {
        $this->searchForm = $searchForm;
        $searchForm->registerSearchFormParametersInTable($this);

        $filterParameters = $this->table->getTableFilterParameters();

        /**
         * We don't want paging to be registered because the number of pages can be different
         * depending on the search parameter
         */
        unset($filterParameters[$this->table->getParameterName(HtmlTable::PARAM_PAGE_NUMBER)]);

        $searchForm->registerTableParametersInSearchForm($filterParameters);
    }

    /**
     * Registers a new parameter and value in the array of parameters
     *
     * @param string $parameter
     * @param string $value
     */
    public function addParameter($parameter, $value)
    {
        $parameters = $this->table->getAdditionalParameters();
        $parameters[$parameter] = $value;

        $this->table->setAdditionalParameters($parameters);
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Builds a class name starting from this class name and extending it with the given type
     *
     * @param string $type
     *
     * @example get_class('DataProvider') returns TableDataProvider
     * @return string
     */
    protected function get_class($type = null)
    {
        $class_name = get_class($this);

        if (!is_null($type))
        {
            $class_name .= $type;
        }

        return $class_name;
    }

    /**
     * Checks whether or not this table prohibits page selection
     *
     * @return boolean
     */
    protected function prohibits_page_selection()
    {
        return $this instanceof TablePageSelectionProhibition;
    }

    /**
     * Checks whether or not this table supports ajax
     *
     * @return boolean
     */
    protected function supports_ajax()
    {
        return $this instanceof TableAjaxSupport;
    }

    /**
     * Gets the default row count of the table
     *
     * @return int The number of rows
     */
    protected function get_default_row_count()
    {
        return static::DEFAULT_ROW_COUNT;
    }

    /**
     * Returns whether or not the table has form actions
     *
     * @return boolean
     */
    public function has_form_actions()
    {
        return ($this instanceof TableFormActionsSupport && $this->get_form_actions() instanceof TableFormActions &&
            $this->get_form_actions()->has_form_actions());
    }

    /**
     * Returns the condition for this table
     *
     * @return mixed
     */
    protected function get_condition()
    {
        return $this->get_component()->get_table_condition(get_called_class());
    }

    /**
     * Returns the parameters for this table
     *
     * @return array
     */
    protected function get_parameters()
    {
        return $this->get_component()->get_parameters();
    }

    /**
     * **************************************************************************************************************
     * Static Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Gets the name of the HTML table element
     *
     * @return string The name
     */
    public static function get_name()
    {
        return static::class_name(false, false);
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static::context();
    }
}
