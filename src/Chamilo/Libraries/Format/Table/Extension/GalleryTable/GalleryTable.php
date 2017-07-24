<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Interfaces\GalleryTableOrderDirectionProhibition;
use Chamilo\Libraries\Format\Table\GalleryHTMLTable;
use Chamilo\Libraries\Format\Table\Table;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;

/**
 * This class represents an table to display resources like thumbnails, images, videos...
 * Refactoring from GalleryObjectTable to support the new Table structure
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class GalleryTable extends Table
{
    /**
     * The default row count
     */
    const DEFAULT_COLUMN_COUNT = 4;
    const DEFAULT_ROW_COUNT = 5;

    /**
     * The current row that is being processed
     *
     * @var array
     */
    private $current_row;

    /**
     *
     * @var TableFormActions
     */
    protected $form_actions;

    /**
     * **************************************************************************************************************
     * Inherited Rendering Functionality *
     * **************************************************************************************************************
     */

    /**
     * Constructs the sortable table
     */
    protected function constructTable()
    {
        $this->table = new GalleryHTMLTable(
            $this->get_name(),
            array($this, 'countData'),
            array($this, 'getData'),
            array($this, 'get_property_model'),
            $this->get_property_model()->get_default_order_property() + ($this->has_form_actions() ? 1 : 0),
            $this->get_default_row_count(),
            $this->get_property_model()->get_default_order_direction(),
            ! $this->prohibits_order_direction(),
            ! $this->prohibits_page_selection());

        if ($this->has_form_actions())
        {
            $this->table->setTableFormActions($this->get_form_actions());
        }

        $this->table->setAdditionalParameters($this->get_parameters());
    }

    /**
     * Initializes the table
     *
     * @return HTML_Table
     */
    protected function initialize_table()
    {
    }

    /**
     * **************************************************************************************************************
     * Inherited Data Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the data from the data provider, parses the data through the cell renderer and returns the data
     * as an array
     *
     * @param int $offset
     * @param int $count
     * @param int $order_column
     * @param string $order_direction
     *
     * @return string[][]
     */
    public function getData($offset, $count, $order_column, $order_direction)
    {
        $table_data = parent::getData($offset, $count, $order_column, $order_direction);

        if (count($this->current_row) > 0)
        {
            $table_data[] = $this->current_row;
        }

        return $table_data;
    }

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
        return $this->get_property_model()->get_order_property($order_index, $order_direction);
    }

    /**
     * Handles a single result of the data and adds it to the table data
     *
     * @param $table_data
     * @param $result
     */
    protected function handle_result(&$table_data, $result)
    {
        if (count($this->current_row) >= $this->get_default_column_count())
        {
            $table_data[] = $this->current_row;
            $this->current_row = array();
        }

        $this->current_row[] = array(
            $this->get_cell_renderer()->render_id_cell($result),
            $this->get_cell_renderer()->render_cell($result));
    }

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Gets the table's cell renderer or builds one if it is not set
     *
     * @throws \Exception
     *
     * @return GalleryTableCellRenderer The cell renderer
     */
    public function get_cell_renderer()
    {
        $cell_renderer = parent::get_cell_renderer();
        if (! $cell_renderer instanceof GalleryTableCellRenderer)
        {
            throw new \Exception('The cell renderer must be of type GalleryTableCellRenderer');
        }

        return $cell_renderer;
    }

    /**
     * **************************************************************************************************************
     * Additional Functionality *
     * **************************************************************************************************************
     */

    /**
     * Gets the default column count of the table.
     *
     * @return int The number of columns.
     */
    public function get_default_column_count()
    {
        return static::DEFAULT_COLUMN_COUNT;
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
     * Gets the actions for the mass-update form at the bottom of the table.
     *
     * @return TableFormActions The actions as an associative array.
     */
    public function get_form_actions()
    {
        if (! isset($this->form_actions))
        {
            $this->form_actions = $this->get_implemented_form_actions();
        }

        return $this->form_actions;
    }

    /**
     * Gets the table's property model.
     *
     * @return GalleryTablePropertyModel The properties.
     */
    public function get_property_model()
    {
        if (! isset($this->property_model))
        {
            $classname = $this->get_class('PropertyModel');
            $this->property_model = new $classname($this);
        }
        return $this->property_model;
    }

    /**
     * Returns if this table supports order direction or not
     *
     * @return boolean
     */
    public function prohibits_order_direction()
    {
        return $this instanceof GalleryTableOrderDirectionProhibition;
    }
}
