<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class PagerRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\Pager
     */
    private $pager;

    /**
     *
     * @param \Chamilo\Libraries\Format\Table\Pager $pager
     */
    public function __construct(Pager $pager)
    {
        $this->pager = $pager;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\Pager
     */
    public function getPager()
    {
        return $this->pager;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Table\Pager $pager
     */
    public function setPager(Pager $pager)
    {
        $this->pager = $pager;
    }

    public function renderCurrentRange()
    {
        $variables = array();
        $variables['START'] = $this->getPager()->getCurrentRangeStart();
        $variables['END'] = $this->getPager()->getCurrentRangeEnd();
        $variables['TOTAL'] = $this->getPager()->getNumberOfItems();

        return Translation :: get('ShowingStartToEndOfTotalEntries', $variables, Utilities :: COMMON_LIBRARIES);
    }

    /**
     *
     * @param integer $start
     * @param integer $end
     * @return string
     */
    protected function renderPaginationBetweenStartAndEnd($start, $end, $includeRange = true)
    {
        $html = array();

        $html[] = '<nav class="pull-right">';
        $html[] = '<ul class="pagination">';

        $isFirstPage = $start == 1;

        $html[] = '<li' . ($isFirstPage ? ' class="disabled"' : '') . '>';
        $symbol = '<span aria-hidden="true">&laquo;</span>';

        if ($isFirstPage)
        {
            $html[] = $symbol;
        }
        else
        {
            $html[] = '<a href="#" aria-label="Previous">' . $symbol . '</a>';
        }

        $html[] = '</li>';

        for ($i = $start; $i <= $end; $i ++)
        {
            $html[] = '<li' . ($this->getPager()->getCurrentPageNumber() == $i ? ' class="active"' : '') .
                 '><a href="#">' . $i . '</a></li>';
        }

        $isLastPage = $end == $this->getPager()->getNumberOfPages();

        $html[] = '<li' . ($isLastPage ? ' class="disabled"' : '') . '>';
        $symbol = '<span aria-hidden="true">&raquo;</span>';

        if ($isLastPage)
        {
            $html[] = $symbol;
        }
        else
        {
            $html[] = '<a href="#" aria-label="Next">' . $symbol . '</a>';
        }

        $html[] = '</li>';

        if ($includeRange)
        {
            $html[] = '<li class="disabled">';
            $html[] = '<span>';
            $html[] = $this->renderCurrentRange();
            $html[] = '</span>';
            $html[] = '</li>';
        }

        $html[] = '</ul>';
        $html[] = '</nav>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderPagination($includeRange = true)
    {
        return $this->renderPaginationBetweenStartAndEnd(1, $this->getPager()->getNumberOfPages());
    }

    /**
     *
     * @param integer $pageLimit
     * @return string
     */
    public function renderPaginationWithPageLimit($pageLimit = 7, $includeRange = true)
    {
        $currentPageNumber = $this->getPager()->getCurrentPageNumber();

        if ($pageLimit % 2 == 0)
        {
            $itemsBefore = ceil($pageLimit / 2);
            $itemsAfter = $pageLimit - 1 - $itemsBefore;
        }
        else
        {
            $itemsBefore = $itemsAfter = ($pageLimit - 1) / 2;
        }

        $calculatedStartPage = $currentPageNumber - $itemsBefore;
        $calculatedEndPage = $currentPageNumber + $itemsAfter;

        if ($calculatedStartPage < 1 && $calculatedEndPage > $this->getPager()->getNumberOfPages())
        {
            $startPage = 1;
            $endPage = $this->getPager()->getNumberOfPages();
        }
        elseif ($calculatedStartPage < 1 && $calculatedEndPage <= $this->getPager()->getNumberOfPages())
        {
            $startPage = 1;
            $calculatedEndPage = $startPage + $pageLimit - 1;

            if ($calculatedEndPage > $this->getPager()->getNumberOfPages())
            {
                $endPage = $this->getPager()->getNumberOfPages();
            }
            else
            {
                $endPage = $calculatedEndPage;
            }
        }
        elseif ($calculatedStartPage >= 1 && $calculatedEndPage > $this->getPager()->getNumberOfPages())
        {
            $endPage = $this->getPager()->getNumberOfPages();
            $calculatedStartPage = $endPage - $pageLimit + 1;

            if ($calculatedStartPage < 1)
            {
                $startPage = 1;
            }
            else
            {
                $startPage = $calculatedStartPage;
            }
        }
        else
        {
            $startPage = $calculatedStartPage;
            $endPage = $calculatedEndPage;
        }

        return $this->renderPaginationBetweenStartAndEnd($startPage, $endPage, $includeRange);
    }
}