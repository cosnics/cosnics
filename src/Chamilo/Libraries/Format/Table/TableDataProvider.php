<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This class represents a data provider for a table
 * Refactoring from ObjectTable to split between a table based on a record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableDataProvider
{
    public const DEFAULT_MAXIMUM_NUMBER_OF_RESULTS = 20;

    private TableCellRenderer $tableCellRenderer;

    private TableColumnModel $tableColumnModel;

    /**
     * @param \Chamilo\Libraries\Format\Table\TableColumnModel $tableColumnModel
     * @param \Chamilo\Libraries\Format\Table\TableCellRenderer $tableCellRenderer
     */
    public function __construct(TableColumnModel $tableColumnModel, TableCellRenderer $tableCellRenderer)
    {
        $this->tableColumnModel = $tableColumnModel;
        $this->tableCellRenderer = $tableCellRenderer;
    }

    abstract public function countData(?Condition $condition = null): int;

    protected function determineMaximumNumberOfResults(): int
    {
        return $this->getDefaultMaximumNumberofResults();
    }

    protected function determineOrderColumnIndex(): int
    {
        return $this->getTableColumnModel()->getDefaultOrderColumnIndex();
    }

    protected function determineOrderDirection(): int
    {
        return $this->getTableColumnModel()->getDefaultOrderDirection();
    }

    protected function determineOrderProperties(): OrderBy
    {
        // Calculates the order column on whether or not the table uses form actions (because sortable
        // table uses data arrays)
        $calculatedOrderColumn = $this->determineOrderColumnIndex() - ($this->hasFormActions() ? 1 : 0);

        $orderProperty =
            $this->getTableColumnModel()->getOrderProperty($calculatedOrderColumn, $this->determineOrderDirection());

        $orderProperties = [];

        if ($orderProperty)
        {
            $orderProperties[] = $orderProperty;
        }

        return new OrderBy($orderProperties);
    }

    protected function determineResultsOffset(): int
    {
        return 0;
    }

    public function getData(?Condition $condition = null): ArrayCollection
    {
        $resultSet = $this->retrieveData(
            $condition, $this->determineResultsOffset(), $this->determineMaximumNumberOfResults(),
            $this->determineOrderProperties()
        );

        $tableData = [];

        foreach ($resultSet as $result)
        {
            $this->handleResult($tableData, $result);
        }

        return new ArrayCollection($tableData);
    }

    public function getDefaultMaximumNumberofResults(): int
    {
        return static::DEFAULT_MAXIMUM_NUMBER_OF_RESULTS;
    }

    public function getTableCellRenderer(): TableCellRenderer
    {
        return $this->tableCellRenderer;
    }

    public function getTableColumnModel(): TableColumnModel
    {
        return $this->tableColumnModel;
    }

    /**
     * Handles a single result of the data and adds it to the table data
     *
     * @param string[][] $tableData
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|array $result
     */
    protected function handleResult(array &$tableData, $result)
    {
        $columnCount = $this->getTableColumnModel()->getColumnCount();

        $rowData = [];

        if ($this->hasFormActions())
        {
            $rowData[] = $this->getTableCellRenderer()->renderIdentifierCell($result);
        }

        for ($i = 0; $i < $columnCount; $i ++)
        {
            $rowData[] =
                $this->getTableCellRenderer()->renderCell($this->getTableColumnModel()->getColumn($i), $result);
        }

        $tableData[] = $rowData;
    }

    abstract public function retrieveData(
        ?Condition $condition = null, ?int $resultsOffset = null, ?int $maximumNumberOfResults = null,
        ?OrderBy $orderBy = null
    ): ArrayCollection;
}
