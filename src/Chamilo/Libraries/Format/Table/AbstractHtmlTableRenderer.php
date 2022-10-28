<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader;
use Chamilo\Libraries\Format\Table\Column\AbstractSortableTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use HTML_Table;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractHtmlTableRenderer
{

    protected PagerRenderer $pagerRenderer;

    protected PathBuilder $pathBuilder;

    protected ResourceManager $resourceManager;

    protected Security $security;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, PagerRenderer $pagerRenderer, Security $security,
        ResourceManager $resourceManager, PathBuilder $pathBuilder
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->pagerRenderer = $pagerRenderer;
        $this->security = $security;
        $this->resourceManager = $resourceManager;
        $this->pathBuilder = $pathBuilder;
    }

    public function getActionsButtonToolbar(TableActions $tableActions): ButtonToolBar
    {
        $formActions = $tableActions->getActions();
        $formActionsCount = count($formActions);

        $firstAction = array_shift($formActions);

        $buttonToolBar = new ButtonToolBar();

        if ($formActionsCount > 1)
        {
            $button = new SplitDropdownButton(
                $firstAction->getTitle(), null, $firstAction->getAction(), AbstractButton::DISPLAY_LABEL,
                $firstAction->getConfirmationMessage(), ['btn-sm btn-table-action'], null, ['btn-table-action']
            );

            foreach ($formActions as $formAction)
            {
                $button->addSubButton(
                    new SubButton(
                        $formAction->getTitle(), null, $formAction->getAction(), AbstractButton::DISPLAY_LABEL,
                        $formAction->getConfirmationMessage()
                    )
                );
            }

            $buttonToolBar->addItem($button);
        }
        else
        {
            $buttonToolBar->addItem(
                new Button(
                    $firstAction->getTitle(), null, $firstAction->getAction(), AbstractButton::DISPLAY_LABEL,
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
            0, 0, $this->getTranslator()->trans('NoSearchResults', [], StringUtilities::LIBRARIES)
        );

        $html = [];

        $html[] = '<div class="table-responsive">';
        $html[] = $htmlTable->toHtml();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    abstract public function getFormClasses(): string;

    public function getPagerRenderer(): PagerRenderer
    {
        return $this->pagerRenderer;
    }

    public function getPathBuilder(): PathBuilder
    {
        return $this->pathBuilder;
    }

    public function getResourceManager(): ResourceManager
    {
        return $this->resourceManager;
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    public function getTableActionsJavascript(): string
    {
        return $this->getResourceManager()->getResourceHtml($this->getTableActionsJavascriptPath());
    }

    abstract public function getTableActionsJavascriptPath(): string;

    abstract public function getTableClasses(): string;

    abstract public function getTableContainerClasses(): string;

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     */
    protected function hasSortableColumns(array $tableColumns): bool
    {
        foreach ($tableColumns as $tableColumn)
        {
            if ($tableColumn instanceof AbstractSortableTableColumn && $tableColumn->is_sortable())
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @throws \TableException
     */
    public function prepareTableData(
        HTML_Table $htmlTable, array $tableColumns, ArrayCollection $tableRows, ?TableActions $tableActions = null
    )
    {
        $this->processSourceData($htmlTable, $tableRows);
        $this->processEmptyCells($htmlTable);
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @throws \TableException
     */
    public function processCellAttributes(HTML_Table $htmlTable, array $tableColumns, ?TableActions $tableActions = null
    )
    {
        foreach ($tableColumns as $key => $tableColumn)
        {
            $cssClasses = $tableColumn->getCssClasses();

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT]))
            {
                $contentAttributes = ['class' => $cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]];

                $htmlTable->setColAttributes(
                    ($tableActions instanceof TableActions && $tableActions->hasActions() ? $key + 1 : $key),
                    $contentAttributes
                );
            }
        }
    }

    /**
     * @throws \TableException
     */
    protected function processEmptyCells(HTML_Table $htmlTable)
    {
        $htmlTable->setAutoFill('-');
    }

    /**
     * @throws \TableException
     */
    public function processSourceData(HTML_Table $htmlTable, ArrayCollection $tableRows)
    {
        foreach ($tableRows as $row)
        {
            $htmlTable->addRow($row);
        }
    }

    /**
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    public function renderActions(string $tableName, TableActions $tableActions): string
    {
        $buttonToolBarRenderer = new ButtonToolBarRenderer($this->getActionsButtonToolbar($tableActions));

        $html = [];

        $html[] = $buttonToolBarRenderer->render();
        $html[] =
            '<input type="hidden" name="' . $tableName . '_namespace" value="' . $tableActions->getNamespace() . '"/>';
        $html[] = '<input type="hidden" name="table_name" value="' . $tableName . '"/>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     */
    public function renderNavigation(
        TableParameterValues $parameterValues, array $parameterNames
    ): string
    {
        return $this->getPagerRenderer()->renderPaginationWithPageLimit(
            $parameterValues, $parameterNames[TableParameterValues::PARAM_PAGE_NUMBER]
        );
    }

    /**
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    public function renderNumberOfItemsPerPageSelector(
        TableParameterValues $parameterValues, array $parameterNames
    ): string
    {
        if ($parameterValues->getTotalNumberOfItems() <= Pager::DISPLAY_PER_INCREMENT)
        {
            return '';
        }

        return $this->getPagerRenderer()->renderItemsPerPageSelector(
            $parameterValues, $parameterNames[TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE]
        );
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function renderPropertyDirectionSubButtons(
        array $tableColumns, TableParameterValues $parameterValues, array $parameterNames
    ): array
    {
        $currentFirstOrderDirection = $parameterValues->getOrderColumnDirection();
        $subButtons = [];
        $translator = $this->getTranslator();

        if ($this->hasSortableColumns($tableColumns))
        {
            $propertyUrl = $this->getUrlGenerator()->fromRequest(
                [$parameterNames[TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION] => SORT_ASC]
            );
            $isSelected = $currentFirstOrderDirection == SORT_ASC;

            $subButtons[] = new SubButton(
                $translator->trans('ASC'), null, $propertyUrl, AbstractButton::DISPLAY_LABEL, null, [], null,
                $isSelected
            );

            $propertyUrl = $this->getUrlGenerator()->fromRequest(
                [$parameterNames[TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION] => SORT_DESC]
            );
            $isSelected = $currentFirstOrderDirection == SORT_DESC;

            $subButtons[] = new SubButton(
                $translator->trans('DESC'), null, $propertyUrl, AbstractButton::DISPLAY_LABEL, null, [], null,
                $isSelected
            );
        }

        return $subButtons;
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @throws \QuickformException
     * @throws \ReflectionException
     */
    public function renderPropertySorting(
        array $tableColumns, TableParameterValues $parameterValues, array $parameterNames
    ): string
    {
        $html = [];

        if ($this->hasSortableColumns($tableColumns))
        {
            $buttonToolBar = new ButtonToolBar();
            $dropDownButton = new DropdownButton();
            $translator = $this->getTranslator();

            $currentFirstOrderColumn = $parameterValues->getOrderColumnIndex();
            $currentFirstOrderDirection = $parameterValues->getOrderColumnDirection();

            $orderProperty = $tableColumns[$currentFirstOrderColumn];

            $dropDownButton->addSubButton(new SubButtonHeader($translator->trans('SortingProperty')));
            $dropDownButton->addSubButtons(
                $this->renderPropertySubButtons($tableColumns, $parameterValues, $parameterNames)
            );
            $dropDownButton->setClasses(['btn-sm']);
            $dropDownButton->setDropdownClasses(['dropdown-menu-right']);

            $dropDownButton->addSubButton(new SubButtonDivider());
            $dropDownButton->addSubButton(new SubButtonHeader($translator->trans('SortingDirection')));
            $dropDownButton->addSubButtons(
                $this->renderPropertyDirectionSubButtons($tableColumns, $parameterValues, $parameterNames)
            );

            $orderDirection =
                $translator->trans(($currentFirstOrderDirection == SORT_ASC ? 'SortAscending' : 'SortDescending'), [],
                    StringUtilities::LIBRARIES);

            $dropDownButton->setLabel(
                $translator->trans(
                    'TableOrderPropertyWithDirection',
                    ['{PROPERTY}' => $orderProperty->get_title(), '{DIRECTION}' => $orderDirection],
                    StringUtilities::LIBRARIES
                )
            );

            $buttonToolBar->addItem($dropDownButton);

            $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

            $html[] = '<div class="pull-right table-order-property">';
            $html[] = $buttonToolBarRenderer->render();
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function renderPropertySubButtons(
        array $tableColumns, TableParameterValues $parameterValues, array $parameterNames
    ): array
    {
        $currentOrderColumnIndex = $parameterValues->getOrderColumnIndex();
        $subButtons = [];

        if ($this->hasSortableColumns($tableColumns))
        {
            foreach ($tableColumns as $index => $tableColumn)
            {
                $propertyUrl = $this->getUrlGenerator()->fromRequest(
                    [$parameterNames[TableParameterValues::PARAM_ORDER_COLUMN_INDEX] => $index]
                );

                $isSelected = $currentOrderColumnIndex == $index;

                $subButtons[] = new SubButton(
                    $this->getSecurity()->removeXSS($tableColumn->get_title()), null, $propertyUrl,
                    AbstractButton::DISPLAY_LABEL, null, [], null, $isSelected
                );
            }
        }

        return $subButtons;
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    protected function renderTable(
        HTML_Table $htmlTable, array $tableColumns, ArrayCollection $tableRows, string $tableName,
        array $parameterNames, TableParameterValues $parameterValues, ?TableActions $tableActions = null
    ): string
    {
        $html = [];

        $html[] = $this->renderTableHeader($tableColumns, $tableName, $parameterNames, $parameterValues, $tableActions);

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

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
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
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
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function renderTableFooter(
        string $tableName, TableParameterValues $parameterValues, array $parameterNames,
        ?TableActions $tableActions = null
    ): string
    {
        $hasFormActions = $tableActions instanceof TableActions && $tableActions->hasActions();

        $html = [];

        $html[] = '<div class="row">';

        if ($hasFormActions)
        {
            $html[] = '<div class="col-xs-12 col-md-6 table-navigation-actions">';
            $html[] = $this->renderActions($tableName, $tableActions);
            $html[] = '</div>';
        }

        $classes = 'col-xs-12';

        if ($hasFormActions)
        {
            $classes .= ' col-md-6';
        }

        $html[] = '<div class="' . $classes . ' table-navigation-pagination">';
        $html[] = $this->renderNavigation($parameterValues, $parameterNames);
        $html[] = '</div>';

        $html[] = '</div>';

        if ($hasFormActions)
        {
            $html[] = '<input type="submit" name="Submit" value="Submit" style="display:none;" />';
            $html[] = '</form>';
            $html[] = $this->getTableActionsJavascript();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    protected function renderTableHeader(
        array $tableColumns, string $tableName, array $parameterNames, TableParameterValues $parameterValues,
        ?TableActions $tableActions = null
    ): string
    {
        $html = [];

        $html[] = $this->renderTableHeaderStart($tableName, $tableActions);
        $html[] = $this->renderNumberOfItemsPerPageSelector($parameterValues, $parameterNames);
        $html[] = $this->renderPropertySorting($tableColumns, $parameterValues, $parameterNames);
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
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    protected function renderTableHeaderStart(string $tableName, ?TableActions $tableActions = null): string
    {
        $hasFormActions = $tableActions instanceof TableActions && $tableActions->hasActions();

        $html = [];

        if ($hasFormActions)
        {
            $formActions = $tableActions->getActions();
            $firstFormAction = array_shift($formActions);

            $html[] =
                '<form class="' . $this->getFormClasses() . '" method="post" action="' . $firstFormAction->getAction() .
                '" name="form_' . $tableName . '">';
        }

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-md-6 table-navigation-actions">';

        if ($hasFormActions)
        {
            $html[] = $this->renderActions($tableName, $tableActions);
        }

        $html[] = '</div>';

        $classes = 'col-xs-12';

        if ($hasFormActions)
        {
            $classes .= ' col-md-6';
        }

        $html[] = '<div class="' . $classes . ' table-navigation-search">';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     *
     * @return AbstractHtmlTableRenderer
     */
    public function setPathBuilder(PathBuilder $pathBuilder): AbstractHtmlTableRenderer
    {
        $this->pathBuilder = $pathBuilder;

        return $this;
    }

    /**
     * @param \Chamilo\Libraries\Format\Utilities\ResourceManager $resourceManager
     *
     * @return AbstractHtmlTableRenderer
     */
    public function setResourceManager(ResourceManager $resourceManager): AbstractHtmlTableRenderer
    {
        $this->resourceManager = $resourceManager;

        return $this;
    }
}
