<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Table\Column\TableColumn;

/**
 * Sortable table which can be used for data available in an array
 *
 * @package Chamilo\Libraries\Format\Table
 */
class SortableTableFromArray extends SortableTable
{
    /**
     *
     * @var boolean
     */
    private $enableSorting;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    private $tableColumns;

    /**
     * The array containing all data for this table
     *
     * @var string[][]
     */
    private $tableData;

    /**
     *
     * @var string
     */
    private $tableName;

    /**
     *
     * @param string[] $tableData
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     * @param string[] $additionalParameters
     * @param integer $defaultOrderColumn
     * @param integer $defaultPerPage
     * @param integer $defaultOrderDirection
     * @param string $tableName
     * @param boolean $allowPageSelection
     * @param boolean $enableSorting
     * @param boolean $allowPageNavigation
     */
    public function __construct(
        $tableData, $tableColumns, $additionalParameters = [], $defaultOrderColumn = 1, $defaultPerPage = 20,
        $defaultOrderDirection = SORT_ASC, $tableName = 'array_table', $allowPageSelection = true,
        $enableSorting = true, $allowPageNavigation = true
    )
    {
        $this->tableName = $tableName;

        if (!$allowPageSelection || !$allowPageNavigation)
        {
            $defaultPerPage = count($tableData);
        }

        parent::__construct($tableName, array($this, 'countData'), array($this, 'getData'), $defaultOrderColumn,
            $defaultPerPage, $defaultOrderDirection, $allowPageSelection, $allowPageNavigation
        );

        $this->tableData = $tableData;
        $this->tableColumns = $tableColumns;
        $this->enableSorting = $enableSorting;

        $this->setAdditionalParameters($additionalParameters);
    }

    /**
     * Returns the complete table HTML.
     *
     * @param boolean $emptyTable
     *
     * @return string
     */
    public function render($emptyTable = false)
    {
        $this->initializeTable();

        return parent::render($emptyTable);
    }

    /**
     *
     * @param string[] $dataRow
     */
    public function addTableData($dataRow)
    {
        $this->tableData[] = $dataRow;
    }

    /**
     *
     * @return integer
     */
    public function countData()
    {
        return count($this->getTableData());
    }

    /**
     *
     * @param integer $offset
     * @param integer $count
     * @param integer[] $orderColumn
     * @param integer[] $orderDirection
     *
     * @return string[][]
     */
    public function getData($offset = null, $count = null, $orderColumn = [0], $orderDirection = [SORT_ASC])
    {
        $content = $this->getTableData();

        if ($this->getEnableSorting())
        {
            $tableSorter = new TableSort($content, $orderColumn, $orderDirection);
            $content = $tableSorter->sort();
        }

        if ($this->isPageSelectionAllowed())
        {
            $content = array_slice($content, $offset, $count);
        }

        return $content;
    }

    /**
     *
     * @return boolean
     */
    public function getEnableSorting()
    {
        return $this->enableSorting;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    public function getTableColumns()
    {
        return $this->tableColumns;
    }

    /**
     *
     * @param string[] $tableColumns
     */
    public function setTableColumns($tableColumns)
    {
        $this->tableColumns = $tableColumns;
    }

    /**
     *
     * @return string[][]
     */
    public function getTableData()
    {
        return $this->tableData;
    }

    /**
     *
     * @param string[][] $tableData
     */
    public function setTableData($tableData)
    {
        $this->tableData = $tableData;
    }

    /**
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     *
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Initialize the table
     */
    protected function initializeTable()
    {
        foreach ($this->getTableColumns() as $key => $tableColumn)
        {
            $headerAttributes = $contentAttributes = [];

            $cssClasses = $tableColumn->getCssClasses();

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]))
            {
                $headerAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER];
            }

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]))
            {
                $contentAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT];
            }

            $this->setColumnHeader(
                $key, $tableColumn->get_title(), $tableColumn->is_sortable(), $headerAttributes, $contentAttributes
            );
        }
    }

    /**
     * @param boolean $emptyTable
     *
     * @return string
     * @deprecated User render() now
     */
    public function toHtml($emptyTable = false)
    {
        return $this->render($emptyTable);
    }
}
