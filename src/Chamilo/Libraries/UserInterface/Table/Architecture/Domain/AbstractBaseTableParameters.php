<?php
namespace Chamilo\Libraries\UserInterface\Table\Architecture\Domain;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractBaseTableParameters extends ParameterBag
{
    public const PARAM_NUMBER_OF_ITEMS_PER_PAGE = 'items_per_page';
    public const PARAM_ORDER_COLUMN_DIRECTION = 'direction';
    public const PARAM_ORDER_COLUMN_INDEX = 'column';
    public const PARAM_PAGE_NUMBER = 'page_nr';
    public const PARAM_TOTAL_NUMBER_OF_ITEMS = 'total';

    public function getNumberOfItemsPerPage(): int
    {
        return $this->get(self::PARAM_NUMBER_OF_ITEMS_PER_PAGE);
    }

    public function getOrderColumnDirection(): int
    {
        return $this->get(self::PARAM_ORDER_COLUMN_DIRECTION);
    }

    public function getOrderColumnIndex(): int
    {
        return $this->get(self::PARAM_ORDER_COLUMN_INDEX);
    }

    public function getPageNumber(): int
    {
        return $this->get(self::PARAM_PAGE_NUMBER);
    }

    public function getTotalNumberOfItems(): int
    {
        return $this->get(self::PARAM_TOTAL_NUMBER_OF_ITEMS);
    }

    public function setNumberOfItemsPerPage(int $numberOfItemsPerPage): AbstractBaseTableParameters
    {
        $this->set(self::PARAM_NUMBER_OF_ITEMS_PER_PAGE, $numberOfItemsPerPage);

        return $this;
    }

    public function setOrderColumnDirection(int $orderColumnDirection): AbstractBaseTableParameters
    {
        $this->set(self::PARAM_ORDER_COLUMN_DIRECTION, $orderColumnDirection);

        return $this;
    }

    public function setOrderColumnIndex(int $orderColumnIndex): AbstractBaseTableParameters
    {
        $this->set(self::PARAM_ORDER_COLUMN_INDEX, $orderColumnIndex);

        return $this;
    }

    public function setPageNumber(int $pageNumber): AbstractBaseTableParameters
    {
        $this->set(self::PARAM_PAGE_NUMBER, $pageNumber);

        return $this;
    }

    public function setTotalNumberOfItems(int $totalNumberOfItems): AbstractBaseTableParameters
    {
        $this->set(self::PARAM_TOTAL_NUMBER_OF_ITEMS, $totalNumberOfItems);

        return $this;
    }
}