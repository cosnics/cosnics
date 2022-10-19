<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Pager
{
    public const DISPLAY_ALL = 'all';
    public const DISPLAY_PER_INCREMENT = 20;
    public const DISPLAY_PER_PAGE_LIMIT = 500;

    /**
     * @throws \Exception
     */
    public function getCurrentRangeEnd(
        int $currentPageNumber, int $numberOfRows, int $numberOfColumns, int $numberOfItems
    ): int
    {
        $currentRangeStart =
            $this->getCurrentRangeStart($currentPageNumber, $numberOfRows, $numberOfColumns, $numberOfItems);
        $calculatedRangeEnd = $currentRangeStart + $this->getNumberOfItemsPerPage($numberOfRows, $numberOfColumns) - 1;

        if ($calculatedRangeEnd > $numberOfItems)
        {
            return $numberOfItems;
        }

        return $calculatedRangeEnd;
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     */
    public function getCurrentRangeOffset(
        int $currentPageNumber, int $numberOfRows, int $numberOfColumns, int $numberOfItems
    ): int
    {
        return $this->getPreviousRangeEnd($currentPageNumber, $numberOfRows, $numberOfColumns, $numberOfItems);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Exception
     */
    public function getCurrentRangeStart(
        int $currentPageNumber, int $numberOfRows, int $numberOfColumns, int $numberOfItems
    ): int
    {
        try
        {
            $calculatedRangeStart =
                $this->getPreviousRangeEnd($currentPageNumber, $numberOfRows, $numberOfColumns, $numberOfItems) + 1;
        }
        catch (InvalidPageNumberException $exception)
        {
            $calculatedRangeStart = 0;
        }

        if ($calculatedRangeStart > $numberOfItems)
        {
            throw new InvalidPageNumberException();
        }

        return $calculatedRangeStart;
    }

    public function getNumberOfItemsPerPage(int $numberOfRows, int $numberOfColumns): int
    {
        return $numberOfRows * $numberOfColumns;
    }

    public function getNumberOfPages(int $numberOfRows, int $numberOfColumns, int $numberOfItems): int
    {
        if ($this->getNumberOfItemsPerPage($numberOfRows, $numberOfColumns) == Pager::DISPLAY_ALL)
        {
            return 1;
        }
        else
        {
            return (int) ceil($numberOfItems / $this->getNumberOfItemsPerPage($numberOfRows, $numberOfColumns));
        }
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     */
    public function getPreviousRangeEnd(
        int $currentPageNumber, int $numberOfRows, int $numberOfColumns, int $numberOfItems
    ): int
    {
        $calculatedRangeEnd =
            ($currentPageNumber - 1) * $this->getNumberOfItemsPerPage($numberOfRows, $numberOfColumns);

        if ($calculatedRangeEnd > $numberOfItems)
        {
            throw new InvalidPageNumberException();
        }

        return $calculatedRangeEnd;
    }
}