<?php
namespace Chamilo\Libraries\UserInterface\Table\Service;

use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\AbstractBaseTableParameters;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableParameterValues;
use Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RequestTableParameterValuesCompiler
{
    public function __construct(
        protected ChamiloRequest $request, protected PageNavigationCalculator $pageNavigationCalculator
    )
    {
    }

    /**
     * @param string[] $parameterNames
     * @param int[] $defaultParameterValues
     */
    protected function determineNumberOfRowsPerPage(array $parameterNames, array $defaultParameterValues): int
    {
        return $this->request->query->get(
            $parameterNames[TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE],
            $defaultParameterValues[TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE]
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

    /**
     * @param string[] $parameterNames
     * @param int[] $defaultParameterValues
     */
    protected function determineOrderColumnDirection(array $parameterNames, array $defaultParameterValues): int
    {
        return $this->request->query->get(
            $parameterNames[AbstractBaseTableParameters::PARAM_ORDER_COLUMN_DIRECTION],
            $defaultParameterValues[AbstractBaseTableParameters::PARAM_ORDER_COLUMN_DIRECTION]
        );
    }

    /**
     * @param string[] $parameterNames
     * @param int[] $defaultParameterValues
     */
    protected function determineOrderColumnIndex(array $parameterNames, array $defaultParameterValues): int
    {
        return $this->request->query->get(
            $parameterNames[AbstractBaseTableParameters::PARAM_ORDER_COLUMN_INDEX],
            $defaultParameterValues[AbstractBaseTableParameters::PARAM_ORDER_COLUMN_INDEX]
        );
    }

    /**
     * @param string[] $parameterNames
     */
    protected function determinePageNumber(array $parameterNames): int
    {
        return $this->request->query->get(
            $parameterNames[AbstractBaseTableParameters::PARAM_PAGE_NUMBER], 1
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

        if ($numberOfRowsPerPage == PageNavigationCalculator::DISPLAY_ALL) {
            $numberOfRowsPerPage = $totalNumberOfItems;
            $numberOfItemsPerPage = $totalNumberOfItems;
        }
        else {
            $numberOfItemsPerPage = $numberOfRowsPerPage * $numberOfColumnsPerPage;
        }

        $tableParameterValues = new TableParameterValues();

        $tableParameterValues->setTotalNumberOfItems($totalNumberOfItems);
        $tableParameterValues->setNumberOfRowsPerPage($numberOfRowsPerPage);
        $tableParameterValues->setNumberOfColumnsPerPage($numberOfColumnsPerPage);
        $tableParameterValues->setNumberOfItemsPerPage($numberOfItemsPerPage);
        $tableParameterValues->setPageNumber($pageNumber);

        $tableParameterValues->setSelectAll(
            $this->request->query->get(
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
}