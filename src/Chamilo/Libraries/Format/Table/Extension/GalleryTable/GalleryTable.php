<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Interfaces\GalleryTableOrderDirectionProhibition;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\GalleryHTMLTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Format\Table\Table;

/**
 * This class represents an table to display resources like thumbnails, images, videos...
 * Refactoring from GalleryObjectTable to support the new Table structure
 *
 * @package Chamilo\Libraries\Format\Table\Extension\GalleryTable
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
     * @var string[]
     */
    private $current_row;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    protected $form_actions;

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
     *
     * @see \Chamilo\Libraries\Format\Table\Table::initialize_table()
     */
    protected function initialize_table()
    {
    }

    /**
     * Retrieves the data from the data provider, parses the data through the cell renderer and returns the data
     * as an array
     *
     * @param integer $offset
     * @param integer $count
     * @param integer $orderColumn
     * @param string $orderDirection
     * @return string[][]
     */
    public function getData($offset, $count, $orderColumn, $orderDirection)
    {
        $table_data = parent::getData($offset, $count, $orderColumn, $orderDirection);

        if (count($this->current_row) > 0)
        {
            $table_data[] = $this->current_row;
        }

        return $table_data;
    }

    /**
     * Returns the order property as ObjectTableOrder
     *
     * @param integer $orderIndex
     * @param integer $orderDirection
     * @return \Chamilo\Libraries\Storage\Query\OrderBy
     */
    protected function get_order_property($orderIndex, $orderDirection)
    {
        return $this->get_property_model()->get_order_property($orderIndex, $orderDirection);
    }

    /**
     * Handles a single result of the data and adds it to the table data
     *
     * @param string[][] $tableData
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     */
    protected function handle_result(&$tableData, $result)
    {
        if (count($this->current_row) >= $this->get_default_column_count())
        {
            $tableData[] = $this->current_row;
            $this->current_row = array();
        }

        $this->current_row[] = array(
            $this->get_cell_renderer()->render_id_cell($result),
            $this->get_cell_renderer()->render_cell($result));
    }

    /**
     * Gets the table's cell renderer or builds one if it is not set
     *
     * @throws \Exception
     * @return \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTableCellRenderer
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
     * Gets the default column count of the table.
     *
     * @return integer
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
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions[]
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
     * @return \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTablePropertyModel The properties.
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
