<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RequestTableParameterValuesCompiler
{
    protected Pager $pager;

    protected ChamiloRequest $request;

    public function __construct(ChamiloRequest $request, Pager $pager)
    {
        $this->request = $request;
        $this->pager = $pager;
    }

    /**
     * @param string[] $parameterNames
     * @param int[] $defaultParameterValues
     */
    protected function determineNumberOfRowsPerPage(array $parameterNames, array $defaultParameterValues): int
    {
        return $this->getRequest()->query->get(
            $parameterNames[TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE],
            $defaultParameterValues[TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE]
        );
    }

    protected function determineOffset(int $pageNumber, int $numberOfItemsPerPage, int $totalNumberOfItems): int
    {
        try
        {
            return $this->getPager()->getCurrentRangeOffset(
                $pageNumber, $numberOfItemsPerPage, $totalNumberOfItems
            );
        }
        catch (InvalidPageNumberException $exception)
        {
            return 0;
        }
    }

    /**
     * @param string[] $parameterNames
     * @param int[] $defaultParameterValues
     */
    protected function determineOrderColumnDirection(array $parameterNames, array $defaultParameterValues): int
    {
        return $this->getRequest()->query->get(
            $parameterNames[TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION],
            $defaultParameterValues[TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION]
        );
    }

    /**
     * @param string[] $parameterNames
     * @param int[] $defaultParameterValues
     */
    protected function determineOrderColumnIndex(array $parameterNames, array $defaultParameterValues): int
    {
        return $this->getRequest()->query->get(
            $parameterNames[TableParameterValues::PARAM_ORDER_COLUMN_INDEX],
            $defaultParameterValues[TableParameterValues::PARAM_ORDER_COLUMN_INDEX]
        );
    }

    /**
     * @param string[] $parameterNames
     */
    protected function determinePageNumber(array $parameterNames): int
    {
        return $this->getRequest()->query->get(
            $parameterNames[TableParameterValues::PARAM_PAGE_NUMBER], 1
        );
    }

    /**
     * @param string[] $parameterNames
     */
    public function determineParameterValues(
        array $parameterNames, array $defaultParameterValues, int $totalNumberOfItems
    ): TableParameterValues
    {
        $pageNumber = $this->determinePageNumber($parameterNames);
        $numberOfColumnsPerPage = $defaultParameterValues[TableParameterValues::PARAM_NUMBER_OF_COLUMNS_PER_PAGE];
        $numberOfRowsPerPage = $this->determineNumberOfRowsPerPage($parameterNames, $defaultParameterValues);

        if ($numberOfRowsPerPage == Pager::DISPLAY_ALL)
        {
            $numberOfRowsPerPage = $totalNumberOfItems;
            $numberOfItemsPerPage = $totalNumberOfItems;
        }
        else
        {
            $numberOfItemsPerPage = $numberOfRowsPerPage * $numberOfColumnsPerPage;
        }

        $tableParameterValues = new TableParameterValues();

        $tableParameterValues->setTotalNumberOfItems($totalNumberOfItems);
        $tableParameterValues->setNumberOfRowsPerPage($numberOfRowsPerPage);
        $tableParameterValues->setNumberOfColumnsPerPage($numberOfColumnsPerPage);
        $tableParameterValues->setNumberOfItemsPerPage($numberOfItemsPerPage);
        $tableParameterValues->setPageNumber($pageNumber);

        $tableParameterValues->setSelectAll(
            $this->getRequest()->query->get(
                $parameterNames[TableParameterValues::PARAM_SELECT_ALL], 0
            )
        );

        $tableParameterValues->setOrderColumnIndex(
            $this->determineOrderColumnIndex($parameterNames, $defaultParameterValues)
        );
        $tableParameterValues->setOrderColumnDirection(
            $this->determineOrderColumnDirection($parameterNames, $defaultParameterValues)
        );
        $tableParameterValues->setOffset(
            $this->determineOffset($pageNumber, $numberOfItemsPerPage, $totalNumberOfItems)
        );

        return $tableParameterValues;
    }

    public function getPager(): Pager
    {
        return $this->pager;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }
}