<?php
namespace Chamilo\Libraries\Format\Table;

class Pager
{

    /**
     *
     * @var integer
     */
    private $numberOfItemsPerPage;

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
     * @param integer $numberOfItemsPerPage
     * @param integer $numberOfItems
     * @param integer $currentPageNumber
     */
    public function __construct($numberOfItemsPerPage, $numberOfItems, $currentPageNumber)
    {
        $this->numberOfItemsPerPage = $numberOfItemsPerPage;
        $this->numberOfItems = $numberOfItems;
        $this->currentPageNumber = $currentPageNumber;
    }

    /**
     *
     * @return integer
     */
    public function getNumberOfItemsPerPage()
    {
        return $this->numberOfItemsPerPage;
    }

    /**
     *
     * @param integer $numberOfItemsPerPage
     */
    public function setNumberOfItemsPerPage($numberOfItemsPerPage)
    {
        $this->numberOfItemsPerPage = $numberOfItemsPerPage;
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
                throw new \Exception('Invalid page number');
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
                $calculatedRangeStart = $this->getPreviousRangeEnd() + 1;

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
            $this->numberOfPages = ceil($this->getNumberOfItems() / $this->getNumberOfItemsPerPage());
        }

        return $this->numberOfPages;
    }
}