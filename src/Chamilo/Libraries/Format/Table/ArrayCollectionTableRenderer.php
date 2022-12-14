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
    protected ListHtmlTableRenderer $htmlTableRenderer;

    protected Pager $pager;

    protected ChamiloRequest $request;

    public function __construct(ChamiloRequest $request, Pager $pager, ListHtmlTableRenderer $htmlTableRenderer)
    {
        $this->request = $request;
        $this->pager = $pager;
        $this->htmlTableRenderer = $htmlTableRenderer;
    }

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    public function render(
        array $tableColumns, ArrayCollection $tableData, int $defaultOrderColumnIndex = 0,
        int $defaultOrderDirection = SORT_ASC, int $defaultNumberOfItemsPerPage = 20, string $tableName = 'arrayTable'
    ): string
    {
        $parameterValues = $this->determineParameterValues(
            $tableData, $defaultOrderColumnIndex, $defaultOrderDirection, $defaultNumberOfItemsPerPage
        );

        return $this->getHtmlTableRenderer()->render(
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
            return $this->getPager()->getCurrentRangeOffset($parameterValues->getPageNumber(), $parameterValues->getNumberOfItemsPerPage(),
                $parameterValues->getTotalNumberOfItems());
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

    /**
     * @throws \Exception
     */
    public function getData(
        TableParameterValues $parameterValues, array $tableColumns, ArrayCollection $tableData
    ): ArrayCollection
    {
        if ($this->isSortable($tableColumns, $parameterValues->getOrderColumnIndex()))
        {
            $tableData = $this->sortData(
                $tableData, $parameterValues->getOrderColumnIndex(), $parameterValues->getOrderColumnDirection()
            );
        }

        return new ArrayCollection(
            $tableData->slice($this->determineOffset($parameterValues), $parameterValues->getNumberOfRowsPerPage())
        );
    }

    public function getHtmlTableRenderer(): ListHtmlTableRenderer
    {
        return $this->htmlTableRenderer;
    }

    public function getPager(): Pager
    {
        return $this->pager;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function isDateColumn(ArrayCollection $data, int $column): bool
    {
        $isDate = true;

        foreach ($data as $row)
        {
            if (strlen(strip_tags($row[$column])) != 0)
            {
                $check_date = strtotime(strip_tags($row[$column]));
                // strtotime Returns a timestamp on success, FALSE otherwise.
                // Previous to PHP 5.1.0, this function would return -1 on failure.
                $isDate &= ($check_date != - 1 && $check_date != false);
            }
            else
            {
                $isDate &= false;
            }

            if (!$isDate)
            {
                break;
            }
        }

        return $isDate;
    }

    public function isImageColumn(ArrayCollection $data, int $column): bool
    {
        $isImage = true;

        foreach ($data as $row)
        {
            $isImage &= strlen(trim(strip_tags($row[$column], '<img>'))) > 0; // at least one img-tag
            $isImage &= strlen(trim(strip_tags($row[$column]))) == 0; // and no text outside attribute-values

            if (!$isImage)
            {
                break;
            }
        }

        return $isImage;
    }

    public function isNumericColumn(ArrayCollection $data, int $column): bool
    {
        $isNumeric = true;

        foreach ($data as $row)
        {
            $isNumeric &= is_numeric(strip_tags($row[$column]));

            if (!$isNumeric)
            {
                break;
            }
        }

        return $isNumeric;
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

    /**
     * @throws \Exception
     */
    protected function sortData(ArrayCollection $data, int $ordercolumnIndex, int $orderColumnDirection
    ): ArrayCollection
    {
        if ($data->isEmpty() || !in_array($orderColumnDirection, [SORT_ASC, SORT_DESC]))
        {
            return $data;
        }

        if ($this->isImageColumn($data, $ordercolumnIndex))
        {
            $compareFunction = function ($a, $b) use ($ordercolumnIndex, $orderColumnDirection) {
                $compareResult = strnatcmp(
                    strip_tags($a[$ordercolumnIndex], '<img>'), strip_tags($b[$ordercolumnIndex], '<img>')
                );

                return $orderColumnDirection == SORT_ASC ? $compareResult > 0 : $compareResult <= 0;
            };
        }
        elseif ($this->isDateColumn($data, $ordercolumnIndex))
        {
            $compareFunction = function ($a, $b) use ($ordercolumnIndex, $orderColumnDirection) {
                $aTime = strtotime(strip_tags($a[$ordercolumnIndex]));
                $bTime = strtotime(strip_tags($b[$ordercolumnIndex]));

                return $orderColumnDirection == SORT_ASC ? $aTime > $bTime : $aTime <= $bTime;
            };
        }
        elseif ($this->isNumericColumn($data, $ordercolumnIndex))
        {
            $compareFunction = function ($a, $b) use ($ordercolumnIndex, $orderColumnDirection) {
                $aNumber = strip_tags($a[$ordercolumnIndex]);
                $bNumber = strip_tags($b[$ordercolumnIndex]);

                return $orderColumnDirection == SORT_ASC ? $aNumber > $bNumber : $aNumber <= $bNumber;
            };
        }
        else
        {
            $compareFunction = function ($a, $b) use ($ordercolumnIndex, $orderColumnDirection) {
                $compareResult = strnatcmp(
                    strip_tags($a[$ordercolumnIndex]), strip_tags($b[$ordercolumnIndex])
                );

                return $orderColumnDirection == SORT_ASC ? $compareResult > 0 : $compareResult <= 0;
            };
        }

        $iterator = $data->getIterator();
        $iterator->uasort($compareFunction);

        return new ArrayCollection($iterator->getArrayCopy());
    }
}
