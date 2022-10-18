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
     * @var bool
     */
    private $enableSorting;

    /**
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
     * @var string
     */
    private $tableName;

    /**
     * @param string[] $tableData
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     * @param string[] $additionalParameters
     * @param int $defaultOrderColumn
     * @param int $defaultPerPage
     * @param int $defaultOrderDirection
     * @param string $tableName
     * @param bool $allowPageSelection
     * @param bool $enableSorting
     * @param bool $allowPageNavigation
     */
    public function __construct(
        $tableData, $tableColumns, $additionalParameters = [], $defaultOrderColumn = 1, $defaultPerPage = 20,
        $defaultOrderDirection = SORT_ASC, $tableName = 'array_table', $enableSorting = true,
        $allowPageNavigation = true
    )
    {
        $this->tableName = $tableName;

        if (!$allowPageNavigation)
        {
            $defaultPerPage = count($tableData);
        }

        parent::__construct($tableName, [$this, 'countData'], [$this, 'getData'], $defaultOrderColumn, $defaultPerPage,
            $defaultOrderDirection, $allowPageNavigation);

        $this->tableData = $tableData;
        $this->tableColumns = $tableColumns;
        $this->enableSorting = $enableSorting;

        $this->setAdditionalParameters($additionalParameters);
    }

    public function render(bool $emptyTable = false): string
    {
        $this->initializeTable();

        return parent::render($emptyTable);
    }

    /**
     * @param string[] $dataRow
     */
    public function addTableData($dataRow)
    {
        $this->tableData[] = $dataRow;
    }

    /**
     * @return int
     */
    public function countData(): int
    {
        return count($this->getTableData());
    }

    /**
     * @param int $offset
     * @param int $count
     * @param int $orderColumn
     * @param int $orderDirection
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

        return array_slice($content, $offset, $count);
    }

    /**
     * @return bool
     */
    public function getEnableSorting()
    {
        return $this->enableSorting;
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    public function getTableColumns()
    {
        return $this->tableColumns;
    }

    /**
     * @param string[] $tableColumns
     */
    public function setTableColumns($tableColumns)
    {
        $this->tableColumns = $tableColumns;
    }

    /**
     * @return string[][]
     */
    public function getTableData()
    {
        return $this->tableData;
    }

    /**
     * @param string[][] $tableData
     */
    public function setTableData($tableData)
    {
        $this->tableData = $tableData;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
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
}
