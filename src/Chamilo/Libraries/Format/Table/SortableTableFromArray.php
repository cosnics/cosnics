<?php
namespace Chamilo\Libraries\Format\Table;

/**
 * Sortable table which can be used for data available in an array
 */
class SortableTableFromArray extends SortableTable
{

    /**
     * The array containing all data for this table
     *
     * @var multitype:multitype
     */
    private $tableData;

    /**
     *
     * @var boolean
     */
    private $enableSorting;

    /**
     * Constructor
     *
     * @param $table_data array
     * @param $default_column int
     * @param $default_items_per_page int
     */
    public function __construct($tableData, $defaultOrderColumn = 1, $defaultPerPage = 20, $tableName = 'tablename',
        $defaultOrderDirection = SORT_ASC, $allowPageSelection = true, $enableSorting = true, $allowPageNavigation = true)
    {
        $this->tableData = $tableData;
        $this->enableSorting = $enableSorting;

        if (! $allowPageSelection)
        {
            $defaultPerPage = count($tableData);
        }

        parent :: __construct(
            $tableName,
            array($this, 'countData'),
            array($this, 'getData'),
            $defaultOrderColumn,
            $defaultPerPage,
            $defaultOrderDirection,
            $allowPageSelection,
            $allowPageNavigation);
    }

    public function getEnableSorting()
    {
        return $this->enableSorting;
    }

    /**
     * Get table data to show on current page
     *
     * @see SortableTable#get_table_data
     */
    public function getData($from = 1)
    {
        $content = $this->getTableData();

        if ($this->getEnableSorting())
        {
            $content = TableSort :: sort_table($content, $this->getOrderColumn(), $this->getOrderDirection());
        }

        if ($this->isPageSelectionAllowed())
        {
            $content = array_slice($content, $from, $this->getNumberOfItemsPerPage());
        }

        return $content;
    }

    /**
     *
     * @return multitype:multitype
     */
    public function getTableData()
    {
        return $this->tableData;
    }

    /**
     *
     * @param $table_data multitype:multitype
     */
    public function setTableData($tableData)
    {
        $this->tableData = $tableData;
    }

    /**
     *
     * @param $data_row multitype:mixed
     */
    public function addTableData($dataRow)
    {
        $this->tableData[] = $dataRow;
    }

    /**
     * Get total number of items
     *
     * @see SortableTable#get_total_number_of_items
     */
    public function countData()
    {
        return count($this->getTableData());
    }

    public function getFrom()
    {
        $pager = $this->getPager();
        $offset = $pager->getOffsetByPageId();
        return $offset[0] - 1;
    }
}
