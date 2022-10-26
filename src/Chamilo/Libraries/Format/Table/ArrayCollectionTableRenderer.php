<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Table\Column\AbstractSortableTableColumn;
use Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Sortable table which can be used for data available in an array
 *
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ArrayCollectionTableRenderer
{
    protected Pager $pager;

    protected ChamiloRequest $request;

    protected SortableTable $sortableTable;

    public function __construct(
        ChamiloRequest $request, Pager $pager, SortableTable $sortableTable
    )
    {
        $this->request = $request;
        $this->pager = $pager;
        $this->sortableTable = $sortableTable;
    }

    public function render(
        array $tableColumns, ArrayCollection $tableData, int $defaultOrderColumnIndex = 0,
        int $defaultOrderDirection = SORT_ASC, int $defaultNumberOfItemsPerPage = 20, string $tableName = 'arrayTable'
    ): string
    {
        $parameterValues = $this->determineParameterValues(
            $tableData, $defaultOrderColumnIndex, $defaultOrderDirection, $defaultNumberOfItemsPerPage
        );

        return $this->getSortableTable()->render(
            $tableColumns, $this->getData($parameterValues, $tableColumns, $tableData), $tableName,
            $this->determineParameterNames($tableName), $parameterValues
        );
    }

    protected function determineNumberOfRowsPerPage(string $tableName, int $defaultNumberOfItemsPerPage = 20): int
    {
        return $this->getRequest()->query->get(
            $this->determineParameterName($tableName, TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE),
            $defaultNumberOfItemsPerPage
        );
    }

    protected function determineOffset(TableParameterValues $parameterValues): int
    {
        try
        {
            return $this->getPager()->getCurrentRangeOffset($parameterValues);
        }
        catch (InvalidPageNumberException $exception)
        {
            return 0;
        }
    }

    protected function determineOrderColumnDirection(string $tableName, int $defaultOrderDirection = SORT_ASC): int
    {
        return $this->getRequest()->query->get(
            $this->determineParameterName($tableName, TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION),
            $defaultOrderDirection
        );
    }

    protected function determineOrderColumnIndex(string $tableName, int $defaultOrderColumnIndex = 0): int
    {
        return $this->getRequest()->query->get(
            $this->determineParameterName($tableName, TableParameterValues::PARAM_ORDER_COLUMN_INDEX),
            $defaultOrderColumnIndex
        );
    }

    protected function determinePageNumber(string $tableName): int
    {
        return $this->getRequest()->query->get(
            $this->determineParameterName($tableName, TableParameterValues::PARAM_PAGE_NUMBER), 1
        );
    }

    protected function determineParameterName(string $tableName, string $parameterName): string
    {
        return $this->determineParameterNames($tableName)[$parameterName];
    }

    /**
     * @return string[]
     */
    protected function determineParameterNames(string $tableName): array
    {
        return [
            TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE => $tableName . '_' .
                TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE,
            TableParameterValues::PARAM_ORDER_COLUMN_INDEX => $tableName . '_' .
                TableParameterValues::PARAM_ORDER_COLUMN_INDEX,
            TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION => $tableName . '_' .
                TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION,
            TableParameterValues::PARAM_PAGE_NUMBER => $tableName . '_' . TableParameterValues::PARAM_PAGE_NUMBER
        ];
    }

    protected function determineParameterValues(
        ArrayCollection $tableData, int $defaultOrderColumnIndex = 0, int $defaultOrderColumnDirection = SORT_ASC,
        int $defaultNumberOfRowsPerPage = 20, string $tableName = 'arrayTable'
    ): TableParameterValues
    {
        $numberOfRowsPerPage = $this->determineNumberOfRowsPerPage($tableName, $defaultNumberOfRowsPerPage);
        $totalNumberOfItems = $tableData->count();

        $tableParameterValues = new TableParameterValues();

        $tableParameterValues->setTotalNumberOfItems($totalNumberOfItems);
        $tableParameterValues->setNumberOfRowsPerPage(
            $numberOfRowsPerPage == Pager::DISPLAY_ALL ? $totalNumberOfItems : $numberOfRowsPerPage
        );
        $tableParameterValues->setNumberOfColumnsPerPage(1);
        $tableParameterValues->setPageNumber($this->determinePageNumber($tableName));
        $tableParameterValues->setOrderColumnIndex(
            $this->determineOrderColumnIndex($tableName, $defaultOrderColumnIndex)
        );
        $tableParameterValues->setOrderColumnDirection(
            $this->determineOrderColumnDirection($tableName, $defaultOrderColumnDirection)
        );

        return $tableParameterValues;
    }

    public function getData(
        TableParameterValues $parameterValues, array $tableColumns, ArrayCollection $tableData
    ): ArrayCollection
    {
        if ($this->isSortable($tableColumns, $parameterValues->getOrderColumnIndex()))
        {
            $tableSorter = new TableSort(
                $tableData->toArray(), $parameterValues->getOrderColumnIndex(),
                $parameterValues->getOrderColumnDirection()
            );

            $tableData = new ArrayCollection($tableSorter->sort());
        }

        return new ArrayCollection(
            $tableData->slice($this->determineOffset($parameterValues), $parameterValues->getNumberOfRowsPerPage())
        );
    }

    public function getPager(): Pager
    {
        return $this->pager;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getSortableTable(): SortableTable
    {
        return $this->sortableTable;
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     */
    protected function isSortable(array $tableColumns, int $orderColumnIndex): bool
    {
        $tableColumn = $tableColumns[$orderColumnIndex];

        if (isset($tableColumn) && $tableColumn instanceof AbstractSortableTableColumn && $tableColumn->is_sortable())
        {
            return true;
        }

        return false;
    }
}
