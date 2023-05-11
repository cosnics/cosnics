<?php
namespace Chamilo\Libraries\Format\Table;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TableResultPosition extends AbstractBaseTableParameters
{
    public const PARAM_POSITION = 'position';
    public const PARAM_TOTAL_NUMBER_OF_PAGES = 'total_pages';

    public function getPosition(): int
    {
        return $this->tableParameters[self::PARAM_POSITION];
    }

    public function getTotalNumberOfPages(): int
    {
        return $this->tableParameters[self::PARAM_TOTAL_NUMBER_OF_PAGES];
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

    public function setPosition(int $position): TableResultPosition
    {
        $this->tableParameters[self::PARAM_POSITION] = $position;

        return $this;
    }

    public function setTotalNumberOfPages(int $totalNumberOfPages): TableResultPosition
    {
        $this->tableParameters[self::PARAM_TOTAL_NUMBER_OF_PAGES] = $totalNumberOfPages;

        return $this;
    }

}