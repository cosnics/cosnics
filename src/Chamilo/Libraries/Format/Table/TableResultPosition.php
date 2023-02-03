<?php

namespace Chamilo\Libraries\Format\Table;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TableResultPosition
{
    public const PARAM_NUMBER_OF_ITEMS_PER_PAGE = 'items_per_page';
    public const PARAM_PAGE_NUMBER = 'page_nr';
    public const PARAM_POSITION = 'position';
    public const PARAM_TOTAL_NUMBER_OF_ITEMS = 'total_items';
    public const PARAM_TOTAL_NUMBER_OF_PAGES = 'total_pages';

    /**
     * @var int[]
     */
    protected array $positionValues;

    /**
     * @param int[] $positionValues
     */
    public function __construct(array $positionValues = [])
    {
        $this->positionValues = $positionValues;
    }

    public function getNumberOfItemsPerPage(): int
    {
        return $this->positionValues[self::PARAM_NUMBER_OF_ITEMS_PER_PAGE];
    }

    public function getPageNumber(): int
    {
        return $this->positionValues[self::PARAM_PAGE_NUMBER];
    }

    public function getPosition(): int
    {
        return $this->positionValues[self::PARAM_POSITION];
    }

    public function getPositionValues(): array
    {
        return $this->positionValues;
    }

    public function getTotalNumberOfItems(): int
    {
        return $this->positionValues[self::PARAM_TOTAL_NUMBER_OF_ITEMS];
    }

    public function getTotalNumberOfPages(): int
    {
        return $this->positionValues[self::PARAM_TOTAL_NUMBER_OF_PAGES];
    }

    public function isLast(): bool
    {
        return $this->isLastOnPage() && $this->isLastPage();
    }

    public function isLastOnPage(): bool
    {
        $itemCount =
            $this->getTotalNumberOfItems() < $this->getNumberOfItemsPerPage() ? $this->getTotalNumberOfItems() :
                $this->getNumberOfItemsPerPage();

        return ($this->getPosition()) + 1 <= $itemCount;
    }

    public function isLastPage(): bool
    {
        return $this->getPageNumber() >= $this->getTotalNumberOfPages();
    }

    public function setNumberOfItemsPerPage(int $numberOfItemsPerPage): TableResultPosition
    {
        $this->positionValues[self::PARAM_NUMBER_OF_ITEMS_PER_PAGE] = $numberOfItemsPerPage;

        return $this;
    }

    public function setPageNumber(int $pageNumber): TableResultPosition
    {
        $this->positionValues[self::PARAM_PAGE_NUMBER] = $pageNumber;

        return $this;
    }

    public function setPosition(int $position): TableResultPosition
    {
        $this->positionValues[self::PARAM_POSITION] = $position;

        return $this;
    }

    /**
     * @param int[] $positionValues
     */
    public function setPositionValues(array $positionValues): TableResultPosition
    {
        $this->positionValues = $positionValues;

        return $this;
    }

    public function setTotalNumberOfItems(int $totalNumberOfItems): TableResultPosition
    {
        $this->positionValues[self::PARAM_TOTAL_NUMBER_OF_ITEMS] = $totalNumberOfItems;

        return $this;
    }

    public function setTotalNumberOfPages(int $totalNumberOfPages): TableResultPosition
    {
        $this->positionValues[self::PARAM_TOTAL_NUMBER_OF_PAGES] = $totalNumberOfPages;

        return $this;
    }

}