<?php
namespace Chamilo\Libraries\Format\Table;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractBaseTableParameters
{
    public const PARAM_NUMBER_OF_ITEMS_PER_PAGE = 'items_per_page';
    public const PARAM_ORDER_COLUMN_DIRECTION = 'direction';
    public const PARAM_ORDER_COLUMN_INDEX = 'column';
    public const PARAM_PAGE_NUMBER = 'page_nr';
    public const PARAM_TOTAL_NUMBER_OF_ITEMS = 'total';

    protected array $tableParameters;

    public function __construct(array $tableParameters = [])
    {
        $this->tableParameters = $tableParameters;
    }

    public function getNumberOfItemsPerPage(): int
    {
        return $this->tableParameters[self::PARAM_NUMBER_OF_ITEMS_PER_PAGE];
    }

    public function getOrderColumnDirection(): int
    {
        return $this->tableParameters[self::PARAM_ORDER_COLUMN_DIRECTION];
    }

    public function getOrderColumnIndex(): int
    {
        return $this->tableParameters[self::PARAM_ORDER_COLUMN_INDEX];
    }

    public function getPageNumber(): int
    {
        return $this->tableParameters[self::PARAM_PAGE_NUMBER];
    }

    public function getTableParameters(): array
    {
        return $this->tableParameters;
    }

    public function getTotalNumberOfItems(): int
    {
        return $this->tableParameters[self::PARAM_TOTAL_NUMBER_OF_ITEMS];
    }

    public function setNumberOfItemsPerPage(int $numberOfItemsPerPage): AbstractBaseTableParameters
    {
        $this->tableParameters[self::PARAM_NUMBER_OF_ITEMS_PER_PAGE] = $numberOfItemsPerPage;

        return $this;
    }

    public function setOrderColumnDirection(int $orderColumnDirection): AbstractBaseTableParameters
    {
        $this->tableParameters[self::PARAM_ORDER_COLUMN_DIRECTION] = $orderColumnDirection;

        return $this;
    }

    public function setOrderColumnIndex(int $orderColumnIndex): AbstractBaseTableParameters
    {
        $this->tableParameters[self::PARAM_ORDER_COLUMN_INDEX] = $orderColumnIndex;

        return $this;
    }

    public function setPageNumber(int $pageNumber): AbstractBaseTableParameters
    {
        $this->tableParameters[self::PARAM_PAGE_NUMBER] = $pageNumber;

        return $this;
    }

    /**
     * @param int[] $tableParameters
     */
    public function setTableParameters(array $tableParameters): AbstractBaseTableParameters
    {
        $this->tableParameters = $tableParameters;

        return $this;
    }

    public function setTotalNumberOfItems(int $totalNumberOfItems): AbstractBaseTableParameters
    {
        $this->tableParameters[self::PARAM_TOTAL_NUMBER_OF_ITEMS] = $totalNumberOfItems;

        return $this;
    }

}