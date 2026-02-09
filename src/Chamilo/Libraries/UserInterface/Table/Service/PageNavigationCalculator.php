<?php
namespace Chamilo\Libraries\UserInterface\Table\Service;

use Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PageNavigationCalculator
{
    public const DISPLAY_ALL = 'all';
    public const DISPLAY_PER_INCREMENT = 20;
    public const DISPLAY_PER_PAGE_LIMIT = 500;

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     */
    public function getCurrentRangeEnd(int $pageNumber, int $numberOfItemsPerPage, int $totalNumberOfItems): int
    {
        $currentRangeStart = $this->getCurrentRangeStart($pageNumber, $numberOfItemsPerPage, $totalNumberOfItems);
        $calculatedRangeEnd = $currentRangeStart + $numberOfItemsPerPage - 1;

        if ($calculatedRangeEnd > $totalNumberOfItems) {
            return $totalNumberOfItems;
        }

        return $calculatedRangeEnd;
    }

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     */
    public function getCurrentRangeOffset(int $pageNumber, int $numberOfItemsPerPage, int $totalNumberOfItems): int
    {
        return $this->getPreviousRangeEnd($pageNumber, $numberOfItemsPerPage, $totalNumberOfItems);
    }

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     */
    public function getCurrentRangeStart(int $pageNumber, int $numberOfItemsPerPage, int $totalNumberOfItems): int
    {
        try {
            $calculatedRangeStart =
                $this->getPreviousRangeEnd($pageNumber, $numberOfItemsPerPage, $totalNumberOfItems) + 1;
        }
        catch (InvalidPageNumberException) {
            $calculatedRangeStart = 0;
        }

        if ($calculatedRangeStart > $totalNumberOfItems) {
            throw new InvalidPageNumberException();
        }

        return $calculatedRangeStart;
    }

    public function getNumberOfPages(int $numberOfItemsPerPage, int $totalNumberOfItems): int
    {
        if ($numberOfItemsPerPage == PageNavigationCalculator::DISPLAY_ALL) {
            return 1;
        }
        else {
            return (int) ceil(
                $totalNumberOfItems / $numberOfItemsPerPage
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     */
    public function getPreviousRangeEnd(int $pageNumber, int $numberOfItemsPerPage, int $totalNumberOfItems): int
    {
        $calculatedRangeEnd = ($pageNumber - 1) * $numberOfItemsPerPage;

        if ($calculatedRangeEnd > $totalNumberOfItems) {
            throw new InvalidPageNumberException();
        }

        return $calculatedRangeEnd;
    }
}