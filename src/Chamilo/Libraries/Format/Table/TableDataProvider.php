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
    public const DEFAULT_NUMBER_OF_RESULTS = 20;

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

    protected function determineNumberOfResults(): int
    {
        return $this->getDefaultNumberOfResults();
    }

    protected function determineOrderColumnIndex(): int
    {
        return $this->getTableColumnModel()->getDefaultOrderColumnIndex();
    }

    protected function determineOrderDirection(): int
    {
        return $this->getTableColumnModel()->getDefaultOrderDirection();
    }

    protected function determineOrderProperties(bool $hasTableActions = false): OrderBy
    {
        // Calculates the order column on whether or not the table uses form actions (because sortable
        // table uses data arrays)
        $calculatedOrderColumn = $this->determineOrderColumnIndex() - ($hasTableActions ? 1 : 0);

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

    public function getData(?Condition $condition = null, bool $hasTableActions = false): ArrayCollection
    {
        $results = $this->retrieveData(
            $condition, $this->determineResultsOffset(), $this->determineNumberOfResults(),
            $this->determineOrderProperties($hasTableActions)
        );

        $tableData = [];

        foreach ($results as $result)
        {
            $tableData[] = $this->processResult($result, $hasTableActions);
        }

        return new ArrayCollection($tableData);
    }

    public function getDefaultNumberOfResults(): int
    {
        return static::DEFAULT_NUMBER_OF_RESULTS;
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
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|array $result
     *
     * @return string[]
     */
    protected function processResult($result, bool $hasTableActions = false): array
    {
        $rowData = [];

        if ($hasTableActions)
        {
            $rowData[] = $this->getTableCellRenderer()->renderIdentifierCell($result);
        }

        foreach ($this->getTableColumnModel()->getColumns() as $column)
        {
            $rowData[] = $this->getTableCellRenderer()->renderCell($column, $result);
        }

        return $rowData;
    }

    abstract public function retrieveData(
        ?Condition $condition = null, ?int $resultsOffset = null, ?int $maximumNumberOfResults = null,
        ?OrderBy $orderBy = null
    ): ArrayCollection;
}
