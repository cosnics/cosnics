<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable;

use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Interfaces\GalleryTableOrderDirectionProhibition;
use Chamilo\Libraries\Format\Table\GalleryHTMLTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
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
    const DEFAULT_MAXIMUM_NUMBER_OF_RESULTS = 5;

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
            $this->getDefaultOrderBy() + ($this->hasTableActions() ? 1 : 0),
            $this->getDefaultMaximumNumberofResults(), $this->getDefaultOrderDirection(),
            !$this->prohibits_order_direction()
        );

        if ($this->hasTableActions())
        {
            $this->table->setTableFormActions($this->getTableActions());
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
     * @return \Chamilo\Libraries\Storage\Query\OrderProperty
     */
    public function getOrderProperty(int $orderIndex, int $orderDirection)
    {
        return $this->get_property_model()->getOrderProperty($orderIndex, $orderDirection);
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
            $classname = get_class($this) . 'PropertyModel';
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
    protected function handleResult(array &$tableData, $result)
    {
        if (count($this->current_row) >= $this->get_default_column_count())
        {
            $tableData[] = $this->current_row;
            $this->current_row = [];
        }

        $this->current_row[] = array(
            $this->renderIdentifierCell($result),
            $this->renderCell(null, $result)
        );
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Table::initializeTable()
     */
    protected function initializeTable()
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

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     *
     * @return string
     */
    abstract public function renderContent($result);

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     *
     * @return string
     */
    abstract public function renderTitle($result);

    /**
     * Renders a single cell
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     *
     * @return string
     */
    public function renderCell(TableColumn $column, $result): string
    {
        $html = [];

        $html[] = '<div class="panel panel-default panel-gallery">';

        $html[] = '<div class="panel-heading">';

        if ($this->getTable()->hasTableActions())
        {
            $html[] = '__CHECKBOX_PLACEHOLDER__';
        }

        $title = $this->renderTitle($result);

        $html[] = '<h3 class="panel-title" title="' . $title . '">';
        $html[] = $title;
        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body panel-body-thumbnail text-center">';

        $html[] = $this->renderContent($result);
        $html[] = '</div>';

        if ($this instanceof TableRowActionsSupport)
        {
            $html[] = '<div class="panel-footer">';
            $html[] = $this->get_actions($result);
            $html[] = '</div>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
