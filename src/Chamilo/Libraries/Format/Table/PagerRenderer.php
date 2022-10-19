<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PagerRenderer
{
    public const PAGE_SELECTOR_TRANSLATION_ROW = 'row';
    public const PAGE_SELECTOR_TRANSLATION_TITLE = 'title';
    public const PAGE_SELECTOR_TRANSLATION_TITLE_ALL = 'title_all';

    protected Pager $pager;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(Translator $translator, Pager $pager, UrlGenerator $urlGenerator)
    {
        $this->translator = $translator;
        $this->pager = $pager;
        $this->urlGenerator = $urlGenerator;
    }

    public function getPager(): Pager
    {
        return $this->pager;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Exception
     */
    public function renderCurrentRange(
        int $currentPageNumber, int $numberOfRows, int $numberOfColumns, int $numberOfItems
    ): string
    {
        $pager = $this->getPager();
        $variables = [];

        $variables['{START}'] =
            $pager->getCurrentRangeStart($currentPageNumber, $numberOfRows, $numberOfColumns, $numberOfItems);
        $variables['{END}'] =
            $pager->getCurrentRangeEnd($currentPageNumber, $numberOfRows, $numberOfColumns, $numberOfItems);
        $variables['{TOTAL}'] = $numberOfItems;

        return $this->getTranslator()->trans('ShowingStartToEndOfTotalEntries', $variables, StringUtilities::LIBRARIES);
    }

    protected function renderDirectionPaginationItem(
        array $queryParameters, string $pageNumberParameterName, bool $isDisabled, InlineGlyph $inlineGlyph,
        string $translation, ?int $targetPage = null
    ): string
    {
        $html = [];

        $html[] = '<li' . ($isDisabled ? ' class="disabled"' : '') . '>';
        $symbolHtml = '<span aria-hidden="true">' . $inlineGlyph->render() . '</span>';

        if ($isDisabled)
        {
            $html[] = $symbolHtml;
        }
        else
        {
            $html[] = '<a href="' .
                $this->getUrlGenerator()->fromRequest(/*$queryParameters,*/ [$pageNumberParameterName => $targetPage]) .
                '" aria-label="' . $translation . '">' . $symbolHtml . '</a>';
        }

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string[] $queryParameters
     * @param string $itemsPerPageParameterName
     * @param string[] $translationVariables
     *
     * @return string
     */
    public function renderItemsPerPageSelector(
        int $numberOfItems, int $numberOfRows, int $numberOfColumns, array $queryParameters,
        string $itemsPerPageParameterName, array $translationVariables = []
    ): string
    {

        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();
        $buttonToolBar->addButtonGroup($buttonGroup);
        $pager = $this->getPager();
        $translator = $this->getTranslator();

        $defaultTranslationVariables[Application::PARAM_CONTEXT] = StringUtilities::LIBRARIES;
        $defaultTranslationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE] = 'ShowNumberOfItemsPerPage';
        $defaultTranslationVariables[self::PAGE_SELECTOR_TRANSLATION_ROW] = 'ShowNumberOfItemsPerPage';
        $defaultTranslationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE_ALL] = 'ShowAllItems';

        $translationVariables = array_merge($defaultTranslationVariables, $translationVariables);

        $numberOfItemsPerPage = $pager->getNumberOfItemsPerPage($numberOfRows, $numberOfColumns);

        if ($numberOfItemsPerPage >= $numberOfItems)
        {
            $dropDownButtonLabel = $translator->trans(
                $translationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE_ALL], [],
                $translationVariables[Application::PARAM_CONTEXT]
            );
        }
        else
        {
            $dropDownButtonLabel = $translator->trans(
                $translationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE], ['{NUMBER}' => $numberOfItemsPerPage],
                $translationVariables[Application::PARAM_CONTEXT]
            );
        }

        $dropDownButton =
            new DropdownButton($dropDownButtonLabel, null, Button::DISPLAY_LABEL, ['btn-sm'], ['dropdown-menu-right']);
        $buttonGroup->addButton($dropDownButton);

        for (
            $nr = Pager::DISPLAY_PER_INCREMENT; $nr <= $numberOfItems && $nr <= 100; $nr += Pager::DISPLAY_PER_INCREMENT
        )
        {
            $numberrOfRowsOption = ($nr / $numberOfColumns);

            $dropDownButton->addSubButton(
                new SubButton(
                    $translator->trans(
                        $translationVariables[self::PAGE_SELECTOR_TRANSLATION_ROW], ['{NUMBER}' => $nr],
                        $translationVariables[Application::PARAM_CONTEXT]
                    ), null, $this->getUrlGenerator()->fromRequest(/*$queryParameters,*/
                    [$itemsPerPageParameterName => $numberrOfRowsOption]
                ), SubButton::DISPLAY_LABEL, null, [], null, $numberrOfRowsOption == $numberOfRows
                )
            );
        }

        if ($numberOfItems < Pager::DISPLAY_PER_PAGE_LIMIT)
        {
            $dropDownButton->addSubButton(
                new SubButton(
                    $translator->trans(
                        $translationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE_ALL], [],
                        $translationVariables[Application::PARAM_CONTEXT]
                    ), null, $this->getUrlGenerator()->fromRequest(/*$queryParameters,*/
                    [$itemsPerPageParameterName => Pager::DISPLAY_ALL]
                ), SubButton::DISPLAY_LABEL, null, [], null, $numberOfItemsPerPage == $numberOfItems
                )
            );
        }

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        $html = [];

        $html[] = '<div class="pull-right">';
        $html[] = $buttonToolBarRenderer->render();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string[] $queryParameters
     * @param string $pageNumberParameterName
     * @param bool $includeRange
     *
     * @return string
     */
    public function renderPagination(
        $queryParameters = [], $pageNumberParameterName = 'page_nr', $includeRange = true
    )
    {
        return $this->renderPaginationBetweenStartAndEnd(
            $queryParameters, $pageNumberParameterName, 1, $this->getPager()->getNumberOfPages(), $includeRange
        );
    }

    protected function renderPaginationBetweenStartAndEnd(
        int $currentPageNumber, int $numberOfPages, int $numberOfRows, int $numberOfColumns, int $numberOfItems,
        array $queryParameters, string $pageNumberParameterName, int $start, int $end, bool $includeRange = true
    ): string
    {
        $translator = $this->getTranslator();

        $html = [];

        $html[] = '<nav class="pull-right">';
        $html[] = '<ul class="pagination">';

        if ($numberOfPages > 1)
        {

            $isDisabled = ($currentPageNumber == 1);

            $html[] = $this->renderDirectionPaginationItem(
                $queryParameters, $pageNumberParameterName, $isDisabled,
                new FontAwesomeGlyph('angles-left', ['fa-2xs']),
                $translator->trans('First', [], StringUtilities::LIBRARIES), 1
            );

            $html[] = $this->renderDirectionPaginationItem(
                $queryParameters, $pageNumberParameterName, $isDisabled, new FontAwesomeGlyph('angle-left', ['fa-2xs']),
                $translator->trans('Previous', [], StringUtilities::LIBRARIES), $currentPageNumber - 1
            );

            for ($i = $start; $i <= $end; $i ++)
            {
                $html[] = '<li' . ($currentPageNumber == $i ? ' class="active"' : '') . '><a href="' .
                    $this->getUrlGenerator()->fromRequest(/*$queryParameters,*/ [$pageNumberParameterName => $i]) .
                    '">' . $i . '</a></li>';
            }

            $isDisabled = ($currentPageNumber == $numberOfPages);

            $html[] = $this->renderDirectionPaginationItem(
                $queryParameters, $pageNumberParameterName, $isDisabled,
                new FontAwesomeGlyph('angle-right', ['fa-2xs']),
                $translator->trans('Next', [], StringUtilities::LIBRARIES), $currentPageNumber + 1
            );

            $html[] = $this->renderDirectionPaginationItem(
                $queryParameters, $pageNumberParameterName, $isDisabled,
                new FontAwesomeGlyph('angles-right', ['fa-2xs']),
                $translator->trans('Last', [], StringUtilities::LIBRARIES), $numberOfPages
            );
        }

        if ($includeRange)
        {
            $html[] = '<li class="disabled">';
            $html[] = '<span>';
            $html[] = $this->renderCurrentRange($currentPageNumber, $numberOfRows, $numberOfColumns, $numberOfItems);
            $html[] = '</span>';
            $html[] = '</li>';
        }

        $html[] = '</ul>';
        $html[] = '</nav>';

        return implode(PHP_EOL, $html);
    }

    public function renderPaginationWithPageLimit(
        int $currentPageNumber, int $numberOfRows, int $numberOfColumns, int $numberOfItems,
        array $queryParameters = [], string $pageNumberParameterName = 'page_nr', int $pageLimit = 7,
        bool $includeRange = true
    ): string
    {
        $pager = $this->getPager();
        $numberOfPages = $pager->getNumberOfPages($numberOfRows, $numberOfColumns, $numberOfItems);

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

        if ($calculatedStartPage < 1 && $calculatedEndPage > $numberOfPages)
        {
            $startPage = 1;
            $endPage = $numberOfPages;
        }
        elseif ($calculatedStartPage < 1 && $calculatedEndPage <= $numberOfPages)
        {
            $startPage = 1;
            $calculatedEndPage = $startPage + $pageLimit - 1;

            if ($calculatedEndPage > $numberOfPages)
            {
                $endPage = $numberOfPages;
            }
            else
            {
                $endPage = $calculatedEndPage;
            }
        }
        elseif ($calculatedStartPage >= 1 && $calculatedEndPage > $numberOfPages)
        {
            $endPage = $numberOfPages;
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
            $currentPageNumber, $numberOfPages, $numberOfRows, $numberOfColumns, $numberOfItems, $queryParameters,
            $pageNumberParameterName, $startPage, $endPage, $includeRange
        );
    }
}