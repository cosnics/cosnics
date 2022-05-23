<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Interfaces\GalleryTableOrderDirectionProhibition;
use Chamilo\Libraries\Format\Table\GalleryHTMLTable;
use Chamilo\Libraries\Format\Table\Table;
use Exception;

/**
 * This class represents an table to display resources like thumbnails, images, videos...
 * Refactoring from GalleryObjectTable to support the new Table structure
 *
 * @package Chamilo\Libraries\Format\Table\Extension\GalleryTable
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class GalleryTable extends Table
{
    const DEFAULT_COLUMN_COUNT = 4;
    const DEFAULT_ROW_COUNT = 5;

    /**
     * The current row that is being processed
     *
     * @var string[]
     */
    private $current_row;

    /**
     * Constructs the sortable table
     */
    protected function constructTable()
    {
        $this->table = new GalleryHTMLTable(
            $this->get_name(), array($this, 'countData'), array($this, 'getData'), array($this, 'get_property_model'),
            $this->get_property_model()->getDefaultOrderBy() + ($this->has_form_actions() ? 1 : 0),
            $this->get_default_row_count(), $this->get_property_model()->get_default_order_direction(),
            !$this->prohibits_order_direction(), !$this->prohibits_page_selection()
        );

        if ($this->has_form_actions())
        {
            $this->table->setTableFormActions($this->get_form_actions());
        }

        $this->table->setAdditionalParameters($this->get_parameters());
    }

    /**
     * Retrieves the data from the data provider, parses the data through the cell renderer and returns the data
     * as an array
     *
     * @param integer $offset
     * @param integer $count
     * @param integer[] $orderColumn
     * @param string[] $orderDirection
     *
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
     * Gets the table's cell renderer or builds one if it is not set
     *
     * @return \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTableCellRenderer
     * @throws \Exception
     */
    public function get_cell_renderer()
    {
        $cell_renderer = parent::get_cell_renderer();

        if (!$cell_renderer instanceof GalleryTableCellRenderer)
        {
            throw new Exception('The cell renderer must be of type GalleryTableCellRenderer');
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
     * Returns the order property as \Chamilo\Libraries\Storage\Query\OrderBy
     *
     * @param integer $orderIndex
     * @param integer $orderDirection
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy
     */
    protected function get_order_property($orderIndex, $orderDirection)
    {
        return $this->get_property_model()->get_order_property($orderIndex, $orderDirection);
    }

    /**
     * Gets the table's property model.
     *
     * @return \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTablePropertyModel The properties.
     */
    public function get_property_model()
    {
        if (!isset($this->property_model))
        {
            $classname = $this->get_class('PropertyModel');
            $this->property_model = new $classname($this);
        }

        return $this->property_model;
    }

    /**
     * Handles a single result of the data and adds it to the table data
     *
     * @param string[][] $tableData
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     *
     * @throws \Exception
     */
    protected function handle_result(&$tableData, $result)
    {
        if (count($this->current_row) >= $this->get_default_column_count())
        {
            $tableData[] = $this->current_row;
            $this->current_row = [];
        }

        $this->current_row[] = array(
            $this->get_cell_renderer()->render_id_cell($result),
            $this->get_cell_renderer()->render_cell(null, $result)
        );
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Table::initialize_table()
     */
    protected function initialize_table()
    {
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
