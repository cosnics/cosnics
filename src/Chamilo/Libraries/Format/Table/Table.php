<?php
namespace Chamilo\Libraries\Format\Table;

use ArrayIterator;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
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
use Exception;

/**
 * This class represents a table with the use of a column model, a data provider and a cell renderer Refactoring from
 * ObjectTable to split between a table based on a record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Table
{
    use ClassContext;

    /**
     * Suffix for checkbox name when using actions on selected learning objects.
     */
    const CHECKBOX_NAME_SUFFIX = '_id';

    /**
     * The default row count
     */
    const DEFAULT_ROW_COUNT = 20;

    /**
     * The identifier for the table (used for table actions)
     */
    const TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\Interfaces\TableSupportedSearchFormInterface
     */
    protected $searchForm;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\SortableTable
     */
    protected $table;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $component;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\TableColumnModel
     */
    private $column_model;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\TableDataProvider
     */
    private $data_provider;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\TableCellRenderer
     */
    private $cell_renderer;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    private $form_actions;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     *
     * @throws \Exception
     */
    public function __construct($component)
    {
        if (!$component instanceof TableSupport)
        {
            throw new Exception(
                ClassnameUtilities::getInstance()->getClassnameFromObject($component) .
                " doesn't seem to support object tables, please implement the TableSupport interface"
            );
        }

        $interfaceClass = $this->get_class('Interface');

        if (interface_exists($interfaceClass))
        {
            if (!$component instanceof $interfaceClass)
            {
                throw new Exception(
                    ClassnameUtilities::getInstance()->getClassnameFromObject($component) . ' must implement ' .
                    $interfaceClass
                );
            }
        }

        $this->component = $component;

        $this->constructTable();
    }

    /**
     * Creates an HTML representation of the table.
     *
     * @return string
     */
    public function render()
    {
        $this->initialize_table();

        return $this->table->render();
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
     * Creates an HTML representation of the table.
     *
     * @return string
     * @deprecated User render() now
     */
    public function as_html()
    {
        return $this->render();
    }

    /**
     * Constructs the sortable table
     */
    protected function constructTable()
    {
        $this->table = new SortableTable(
            $this->get_name(), array($this, 'countData'), array($this, 'getData'),
            $this->get_column_model()->get_default_order_column() + ($this->has_form_actions() ? 1 : 0),
            $this->get_default_row_count(), $this->get_column_model()->get_default_order_direction(),
            !$this->prohibits_page_selection(), true, $this->get_column_model() instanceof TableMultiColumnSortSupport
        );

        $this->table->setAdditionalParameters($this->get_parameters());
    }

    /**
     * Counts the number of rows that a full retrieve would provide
     *
     * @return integer
     */
    public function countData()
    {
        return $this->get_data_provider()->count_data($this->get_condition());
    }

    /**
     *
     * @param integer[] $orderColumns
     * @param integer[] $orderDirections
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy[]
     */
    protected function determineOrderProperties($orderColumns, $orderDirections)
    {
        $orderProperties = [];

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
     * Retrieves the data from the data provider, parses the data through the cell renderer and returns the data as an
     * array
     *
     * @param integer $offset
     * @param integer $count
     * @param integer[] $orderColumns
     * @param integer[] $orderDirections
     *
     * @return string[][]
     */
    public function getData($offset, $count, $orderColumns, $orderDirections)
    {
        $resultSet = $this->get_data_provider()->retrieve_data(
            $this->get_condition(), $offset, $count, $this->determineOrderProperties($orderColumns, $orderDirections)
        );

        $tableData = [];

        foreach ($resultSet as $result)
        {
            $this->handle_result($tableData, $result);
        }

        return $tableData;
    }

    /**
     * Gets the table's cell renderer or builds one if it is not set
     *
     * @return \Chamilo\Libraries\Format\Table\TableCellRenderer
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
     * @param \Chamilo\Libraries\Format\Table\TableCellRenderer $cellRenderer
     */
    public function set_cell_renderer($cellRenderer)
    {
        $this->cell_renderer = $cellRenderer;
    }

    /**
     * Builds a class name starting from this class name and extending it with the given type
     *
     * @param string $type
     *
     * @return string
     */
    protected function get_class($type = null)
    {
        $className = get_class($this);

        if (!is_null($type))
        {
            $className .= $type;
        }

        return $className;
    }

    /**
     * Gets the table's column model or builds one if it is not set
     *
     * @return \Chamilo\Libraries\Format\Table\TableColumnModel
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
     * @param \Chamilo\Libraries\Format\Table\TableColumnModel $columnModel
     */
    public function set_column_model($columnModel)
    {
        $this->column_model = $columnModel;
    }

    /**
     * Returns the component
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function get_component()
    {
        return $this->component;
    }

    /**
     * Sets the component
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     */
    public function set_component(Application $component)
    {
        $this->component = $component;
    }

    /**
     * Returns the condition for this table
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function get_condition()
    {
        return $this->get_component()->get_table_condition(get_called_class());
    }

    /**
     * Gets the table's data provider or builds one if it is not set
     *
     * @return \Chamilo\Libraries\Format\Table\TableDataProvider
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
     * @param \Chamilo\Libraries\Format\Table\TableDataProvider $dataProvider
     */
    public function set_data_provider($dataProvider)
    {
        $this->data_provider = $dataProvider;
    }

    /**
     * Gets the default row count of the table
     *
     * @return integer
     */
    protected function get_default_row_count()
    {
        return static::DEFAULT_ROW_COUNT;
    }

    /**
     * Gets the actions for the mass-update form at the bottom of the table.
     *
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
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
     * Gets the name of the HTML table element
     *
     * @return string
     */
    public static function get_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(static::class, true);
    }

    /**
     * Returns the order property as ObjectTableOrder
     *
     * @param integer $orderIndex
     * @param integer $orderDirection
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy
     */
    protected function get_order_property($orderIndex, $orderDirection)
    {
        $columnModel = $this->get_column_model();
        $columnModel->addCurrentOrderedColumn($orderIndex, $orderDirection);

        return $columnModel->get_column_object_table_order($orderIndex, $orderDirection);
    }

    /**
     * Returns the parameters for this table
     *
     * @return string[]
     */
    protected function get_parameters()
    {
        return $this->get_component()->get_parameters();
    }

    /**
     * Returns the selected ids
     *
     * @return integer[]
     */
    public static function get_selected_ids()
    {
        $selectedIds = Request::post(static::get_name() . self::CHECKBOX_NAME_SUFFIX);

        if (empty($selectedIds))
        {
            $selectedIds = [];
        }
        elseif (!is_array($selectedIds))
        {
            $selectedIds = array($selectedIds);
        }

        return $selectedIds;
    }

    /**
     * Handles a single result of the data and adds it to the table data
     *
     * @param string[][] $table_data
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|array[] $result
     */
    protected function handle_result(&$tableData, $result)
    {
        $columnCount = $this->get_column_model()->get_column_count();

        $rowData = [];

        if ($this->has_form_actions())
        {
            $rowData[] = $this->get_cell_renderer()->render_id_cell($result);
        }

        for ($i = 0; $i < $columnCount; $i ++)
        {
            $rowData[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $result);
        }

        $tableData[] = $rowData;
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
     * Initializes the table
     */
    protected function initialize_table()
    {
        if ($this->has_form_actions())
        {
            $this->table->setTableFormActions($this->get_form_actions());
        }

        // refactored the column model out of the loop.
        $columnModel = $this->get_column_model();
        $columnCount = $columnModel->get_column_count();

        for ($i = 0; $i < $columnCount; $i ++)
        {
            $column = $columnModel->get_column($i);

            $headerAttributes = $contentAttributes = [];

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
                ($this->has_form_actions() ? $i + 1 : $i), Security::remove_XSS($column->get_title()),
                $column->is_sortable(), $headerAttributes, $contentAttributes
            );
        }

        // store the actual direction of the sortable table in the table column
        // model, to be used for a correct mover action implementation.
        // The prefix 'default_' is not relevant.
        $direction = intval($this->table->getOrderDirection());
        $columnModel->set_default_order_direction($direction);

        $columnModel->set_default_order_column($this->table->getOrderColumn());
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static::context();
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
     * Connects a table supported search form to this table to share the parameters of the search form and the
     * table
     *
     * @param \Chamilo\Libraries\Format\Table\Interfaces\TableSupportedSearchFormInterface $searchForm
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
     * Checks whether or not this table supports ajax
     *
     * @return boolean
     */
    protected function supports_ajax()
    {
        return $this instanceof TableAjaxSupport;
    }
}
