<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PagerRenderer
{
    const PAGE_SELECTOR_TRANSLATION_TITLE = 'title';
    const PAGE_SELECTOR_TRANSLATION_ROW = 'row';
    const PAGE_SELECTOR_TRANSLATION_TITLE_ALL = 'title_all';

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

    /**
     *
     * @return string
     */
    public function renderCurrentRange()
    {
        $variables = array();
        $variables['START'] = $this->getPager()->getCurrentRangeStart();
        $variables['END'] = $this->getPager()->getCurrentRangeEnd();
        $variables['TOTAL'] = $this->getPager()->getNumberOfItems();

        return Translation::get('ShowingStartToEndOfTotalEntries', $variables, Utilities::COMMON_LIBRARIES);
    }

    /**
     *
     * @param boolean $isDisabled
     * @param string $symbol
     * @param string $translation
     * @param unknown $target
     *
     * @return string
     */
    protected function renderDirectionPaginationItem($queryParameters, $pageNumberParameterName, $isDisabled, $symbol,
        $translation, $targetPage = null)
    {
        $html = array();

        $html[] = '<li' . ($isDisabled ? ' class="disabled"' : '') . '>';
        $symbolHtml = '<span aria-hidden="true">' . $symbol . '</span>';

        if ($isDisabled)
        {
            $html[] = $symbolHtml;
        }
        else
        {
            $html[] = '<a href="' . $this->getUrl($queryParameters, $pageNumberParameterName, $targetPage) .
                 '" aria-label="' . $translation . '">' . $symbolHtml . '</a>';
        }

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param integer $start
     * @param integer $end
     * @param boolean $includeRange
     *
     * @return string
     */
    protected function renderPaginationBetweenStartAndEnd($queryParameters, $pageNumberParameterName, $start, $end,
        $includeRange = true)
    {
        $html = array();

        $html[] = '<nav class="pull-right">';
        $html[] = '<ul class="pagination">';

        if ($this->getPager()->getNumberOfPages() > 1)
        {

            $isDisabled = ($this->getPager()->getCurrentPageNumber() == 1);

            $html[] = $this->renderDirectionPaginationItem(
                $queryParameters,
                $pageNumberParameterName,
                $isDisabled,
                '&laquo;',
                Translation::get('First'),
                1);

            $html[] = $this->renderDirectionPaginationItem(
                $queryParameters,
                $pageNumberParameterName,
                $isDisabled,
                '&lsaquo;',
                Translation::get('Previous'),
                $this->getPager()->getCurrentPageNumber() - 1);

            for ($i = $start; $i <= $end; $i ++)
            {
                $html[] = '<li' . ($this->getPager()->getCurrentPageNumber() == $i ? ' class="active"' : '') .
                     '><a href="' . $this->getUrl($queryParameters, $pageNumberParameterName, $i) . '">' . $i .
                     '</a></li>';
            }

            $isDisabled = ($this->getPager()->getCurrentPageNumber() == $this->getPager()->getNumberOfPages());

            $html[] = $this->renderDirectionPaginationItem(
                $queryParameters,
                $pageNumberParameterName,
                $isDisabled,
                '&rsaquo;',
                Translation::get('Next'),
                $this->getPager()->getCurrentPageNumber() + 1);

            $html[] = $this->renderDirectionPaginationItem(
                $queryParameters,
                $pageNumberParameterName,
                $isDisabled,
                '&raquo;',
                Translation::get('Last'),
                $this->getPager()->getNumberOfPages());
        }

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
     * @param string[] $queryParameters
     * @param string $pageNumberParameterName
     * @param boolean $includeRange
     *
     * @return string
     */
    public function renderPagination($queryParameters = array(), $pageNumberParameterName = 'page_nr', $includeRange = true)
    {
        return $this->renderPaginationBetweenStartAndEnd(
            $queryParameters,
            $pageNumberParameterName,
            1,
            $this->getPager()->getNumberOfPages(),
            $includeRange);
    }

    /**
     *
     * @param string[] $queryParameters
     * @param string $itemsPerPageParameterName
     * @param integer $pageLimit
     * @param boolean $includeRange
     *
     * @return string
     */
    public function renderPaginationWithPageLimit($queryParameters = array(), $pageNumberParameterName = 'page_nr', $pageLimit = 7,
        $includeRange = true)
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

        return $this->renderPaginationBetweenStartAndEnd(
            $queryParameters,
            $pageNumberParameterName,
            $startPage,
            $endPage,
            $includeRange);
    }

    /**
     *
     * @param string[] $queryParameters
     * @param string $itemsPerPageParameterName
     *
     * @return string
     */
    public function renderItemsPerPageSelector($queryParameters, $itemsPerPageParameterName,
        $translationVariables = array())

    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();
        $buttonToolBar->addButtonGroup($buttonGroup);

        $defaultTranslationVariables[Application::PARAM_CONTEXT] = Utilities::COMMON_LIBRARIES;
        $defaultTranslationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE] = 'ShowNumberOfItemsPerPage';
        $defaultTranslationVariables[self::PAGE_SELECTOR_TRANSLATION_ROW] = 'ShowNumberOfItemsPerPage';
        $defaultTranslationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE_ALL] = 'ShowAllItems';

        $translationVariables = array_merge($defaultTranslationVariables, $translationVariables);

        $currentNumberOfRowsPerPage = $this->getPager()->getNumberOfRows();
        $numberOfItems = $this->getPager()->getNumberOfItems();

        if ($this->getPager()->getNumberOfItemsPerPage() >= $this->getPager()->getNumberOfItems())
        {
            $dropDownButtonLabel = Translation::get(
                $translationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE_ALL],
                null,
                $translationVariables[Application::PARAM_CONTEXT]);
        }
        else
        {
            $dropDownButtonLabel = Translation::get(
                $translationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE],
                array('NUMBER' => $this->getPager()->getNumberOfItemsPerPage()),
                $translationVariables[Application::PARAM_CONTEXT]);
        }

        $dropDownButton = new DropdownButton($dropDownButtonLabel, null, Button::DISPLAY_LABEL, 'btn-sm');

        $dropDownButton->setDropdownClasses('dropdown-menu-right');
        $buttonGroup->addButton($dropDownButton);

        for ($nr = Pager::DISPLAY_PER_INCREMENT; $nr <= $numberOfItems && $nr <= 100; $nr += Pager::DISPLAY_PER_INCREMENT)
        {
            $nrOfRows = ($nr / $this->getPager()->getNumberOfColumns());

            $dropDownButton->addSubButton(
                new SubButton(
                    Translation::get(
                        $translationVariables[self::PAGE_SELECTOR_TRANSLATION_ROW],
                        array('NUMBER' => $nr),
                        $translationVariables[Application::PARAM_CONTEXT]),
                    null,
                    $this->getUrl($queryParameters, $itemsPerPageParameterName, $nrOfRows),
                    SubButton::DISPLAY_LABEL,
                    false,
                    ($nrOfRows == $currentNumberOfRowsPerPage ? 'selected' : 'not-selected')));
        }

        if ($numberOfItems < Pager::DISPLAY_PER_PAGE_LIMIT)
        {
            $dropDownButton->addSubButton(
                new SubButton(
                    Translation::get(
                        $translationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE_ALL],
                        null,
                        $translationVariables[Application::PARAM_CONTEXT]),
                    null,
                    $this->getUrl($queryParameters, $itemsPerPageParameterName, Pager::DISPLAY_ALL),
                    SubButton::DISPLAY_LABEL,
                    false,
                    ($this->getPager()->getNumberOfItemsPerPage() == $this->getPager()->getNumberOfItems() ? ' selected' : 'not-selected')));
        }

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        $html = array();

        $html[] = '<div class="pull-right">';
        $html[] = $buttonToolBarRenderer->render();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string[] $queryParameters
     * @param string $variableName
     * @param string $variableValue
     *
     * @return string
     */
    protected function getUrl($queryParameters, $variableName, $variableValue)
    {
        $queryParameters[$variableName] = $variableValue;
        $action = new Redirect($queryParameters);

        return $action->getUrl();
    }
}