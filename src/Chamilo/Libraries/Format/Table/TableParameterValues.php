<?php

namespace Chamilo\Libraries\Format\Table;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TableParameterValues
{
    public const PARAM_NUMBER_OF_COLUMNS_PER_PAGE = 'columns_per_page';
    public const PARAM_NUMBER_OF_ITEMS_PER_PAGE = 'items_per_page';
    public const PARAM_NUMBER_OF_ROWS_PER_PAGE = 'per_page';
    public const PARAM_OFFSET = 'offset';
    public const PARAM_ORDER_COLUMN_DIRECTION = 'direction';
    public const PARAM_ORDER_COLUMN_INDEX = 'column';
    public const PARAM_PAGE_NUMBER = 'page_nr';
    public const PARAM_SELECT_ALL = 'selectall';
    public const PARAM_TOTAL_NUMBER_OF_ITEMS = 'total';

    /**
     * @var int[]
     */
    protected array $parameterValues;

    /**
     * @param int[] $parameterValues
     */
    public function __construct(array $parameterValues = [])
    {
        $this->parameterValues = $parameterValues;
    }

    public function getNumberOfColumnsPerPage(): int
    {
        return $this->parameterValues[self::PARAM_NUMBER_OF_COLUMNS_PER_PAGE];
    }

    public function getNumberOfItemsPerPage(): int
    {
        return $this->parameterValues[self::PARAM_NUMBER_OF_ITEMS_PER_PAGE];
    }

    public function getNumberOfRowsPerPage(): int
    {
        return $this->parameterValues[self::PARAM_NUMBER_OF_ROWS_PER_PAGE];
    }

    public function getOffset(): int
    {
        return $this->parameterValues[self::PARAM_OFFSET];
    }

    public function getOrderColumnDirection(): int
    {
        return $this->parameterValues[self::PARAM_ORDER_COLUMN_DIRECTION];
    }

    public function getOrderColumnIndex(): int
    {
        return $this->parameterValues[self::PARAM_ORDER_COLUMN_INDEX];
    }

    public function getPageNumber(): int
    {
        return $this->parameterValues[self::PARAM_PAGE_NUMBER];
    }

    public function getParameterValues(): array
    {
        return $this->parameterValues;
    }

    public function getSelectAll(): int
    {
        return $this->parameterValues[self::PARAM_SELECT_ALL];
    }

    public function getTotalNumberOfItems(): int
    {
        return $this->parameterValues[self::PARAM_TOTAL_NUMBER_OF_ITEMS];
    }

    public function setNumberOfColumnsPerPage(int $numberOfColumnsPerPage): TableParameterValues
    {
        $this->parameterValues[self::PARAM_NUMBER_OF_COLUMNS_PER_PAGE] = $numberOfColumnsPerPage;

        return $this;
    }

    public function setNumberOfItemsPerPage(int $numberOfItemsPerPage): TableParameterValues
    {
        $this->parameterValues[self::PARAM_NUMBER_OF_ITEMS_PER_PAGE] = $numberOfItemsPerPage;

        return $this;
    }

    public function setNumberOfRowsPerPage(int $numberOfRowsPerPage): TableParameterValues
    {
        $this->parameterValues[self::PARAM_NUMBER_OF_ROWS_PER_PAGE] = $numberOfRowsPerPage;

        return $this;
    }

    public function setOffset(int $offset): TableParameterValues
    {
        $this->parameterValues[self::PARAM_OFFSET] = $offset;

        return $this;
    }

    public function setOrderColumnDirection(int $orderColumnDirection): TableParameterValues
    {
        $this->parameterValues[self::PARAM_ORDER_COLUMN_DIRECTION] = $orderColumnDirection;

        return $this;
    }

    public function setOrderColumnIndex(int $orderColumnIndex): TableParameterValues
    {
        $this->parameterValues[self::PARAM_ORDER_COLUMN_INDEX] = $orderColumnIndex;

        return $this;
    }

    public function setPageNumber(int $pageNumber): TableParameterValues
    {
        $this->parameterValues[self::PARAM_PAGE_NUMBER] = $pageNumber;

        return $this;
    }

    /**
     * @param int[] $parameterValues
     */
    public function setParameterValues(array $parameterValues): TableParameterValues
    {
        $this->parameterValues = $parameterValues;

        return $this;
    }

    public function setSelectAll(int $selectAll): TableParameterValues
    {
        $this->parameterValues[self::PARAM_SELECT_ALL] = $selectAll;

        return $this;
    }

    public function setTotalNumberOfItems(int $totalNumberOfItems): TableParameterValues
    {
        $this->parameterValues[self::PARAM_TOTAL_NUMBER_OF_ITEMS] = $totalNumberOfItems;

        return $this;
    }

}