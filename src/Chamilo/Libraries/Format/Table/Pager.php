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
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     */
    public function getCurrentRangeEnd(TableParameterValues $parameterValues): int
    {
        $currentRangeStart = $this->getCurrentRangeStart($parameterValues);
        $calculatedRangeEnd = $currentRangeStart + $this->getNumberOfItemsPerPage($parameterValues) - 1;

        if ($calculatedRangeEnd > $parameterValues->getTotalNumberOfItems())
        {
            return $parameterValues->getTotalNumberOfItems();
        }

        return $calculatedRangeEnd;
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     */
    public function getCurrentRangeOffset(TableParameterValues $parameterValues): int
    {
        return $this->getPreviousRangeEnd($parameterValues);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     */
    public function getCurrentRangeStart(TableParameterValues $parameterValues): int
    {
        try
        {
            $calculatedRangeStart = $this->getPreviousRangeEnd($parameterValues) + 1;
        }
        catch (InvalidPageNumberException $exception)
        {
            $calculatedRangeStart = 0;
        }

        if ($calculatedRangeStart > $parameterValues->getTotalNumberOfItems())
        {
            throw new InvalidPageNumberException();
        }

        return $calculatedRangeStart;
    }

    public function getNumberOfItemsPerPage(TableParameterValues $parameterValues): int
    {
        return $parameterValues->getNumberOfRowsPerPage() * $parameterValues->getNumberOfColumnsPerPage();
    }

    public function getNumberOfPages(TableParameterValues $parameterValues): int
    {
        if ($this->getNumberOfItemsPerPage($parameterValues) == Pager::DISPLAY_ALL)
        {
            return 1;
        }
        else
        {
            return (int) ceil(
                $parameterValues->getTotalNumberOfItems() / $this->getNumberOfItemsPerPage($parameterValues)
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     */
    public function getPreviousRangeEnd(TableParameterValues $parameterValues): int
    {
        $calculatedRangeEnd =
            ($parameterValues->getPageNumber() - 1) * $this->getNumberOfItemsPerPage($parameterValues);

        if ($calculatedRangeEnd > $parameterValues->getTotalNumberOfItems())
        {
            throw new InvalidPageNumberException();
        }

        return $calculatedRangeEnd;
    }
}