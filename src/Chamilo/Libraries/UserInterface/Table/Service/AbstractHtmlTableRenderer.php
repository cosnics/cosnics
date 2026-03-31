<?php
namespace Chamilo\Libraries\UserInterface\Table\Service;

use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Protocol\Security\Service\SecurityUtilities;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonDivider;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonHeader;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\AbstractBaseTableParameters;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\AbstractSortableTableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableAction\TableActions;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableParameterValues;
use Doctrine\Common\Collections\ArrayCollection;
use HTML_Table;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractHtmlTableRenderer
{
    public function __construct(
        protected Translator $translator, protected UrlGenerator $urlGenerator,
        protected PageNavigationRenderer $pagerRenderer, protected SecurityUtilities $securityUtilities,
        protected ResourceManager $resourceManager, protected WebPathBuilder $webPathBuilder,
        protected ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
    }

    public function getActionsButtonToolBar(TableActions $tableActions): ButtonToolBar
    {
        $formActions = $tableActions->getActions();
        $formActionsCount = count($formActions);

        $firstAction = array_shift($formActions);

        $buttonToolBar = new ButtonToolBar();

        if ($formActionsCount > 1) {
            $button = new SplitDropdownButtonCollection(
                $firstAction->getTitle(), null, $firstAction->getAction(), DisplayTypeEnum::LABEL,
                $firstAction->getConfirmationMessage(), ['btn-sm btn-table-action'], null, ['btn-table-action']
            );

            foreach ($formActions as $formAction) {
                $button->addButton(
                    new SubButton(
                        $formAction->getTitle(), null, $formAction->getAction(), DisplayTypeEnum::LABEL,
                        $formAction->getConfirmationMessage()
                    )
                );
            }

            $buttonToolBar->addButton($button);
        }
        else {
            $buttonToolBar->addButton(
                new Button(
                    $firstAction->getTitle(), null, $firstAction->getAction(), DisplayTypeEnum::LABEL,
                    $firstAction->getConfirmationMessage(), ['btn-sm', 'btn-table-action']
                )
            );
        }

        return $buttonToolBar;
    }

    /**
     * @throws \TableException
     */
    public function getEmptyTable(HTML_Table $htmlTable): string
    {
        $cols = $htmlTable->getHeader()->getColCount();

        $htmlTable->setCellAttributes(0, 0, 'style="font-style: italic;text-align:center;" colspan=' . $cols);
        $htmlTable->setCellContents(
            0, 0, $this->translator->trans('NoSearchResults', [], StringUtilities::LIBRARIES)
        );

        $html = [];

        $html[] = '<div class="table-responsive">';
        $html[] = $htmlTable->toHtml();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    abstract public function getFormClasses(): string;

    public function getTableActionsJavascript(): string
    {
        return $this->resourceManager->getResourceHtml($this->getTableActionsJavascriptPath());
    }

    abstract public function getTableActionsJavascriptPath(): string;

    abstract public function getTableClasses(): string;

    abstract public function getTableContainerClasses(): string;

    /**
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[] $tableColumns
     */
    protected function hasSortableColumns(array $tableColumns): bool
    {
        foreach ($tableColumns as $tableColumn) {
            if ($tableColumn instanceof AbstractSortableTableColumn && $tableColumn->isSortable()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[] $tableColumns
     *
     * @throws \TableException
     */
    public function prepareTableData(
        HTML_Table $htmlTable, array $tableColumns, ArrayCollection $tableRows, ?TableActions $tableActions = null
    ): static
    {
        $this->processSourceData($htmlTable, $tableRows);
        $this->processEmptyCells($htmlTable);

        return $this;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[] $tableColumns
     *
     * @throws \TableException
     */
    public function processCellAttributes(HTML_Table $htmlTable, array $tableColumns, ?TableActions $tableActions = null
    ): static
    {
        foreach ($tableColumns as $key => $tableColumn) {
            $cssClasses = $tableColumn->getCssClasses();

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT])) {
                $contentAttributes = ['class' => $cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]];

                $htmlTable->setColAttributes(
                    ($tableActions instanceof TableActions && $tableActions->hasActions() ? $key + 1 : $key),
                    $contentAttributes
                );
            }
        }

        return $this;
    }

    /**
     * @throws \TableException
     */
    protected function processEmptyCells(HTML_Table $htmlTable): static
    {
        $htmlTable->setAutoFill('-');

        return $this;
    }

    /**
     * @throws \TableException
     */
    public function processSourceData(HTML_Table $htmlTable, ArrayCollection $tableRows): static
    {
        foreach ($tableRows as $row) {
            $htmlTable->addRow($row);
        }

        return $this;
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderActions(string $tableName, TableActions $tableActions): string
    {
        $html = [];

        $html[] = $this->buttonToolBarRenderer->render($this->getActionsButtonToolBar($tableActions));
        $html[] =
            '<input type="hidden" name="' . $tableName . '_namespace" value="' . $tableActions->getNamespace() . '"/>';
        $html[] = '<input type="hidden" name="table_name" value="' . $tableName . '"/>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     */
    public function renderNavigation(
        TableParameterValues $parameterValues, array $parameterNames
    ): string
    {
        return $this->pagerRenderer->renderPaginationWithPageLimit(
            $parameterValues, $parameterNames[AbstractBaseTableParameters::PARAM_PAGE_NUMBER]
        );
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderNumberOfItemsPerPageSelector(
        TableParameterValues $parameterValues, array $parameterNames
    ): string
    {
        if ($parameterValues->getTotalNumberOfItems() <= PageNavigationCalculator::DISPLAY_PER_INCREMENT) {
            return '';
        }

        return $this->pagerRenderer->renderItemsPerPageSelector(
            $parameterValues, $parameterNames[TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE]
        );
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[] $tableColumns
     *
     * @return ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton>
     */
    public function renderPropertyDirectionSubButtons(
        array $tableColumns, TableParameterValues $parameterValues, array $parameterNames
    ): ArrayCollection
    {
        $currentFirstOrderDirection = $parameterValues->getOrderColumnDirection();
        $subButtons = [];
        $translator = $this->translator;

        if ($this->hasSortableColumns($tableColumns)) {
            $propertyUrl = $this->urlGenerator->fromRequest(
                [$parameterNames[AbstractBaseTableParameters::PARAM_ORDER_COLUMN_DIRECTION] => SORT_ASC]
            );
            $isSelected = $currentFirstOrderDirection == SORT_ASC;

            $subButtons[] = new SubButton(
                $translator->trans('ASC', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('chevron-down'),
                $propertyUrl, DisplayTypeEnum::ICON_AND_LABEL, null, [], null, $isSelected
            );

            $propertyUrl = $this->urlGenerator->fromRequest(
                [$parameterNames[AbstractBaseTableParameters::PARAM_ORDER_COLUMN_DIRECTION] => SORT_DESC]
            );
            $isSelected = $currentFirstOrderDirection == SORT_DESC;

            $subButtons[] = new SubButton(
                $translator->trans('DESC', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('chevron-up'),
                $propertyUrl, DisplayTypeEnum::ICON_AND_LABEL, null, [], null, $isSelected
            );
        }

        return new ArrayCollection($subButtons);
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[] $tableColumns
     *
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderPropertySorting(
        array $tableColumns, TableParameterValues $parameterValues, array $parameterNames
    ): string
    {
        $html = [];

        if ($this->hasSortableColumns($tableColumns)) {
            $buttonToolBar = new ButtonToolBar();
            $dropDownButton = new DropDownButtonCollection();
            $translator = $this->translator;

            $currentFirstOrderColumn = $parameterValues->getOrderColumnIndex();
            $currentFirstOrderDirection = $parameterValues->getOrderColumnDirection();

            $orderProperty = $tableColumns[$currentFirstOrderColumn];

            $dropDownButton->addButton(
                new SubButtonHeader($translator->trans('SortingProperty', [], StringUtilities::LIBRARIES))
            );
            $dropDownButton->addButtons(
                $this->renderPropertySubButtons($tableColumns, $parameterValues, $parameterNames)
            );
            $dropDownButton->setClasses(['btn-sm']);
            $dropDownButton->setDropDownClasses(['dropdown-menu-right']);

            $dropDownButton->addButton(new SubButtonDivider());
            $dropDownButton->addButton(
                new SubButtonHeader($translator->trans('SortingDirection', [], StringUtilities::LIBRARIES))
            );
            $dropDownButton->addButtons(
                $this->renderPropertyDirectionSubButtons($tableColumns, $parameterValues, $parameterNames)
            );

            $orderDirection =
                $translator->trans(($currentFirstOrderDirection == SORT_ASC ? 'SortAscending' : 'SortDescending'), [],
                    StringUtilities::LIBRARIES);

            $dropDownButton->setLabel(
                $translator->trans(
                    'TableOrderPropertyWithDirection',
                    ['%Property%' => $orderProperty->getTitle(), '%Direction%' => $orderDirection],
                    StringUtilities::LIBRARIES
                )
            );

            $buttonToolBar->addButton($dropDownButton);

            $html[] = $this->buttonToolBarRenderer->render($buttonToolBar);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[] $tableColumns
     *
     * @return ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton>
     */
    public function renderPropertySubButtons(
        array $tableColumns, TableParameterValues $parameterValues, array $parameterNames
    ): ArrayCollection
    {
        $currentOrderColumnIndex = $parameterValues->getOrderColumnIndex();
        $subButtons = [];

        if ($this->hasSortableColumns($tableColumns)) {
            foreach ($tableColumns as $index => $tableColumn) {
                if ($tableColumn instanceof AbstractSortableTableColumn) {
                    $propertyUrl = $this->urlGenerator->fromRequest(
                        [$parameterNames[AbstractBaseTableParameters::PARAM_ORDER_COLUMN_INDEX] => $index]
                    );

                    $isSelected = $currentOrderColumnIndex == $index;

                    $subButtons[] = new SubButton(
                        $this->securityUtilities->removeXSS($tableColumn->getTitle()), null, $propertyUrl,
                        DisplayTypeEnum::LABEL, null, [], null, $isSelected
                    );
                }
            }
        }

        return new ArrayCollection($subButtons);
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[] $tableColumns
     *
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function renderTable(
        HTML_Table $htmlTable, array $tableColumns, ArrayCollection $tableRows, string $tableName,
        array $parameterNames, TableParameterValues $parameterValues, ?TableActions $tableActions = null
    ): string
    {
        $html = [];

        $html[] = $this->renderTableHeader($tableColumns, $tableName, $parameterNames, $parameterValues, $tableActions);

        $html[] = '<div class="row">';
        $html[] = '<div class="col-12">';

        $html[] = '<div class="' . $this->getTableContainerClasses() . '">';
        $html[] = $this->renderTableBody($htmlTable, $tableColumns, $tableRows, $tableActions);
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderTableFooter(
            $tableName, $parameterValues, $parameterNames, $tableActions
        );

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[] $tableColumns
     *
     * @throws \TableException
     */
    public function renderTableBody(
        HTML_Table $htmlTable, array $tableColumns, ArrayCollection $tableRows, ?TableActions $tableActions = null
    ): string
    {
        $this->prepareTableData($htmlTable, $tableColumns, $tableRows, $tableActions);

        return $htmlTable->toHtml();
    }

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderTableFooter(
        string $tableName, TableParameterValues $parameterValues, array $parameterNames,
        ?TableActions $tableActions = null
    ): string
    {
        $hasFormActions = $tableActions instanceof TableActions && $tableActions->hasActions();

        $html = [];

        $html[] = '<div class="row">';

        if ($hasFormActions) {
            $html[] = '<div class="col-12 col-md-4">';
            $html[] = $this->renderActions($tableName, $tableActions);
            $html[] = '</div>';
        }

        $classes = 'col-12';

        if ($hasFormActions) {
            $classes .= ' col-md-8';
        }

        $html[] = '<div class="' . $classes . '">';
        $html[] = $this->renderNavigation($parameterValues, $parameterNames);
        $html[] = '</div>';

        $html[] = '</div>';

        if ($hasFormActions) {
            $html[] = '<input class="d-none" type="submit" name="Submit" value="Submit" />';
            $html[] = '</form>';
            $html[] = $this->getTableActionsJavascript();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[] $tableColumns
     *
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function renderTableHeader(
        array $tableColumns, string $tableName, array $parameterNames, TableParameterValues $parameterValues,
        ?TableActions $tableActions = null
    ): string
    {
        $html = [];

        $html[] = $this->renderTableHeaderStart($tableName, $tableActions);
        $html[] = $this->renderPropertySorting($tableColumns, $parameterValues, $parameterNames);
        $html[] = $this->renderNumberOfItemsPerPageSelector($parameterValues, $parameterNames);
        $html[] = $this->renderTableHeaderEnd();

        return implode(PHP_EOL, $html);
    }

    protected function renderTableHeaderEnd(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function renderTableHeaderStart(string $tableName, ?TableActions $tableActions = null): string
    {
        $hasFormActions = $tableActions instanceof TableActions && $tableActions->hasActions();

        $html = [];

        if ($hasFormActions) {
            $formActions = $tableActions->getActions();
            $firstFormAction = array_shift($formActions);

            $html[] =
                '<form class="' . $this->getFormClasses() . '" method="post" action="' . $firstFormAction->getAction() .
                '" name="form_' . $tableName . '">';
        }

        $html[] = '<div class="row mb-1">';
        $html[] = '<div class="col-12 d-flex justify-content-between">';

        if ($hasFormActions) {
            $html[] = $this->renderActions($tableName, $tableActions);
        }

        return implode(PHP_EOL, $html);
    }
}
