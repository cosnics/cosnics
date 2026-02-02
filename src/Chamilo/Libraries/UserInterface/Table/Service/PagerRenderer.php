<?php
namespace Chamilo\Libraries\UserInterface\Table\Service;

use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableParameterValues;
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

    protected ButtonToolBarRenderer $buttonToolBarRenderer;

    protected Pager $pager;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        Translator $translator, Pager $pager, UrlGenerator $urlGenerator, ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
        $this->translator = $translator;
        $this->pager = $pager;
        $this->urlGenerator = $urlGenerator;
        $this->buttonToolBarRenderer = $buttonToolBarRenderer;
    }

    public function getButtonToolBarRenderer(): ButtonToolBarRenderer
    {
        return $this->buttonToolBarRenderer;
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
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     */
    public function renderCurrentRange(TableParameterValues $parameterValues): string
    {
        $pager = $this->getPager();
        $variables = [];

        $variables['{START}'] = $pager->getCurrentRangeStart(
            $parameterValues->getPageNumber(), $parameterValues->getNumberOfItemsPerPage(),
            $parameterValues->getTotalNumberOfItems()
        );
        $variables['{END}'] = $pager->getCurrentRangeEnd(
            $parameterValues->getPageNumber(), $parameterValues->getNumberOfItemsPerPage(),
            $parameterValues->getTotalNumberOfItems()
        );
        $variables['{TOTAL}'] = $parameterValues->getTotalNumberOfItems();

        return $this->getTranslator()->trans('ShowingStartToEndOfTotalEntries', $variables, StringUtilities::LIBRARIES);
    }

    protected function renderDirectionPaginationItem(
        string $pageNumberParameterName, bool $isDisabled, InlineGlyph $inlineGlyph, string $translation,
        ?int $targetPage = null
    ): string
    {
        $html = [];

        $html[] = '<li' . ($isDisabled ? ' class="disabled"' : '') . '>';
        $symbolHtml = '<span aria-hidden="true">' . $inlineGlyph->render() . '</span>';

        if ($isDisabled) {
            $html[] = $symbolHtml;
        }
        else {
            $html[] = '<a href="' . $this->getUrlGenerator()->fromRequest([$pageNumberParameterName => $targetPage]) .
                '" aria-label="' . $translation . '">' . $symbolHtml . '</a>';
        }

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string[] $translationVariables
     *
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function renderItemsPerPageSelector(
        TableParameterValues $parameterValues, string $itemsPerPageParameterName, array $translationVariables = []
    ): string
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();
        $buttonToolBar->addButton($buttonGroup);
        $translator = $this->getTranslator();

        $defaultTranslationVariables[Application::PARAM_CONTEXT] = StringUtilities::LIBRARIES;
        $defaultTranslationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE] = 'ShowNumberOfItemsPerPage';
        $defaultTranslationVariables[self::PAGE_SELECTOR_TRANSLATION_ROW] = 'ShowNumberOfItemsPerPage';
        $defaultTranslationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE_ALL] = 'ShowAllItems';

        $translationVariables = array_merge($defaultTranslationVariables, $translationVariables);

        $numberOfItemsPerPage = $parameterValues->getNumberOfItemsPerPage();

        if ($numberOfItemsPerPage >= $parameterValues->getTotalNumberOfItems()) {
            $dropDownButtonLabel = $translator->trans(
                $translationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE_ALL], [],
                $translationVariables[Application::PARAM_CONTEXT]
            );
        }
        else {
            $dropDownButtonLabel = $translator->trans(
                $translationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE], ['{NUMBER}' => $numberOfItemsPerPage],
                $translationVariables[Application::PARAM_CONTEXT]
            );
        }

        $dropDownButton =
            new DropDownButtonCollection($dropDownButtonLabel, null, ButtonDisplayInterface::DISPLAY_LABEL, ['btn-sm'],
                ['dropdown-menu-right']);
        $buttonGroup->addButton($dropDownButton);

        for (
            $nr = Pager::DISPLAY_PER_INCREMENT; $nr <= $parameterValues->getTotalNumberOfItems() && $nr <= 100;
            $nr += Pager::DISPLAY_PER_INCREMENT
        ) {
            $numberrOfRowsOption = ($nr / $parameterValues->getNumberOfColumnsPerPage());

            $dropDownButton->addButton(
                new SubButton(
                    $translator->trans(
                        $translationVariables[self::PAGE_SELECTOR_TRANSLATION_ROW], ['{NUMBER}' => $nr],
                        $translationVariables[Application::PARAM_CONTEXT]
                    ), null, $this->getUrlGenerator()->fromRequest(
                    [$itemsPerPageParameterName => $numberrOfRowsOption]
                ), ButtonDisplayInterface::DISPLAY_LABEL, null, [], null,
                    $numberrOfRowsOption == $parameterValues->getNumberOfRowsPerPage()
                )
            );
        }

        if ($parameterValues->getTotalNumberOfItems() < Pager::DISPLAY_PER_PAGE_LIMIT) {
            $dropDownButton->addButton(
                new SubButton(
                    $translator->trans(
                        $translationVariables[self::PAGE_SELECTOR_TRANSLATION_TITLE_ALL], [],
                        $translationVariables[Application::PARAM_CONTEXT]
                    ), null, $this->getUrlGenerator()->fromRequest(
                    [$itemsPerPageParameterName => Pager::DISPLAY_ALL]
                ), ButtonDisplayInterface::DISPLAY_LABEL, null, [], null,
                    $numberOfItemsPerPage == $parameterValues->getTotalNumberOfItems()
                )
            );
        }

        $html = [];

        $html[] = '<div class="pull-right">';
        $html[] = $this->getButtonToolBarRenderer()->render($buttonToolBar);
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     */
    public function renderPagination(
        int $numberOfPages, TableParameterValues $parameterValues, string $pageNumberParameterName,
        bool $includeRange = true
    ): string
    {
        return $this->renderPaginationBetweenStartAndEnd(
            $numberOfPages, $parameterValues, $pageNumberParameterName, 1, $numberOfPages, $includeRange
        );
    }

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     */
    protected function renderPaginationBetweenStartAndEnd(
        int $numberOfPages, TableParameterValues $parameterValues, string $pageNumberParameterName, int $start,
        int $end, bool $includeRange = true
    ): string
    {
        $translator = $this->getTranslator();

        $html = [];

        $html[] = '<nav class="pull-right">';
        $html[] = '<ul class="pagination">';

        if ($numberOfPages > 1) {
            $currentPageNumber = $parameterValues->getPageNumber();

            $isDisabled = ($currentPageNumber == 1);

            $html[] = $this->renderDirectionPaginationItem(
                $pageNumberParameterName, $isDisabled, new FontAwesomeGlyph('angles-left', ['fa-2xs']),
                $translator->trans('First', [], StringUtilities::LIBRARIES), 1
            );

            $html[] = $this->renderDirectionPaginationItem(
                $pageNumberParameterName, $isDisabled, new FontAwesomeGlyph('angle-left', ['fa-2xs']),
                $translator->trans('Previous', [], StringUtilities::LIBRARIES), $currentPageNumber - 1
            );

            for ($i = $start; $i <= $end; $i ++) {
                $html[] = '<li' . ($currentPageNumber == $i ? ' class="active"' : '') . '><a href="' .
                    $this->getUrlGenerator()->fromRequest([$pageNumberParameterName => $i]) . '">' . $i . '</a></li>';
            }

            $isDisabled = ($currentPageNumber == $numberOfPages);

            $html[] = $this->renderDirectionPaginationItem(
                $pageNumberParameterName, $isDisabled, new FontAwesomeGlyph('angle-right', ['fa-2xs']),
                $translator->trans('Next', [], StringUtilities::LIBRARIES), $currentPageNumber + 1
            );

            $html[] = $this->renderDirectionPaginationItem(
                $pageNumberParameterName, $isDisabled, new FontAwesomeGlyph('angles-right', ['fa-2xs']),
                $translator->trans('Last', [], StringUtilities::LIBRARIES), $numberOfPages
            );
        }

        if ($includeRange) {
            $html[] = '<li class="disabled">';
            $html[] = '<span>';
            $html[] = $this->renderCurrentRange($parameterValues);
            $html[] = '</span>';
            $html[] = '</li>';
        }

        $html[] = '</ul>';
        $html[] = '</nav>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     */
    public function renderPaginationWithPageLimit(
        TableParameterValues $parameterValues, string $pageNumberParameterName = 'page_nr', int $pageLimit = 7,
        bool $includeRange = true
    ): string
    {
        $numberOfPages = $this->getPager()->getNumberOfPages(
            $parameterValues->getNumberOfItemsPerPage(), $parameterValues->getTotalNumberOfItems()
        );

        if ($pageLimit % 2 == 0) {
            $itemsBefore = ceil($pageLimit / 2);
            $itemsAfter = $pageLimit - 1 - $itemsBefore;
        }
        else {
            $itemsBefore = $itemsAfter = ($pageLimit - 1) / 2;
        }

        $calculatedStartPage = $parameterValues->getPageNumber() - $itemsBefore;
        $calculatedEndPage = $parameterValues->getPageNumber() + $itemsAfter;

        if ($calculatedStartPage < 1 && $calculatedEndPage > $numberOfPages) {
            $startPage = 1;
            $endPage = $numberOfPages;
        }
        elseif ($calculatedStartPage < 1 && $calculatedEndPage <= $numberOfPages) {
            $startPage = 1;
            $calculatedEndPage = $startPage + $pageLimit - 1;

            $endPage = min($calculatedEndPage, $numberOfPages);
        }
        elseif ($calculatedStartPage >= 1 && $calculatedEndPage > $numberOfPages) {
            $endPage = $numberOfPages;
            $calculatedStartPage = $endPage - $pageLimit + 1;

            if ($calculatedStartPage < 1) {
                $startPage = 1;
            }
            else {
                $startPage = $calculatedStartPage;
            }
        }
        else {
            $startPage = $calculatedStartPage;
            $endPage = $calculatedEndPage;
        }

        return $this->renderPaginationBetweenStartAndEnd(
            $numberOfPages, $parameterValues, $pageNumberParameterName, $startPage, $endPage, $includeRange
        );
    }
}