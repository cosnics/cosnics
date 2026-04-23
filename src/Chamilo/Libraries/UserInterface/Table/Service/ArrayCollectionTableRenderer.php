<?php
namespace Chamilo\Libraries\UserInterface\Table\Service;

use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\AbstractBaseTableParameters;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\AbstractSortableTableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableParameterValues;
use Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Sortable table which can be used for data available in an array
 *
 * @package Chamilo\Libraries\UserInterface\Table\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ArrayCollectionTableRenderer
{
    public function __construct(
        protected ChamiloRequest $request, protected PageNavigationCalculator $pageNavigationCalculator,
        protected ListHtmlTableRenderer $listHtmlTableRenderer
    )
    {
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
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

        return $this->listHtmlTableRenderer->render(
            $tableColumns, $this->getData($parameterValues, $tableColumns, $tableData), $tableName,
            $this->determineParameterNames($tableName), $parameterValues
        );
    }

    protected function determineNumberOfRowsPerPage(string $tableName, int $defaultNumberOfItemsPerPage = 20): int
    {
        return $this->request->query->get(
            $this->determineParameterName($tableName, TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE),
            $defaultNumberOfItemsPerPage
        );
    }

    protected function determineOffset(int $pageNumber, int $numberOfItemsPerPage, int $totalNumberOfItems): int
    {
        try {
            return $this->pageNavigationCalculator->getCurrentRangeOffset(
                $pageNumber, $numberOfItemsPerPage, $totalNumberOfItems
            );
        }
        catch (InvalidPageNumberException) {
            return 0;
        }
    }

    protected function determineOrderColumnDirection(string $tableName, int $defaultOrderDirection = SORT_ASC): int
    {
        return $this->request->query->get(
            $this->determineParameterName($tableName, AbstractBaseTableParameters::PARAM_ORDER_COLUMN_DIRECTION),
            $defaultOrderDirection
        );
    }

    protected function determineOrderColumnIndex(string $tableName, int $defaultOrderColumnIndex = 0): int
    {
        return $this->request->query->get(
            $this->determineParameterName($tableName, AbstractBaseTableParameters::PARAM_ORDER_COLUMN_INDEX),
            $defaultOrderColumnIndex
        );
    }

    protected function determinePageNumber(string $tableName): int
    {
        return $this->request->query->get(
            $this->determineParameterName($tableName, AbstractBaseTableParameters::PARAM_PAGE_NUMBER), 1
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
            AbstractBaseTableParameters::PARAM_ORDER_COLUMN_INDEX => $tableName . '_' .
                AbstractBaseTableParameters::PARAM_ORDER_COLUMN_INDEX,
            AbstractBaseTableParameters::PARAM_ORDER_COLUMN_DIRECTION => $tableName . '_' .
                AbstractBaseTableParameters::PARAM_ORDER_COLUMN_DIRECTION,
            AbstractBaseTableParameters::PARAM_PAGE_NUMBER => $tableName . '_' .
                AbstractBaseTableParameters::PARAM_PAGE_NUMBER
        ];
    }

    protected function determineParameterValues(
        ArrayCollection $tableData, int $defaultOrderColumnIndex = 0, int $defaultOrderColumnDirection = SORT_ASC,
        int $defaultNumberOfRowsPerPage = 20, string $tableName = 'arrayTable'
    ): TableParameterValues
    {
        $pageNumber = $this->determinePageNumber($tableName);
        $numberOfRowsPerPage = $this->determineNumberOfRowsPerPage($tableName, $defaultNumberOfRowsPerPage);
        $totalNumberOfItems = $tableData->count();

        if ($numberOfRowsPerPage == PageNavigationCalculator::DISPLAY_ALL) {
            $numberOfItemsPerPage = $totalNumberOfItems;
        }
        else {
            $numberOfItemsPerPage = $numberOfRowsPerPage;
        }

        $tableParameterValues = new TableParameterValues();

        $tableParameterValues->setTotalNumberOfItems($totalNumberOfItems);
        $tableParameterValues->setNumberOfRowsPerPage(
            $numberOfRowsPerPage == PageNavigationCalculator::DISPLAY_ALL ? $totalNumberOfItems : $numberOfRowsPerPage
        );
        $tableParameterValues->setNumberOfColumnsPerPage(1);
        $tableParameterValues->setNumberOfItemsPerPage($numberOfItemsPerPage);
        $tableParameterValues->setPageNumber($pageNumber);
        $tableParameterValues->setOrderColumnIndex(
            $this->determineOrderColumnIndex($tableName, $defaultOrderColumnIndex)
        );
        $tableParameterValues->setOrderColumnDirection(
            $this->determineOrderColumnDirection($tableName, $defaultOrderColumnDirection)
        );
        $tableParameterValues->setOffset(
            $this->determineOffset($pageNumber, $numberOfItemsPerPage, $totalNumberOfItems)
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
        if ($this->isSortable($tableColumns, $parameterValues->getOrderColumnIndex())) {
            $tableData = $this->sortData(
                $tableData, $parameterValues->getOrderColumnIndex(), $parameterValues->getOrderColumnDirection()
            );
        }

        return new ArrayCollection(
            $tableData->slice($parameterValues->getOffset(), $parameterValues->getNumberOfRowsPerPage())
        );
    }

    public function isDateColumn(ArrayCollection $data, int $column): bool
    {
        $isDate = true;

        foreach ($data as $row) {
            if (strlen(strip_tags($row[$column])) != 0) {
                $checkDate = strtotime(strip_tags($row[$column]));
                // strtotime Returns a timestamp on success, FALSE otherwise.
                // Previous to PHP 5.1.0, this function would return -1 on failure.
                $isDate &= ($checkDate != - 1 && $checkDate != false);
            }
            else {
                $isDate &= false;
            }

            if (!$isDate) {
                break;
            }
        }

        return $isDate;
    }

    public function isImageColumn(ArrayCollection $data, int $column): bool
    {
        $isImage = true;

        foreach ($data as $row) {
            $isImage &= strlen(trim(strip_tags($row[$column], '<img>'))) > 0; // at least one img-tag
            $isImage &= strlen(trim(strip_tags($row[$column]))) == 0; // and no text outside attribute-values

            if (!$isImage) {
                break;
            }
        }

        return $isImage;
    }

    public function isNumericColumn(ArrayCollection $data, int $column): bool
    {
        $isNumeric = true;

        foreach ($data as $row) {
            $isNumeric &= is_numeric(strip_tags($row[$column]));

            if (!$isNumeric) {
                break;
            }
        }

        return $isNumeric;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[] $tableColumns
     */
    protected function isSortable(array $tableColumns, int $orderColumnIndex): bool
    {
        $tableColumn = $tableColumns[$orderColumnIndex];

        if (isset($tableColumn) && $tableColumn instanceof AbstractSortableTableColumn && $tableColumn->isSortable()) {
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
        if ($data->isEmpty() || !in_array($orderColumnDirection, [SORT_ASC, SORT_DESC])) {
            return $data;
        }

        if ($this->isImageColumn($data, $ordercolumnIndex)) {
            $compareFunction = function ($a, $b) use ($ordercolumnIndex, $orderColumnDirection) {
                $compareResult = strnatcmp(
                    strip_tags($a[$ordercolumnIndex], '<img>'), strip_tags($b[$ordercolumnIndex], '<img>')
                );

                return $orderColumnDirection == SORT_ASC ? $compareResult > 0 : $compareResult <= 0;
            };
        }
        elseif ($this->isDateColumn($data, $ordercolumnIndex)) {
            $compareFunction = function ($a, $b) use ($ordercolumnIndex, $orderColumnDirection) {
                $aTime = strtotime(strip_tags($a[$ordercolumnIndex]));
                $bTime = strtotime(strip_tags($b[$ordercolumnIndex]));

                return $orderColumnDirection == SORT_ASC ? $aTime > $bTime : $aTime <= $bTime;
            };
        }
        elseif ($this->isNumericColumn($data, $ordercolumnIndex)) {
            $compareFunction = function ($a, $b) use ($ordercolumnIndex, $orderColumnDirection) {
                $aNumber = strip_tags($a[$ordercolumnIndex]);
                $bNumber = strip_tags($b[$ordercolumnIndex]);

                return $orderColumnDirection == SORT_ASC ? $aNumber > $bNumber : $aNumber <= $bNumber;
            };
        }
        else {
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
