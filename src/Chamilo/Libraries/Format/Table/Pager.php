<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException;

/**
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Pager
{
    const DISPLAY_PER_PAGE_LIMIT = 500;
    const DISPLAY_PER_INCREMENT = 20;
    const DISPLAY_PER_INCREMENT_INTERVAL_LIMIT = 60;
    const DISPLAY_ALL = 'all';

    /**
     *
     * @var integer
     */
    private $numberOfRows;

    /**
     *
     * @var integer
     */
    private $numberOfColumns;

    /**
     *
     * @var integer
     */
    private $numberOfItems;

    /**
     *
     * @var integer
     */
    private $currentPageNumber;

    /**
     *
     * @var integer
     */
    private $previousRangeEnd;

    /**
     *
     * @var integer
     */
    private $currentRangeStart;

    /**
     *
     * @var integer
     */
    private $currentRangeEnd;

    /**
     *
     * @var integer
     */
    private $numberOfPages;

    /**
     *
     * @param integer $numberOfRows
     * @param integer $numberOfColumns
     * @param integer $numberOfItems
     * @param integer $currentPageNumber
     */
    public function __construct($numberOfRows, $numberOfColumns, $numberOfItems, $currentPageNumber)
    {
        $this->numberOfRows = $numberOfRows;
        $this->numberOfColumns = $numberOfColumns;
        $this->numberOfItems = $numberOfItems;
        $this->currentPageNumber = $currentPageNumber;
    }

    /**
     *
     * @return integer
     */
    public function getNumberOfRows()
    {
        return $this->numberOfRows;
    }

    /**
     *
     * @param integer $numberOfRows
     */
    public function setNumberOfRows($numberOfRows)
    {
        $this->numberOfRows = $numberOfRows;
    }

    /**
     *
     * @return integer
     */
    public function getNumberOfColumns()
    {
        return $this->numberOfColumns;
    }

    /**
     *
     * @param integer $numberOfColumns
     */
    public function setNumberOfColumns($numberOfColumns)
    {
        $this->numberOfColumns = $numberOfColumns;
    }

    /**
     *
     * @return integer
     */
    public function getNumberOfItems()
    {
        return $this->numberOfItems;
    }

    /**
     *
     * @param integer $numberOfItems
     */
    public function setNumberOfItems($numberOfItems)
    {
        $this->numberOfItems = $numberOfItems;
    }

    /**
     *
     * @return integer
     */
    public function getCurrentPageNumber()
    {
        return $this->currentPageNumber;
    }

    /**
     *
     * @param integer $currentPageNumber
     */
    public function setCurrentPageNumber($currentPageNumber)
    {
        $this->currentPageNumber = $currentPageNumber;
    }

    /**
     *
     * @return integer
     */
    public function getNumberOfItemsPerPage()
    {
        return $this->getNumberOfRows() * $this->getNumberOfColumns();
    }

    /**
     *
     * @throws \Exception
     * @return integer
     */
    public function getPreviousRangeEnd()
    {
        if (! isset($this->previousRangeEnd))
        {
            $calculatedRangeEnd = ($this->getCurrentPageNumber() - 1) * $this->getNumberOfItemsPerPage();
            
            if ($calculatedRangeEnd > $this->getNumberOfItems())
            {
                throw new InvalidPageNumberException();
            }
            
            $this->previousRangeEnd = $calculatedRangeEnd;
        }
        
        return $this->previousRangeEnd;
    }

    /**
     *
     * @throws \Exception
     * @return integer
     */
    public function getCurrentRangeStart()
    {
        try
        {
            if (! isset($this->currentRangeStart))
            {
                try {
                    $calculatedRangeStart = $this->getPreviousRangeEnd() + 1;
                } catch (InvalidPageNumberException $exception) {
                    $calculatedRangeStart = 0;
                }
                
                if ($calculatedRangeStart > $this->getNumberOfItems())
                {
                    throw new \Exception('Invalid page number');
                }
                
                $this->currentRangeStart = $calculatedRangeStart;
            }
            
            return $this->currentRangeStart;
        }
        catch (\Exception $exception)
        {
            throw $exception;
        }
    }

    /**
     *
     * @return integer
     */
    public function getCurrentRangeOffset()
    {
        return $this->getPreviousRangeEnd();
    }

    /**
     *
     * @throws Exception
     * @return integer
     */
    public function getCurrentRangeEnd()
    {
        try
        {
            if (! isset($this->currentRangeEnd))
            {
                $currentRangeStart = $this->getCurrentRangeStart();
                $calculatedRangeEnd = $currentRangeStart + $this->getNumberOfItemsPerPage() - 1;
                
                if ($calculatedRangeEnd > $this->getNumberOfItems())
                {
                    return $this->getNumberOfItems();
                }
                
                $this->currentRangeEnd = $calculatedRangeEnd;
            }
            
            return $this->currentRangeEnd;
        }
        catch (\Exception $exception)
        {
            throw $exception;
        }
    }

    /**
     *
     * @return integer
     */
    public function getNumberOfPages()
    {
        if (! isset($this->numberOfPages))
        {
            if ($this->getNumberOfItemsPerPage() == Pager::DISPLAY_ALL)
            {
                $this->numberOfPages = 1;
            }
            else
            {
                $this->numberOfPages = ceil($this->getNumberOfItems() / $this->getNumberOfItemsPerPage());
            }
        }
        
        return $this->numberOfPages;
    }
}