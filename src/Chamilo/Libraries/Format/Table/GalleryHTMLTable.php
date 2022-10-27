<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTablePropertyModel;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use HTML_Table;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class GalleryHTMLTable
{
    protected Pager $pager;

    protected PagerRenderer $pagerRenderer;

    protected Security $security;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, Pager $pager, PagerRenderer $pagerRenderer,
        Security $security
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->pager = $pager;
        $this->pagerRenderer = $pagerRenderer;
        $this->security = $security;
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function render(
        array $tableColumns, ArrayCollection $tableRows, string $tableName, array $parameterNames,
        TableParameterValues $parameterValues, ?TableActions $tableActions = null
    ): string
    {
        $htmlTable = new HTML_Table(['class' => $this->getTableClasses()], 0, true);

        if ($parameterValues->getTotalNumberOfItems() == 0)
        {
            return $this->getEmptyTable($htmlTable);
        }

        $html = [];

        $html[] = $this->renderTableHeader(
            $tableName, $parameterValues, $parameterNames, $tableActions
        );

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

        $buttonToolBar->prependItem(
            new Button(
                Translation::get('SelectAll', null, StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('check-square', [], null, 'far'), '#', Button::DISPLAY_ICON_AND_LABEL, null,
                ['btn-sm select-all']
            )
        );

        $buttonToolBar->prependItem(
            new Button(
                Translation::get('UnselectAll', null, StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('square', [], null, 'far'), '#', Button::DISPLAY_ICON_AND_LABEL, null,
                ['btn-sm select-none']
            )
        );

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

    public function getFormClasses(): string
    {
        return 'form-gallery-table';
    }

    public function getPagerRenderer(): PagerRenderer
    {
        return $this->pagerRenderer;
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    public function getTableActionsJavascript(): string
    {
        return ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(StringUtilities::LIBRARIES, true) . 'GalleryTable.js'
        );
    }

    public function getTableClasses(): string
    {
        return 'table-gallery col-xs-12';
    }

    public function getTableContainerClasses(): string
    {
        return 'table-gallery-container';
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
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @throws \TableException
     */
    public function prepareTableData(
        HTML_Table $htmlTable, array $tableColumns, ArrayCollection $tableRows, ?TableActions $tableActions = null
    )
    {
        $this->processSourceData($htmlTable, $tableRows);
        $this->processCellAttributes($htmlTable, $tableColumns, $tableActions);
        $this->processEmptyCells($htmlTable);

        $htmlTable->setAllAttributes(['class' => 'col-xs-6 col-lg-3']);
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
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function renderPropertyDirectionSubButtons()
    {
        $propertyModel = $this->getSourceProperties();
        $currentOrderDirections = $this->getOrderDirection();
        $currentFirstOrderDirection = $currentOrderDirections[0];
        $subButtons = [];

        if ($this->allowOrderDirection && $propertyModel instanceof GalleryTablePropertyModel &&
            count($propertyModel->get_properties()) > 0)
        {
            $queryParameters = [];
            $queryParameters[$this->getParameterName(TableParameterValues::PARAM_PAGE_NUMBER)] = $this->getPageNumber();
            $queryParameters[$this->getParameterName(TableParameterValues::PARAM_ORDER_COLUMN_INDEX)] =
                $this->getOrderColumn();
            $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

            $queryParameters[$this->getParameterName(TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION)] = [SORT_ASC];
            $propertyUrl = new Redirect($queryParameters);
            $isSelected = $currentFirstOrderDirection == SORT_ASC;

            $subButtons[] = new SubButton(
                Translation::get('ASC'), null, $propertyUrl->getUrl(), SubButton::DISPLAY_LABEL, null, [], null,
                $isSelected
            );

            $queryParameters[$this->getParameterName(TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION)] = SORT_DESC;
            $propertyUrl = new Redirect($queryParameters);
            $isSelected = $currentFirstOrderDirection == SORT_DESC;

            $subButtons[] = new SubButton(
                Translation::get('DESC'), null, $propertyUrl->getUrl(), SubButton::DISPLAY_LABEL, null, [], null,
                $isSelected
            );
        }

        return $subButtons;
    }

    /**
     * @return string
     */
    public function renderPropertySorting()
    {
        $propertyModel = $this->getSourceProperties();

        $html = [];

        if ($propertyModel instanceof GalleryTablePropertyModel && count($propertyModel->get_properties()) > 0)
        {
            $buttonToolBar = new ButtonToolBar();
            $dropDownButton = new DropdownButton();
            $properties = $propertyModel->get_properties();

            $currentOrderColumns = $this->getOrderColumn();
            $currentFirstOrderColumn = $currentOrderColumns[0];
            $currentOrderDirections = $this->getOrderDirection();
            $currentFirstOrderDirection = $currentOrderDirections[0];

            $hasFormActions =
                $this->getTableFormActions() instanceof TableActions && $this->getTableFormActions()->hasFormActions();

            $propertyIndex = $currentFirstOrderColumn - ($hasFormActions ? 1 : 0);

            $orderProperty = $properties[$propertyIndex];

            $dropDownButton->addSubButton(new SubButtonHeader(Translation::get('SortingProperty')));
            $dropDownButton->addSubButtons($this->renderPropertySubButtons());
            $dropDownButton->setClasses(['btn-sm']);
            $dropDownButton->setDropdownClasses(['dropdown-menu-right']);

            $orderPropertyName = Translation::get(
                (string) StringUtilities::getInstance()->createString($orderProperty->get_name())->upperCamelize()
            );

            if ($this->allowOrderDirection)
            {
                $dropDownButton->addSubButton(new SubButtonDivider());
                $dropDownButton->addSubButton(new SubButtonHeader(Translation::get('SortingDirection')));
                $dropDownButton->addSubButtons($this->renderPropertyDirectionSubButtons());

                $orderDirection = Translation::get(($currentFirstOrderDirection == SORT_ASC ? 'ASC' : 'DESC'));

                $dropDownButton->setLabel(
                    Translation::get(
                        'GalleryTableOrderPropertyWithDirection',
                        ['PROPERTY' => $orderPropertyName, 'DIRECTION' => $orderDirection]
                    )
                );
            }
            else
            {
                $dropDownButton->setLabel(
                    Translation::get('GalleryTableOrderProperty', ['PROPERTY' => $orderPropertyName])
                );
            }

            $buttonToolBar->addItem($dropDownButton);

            $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

            $html[] = '<div class="pull-right table-order-property">';
            $html[] = $buttonToolBarRenderer->render();
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function renderPropertySubButtons()
    {
        $propertyModel = $this->getSourceProperties();
        $hasFormActions =
            $this->getTableFormActions() instanceof TableActions && $this->getTableFormActions()->hasFormActions();
        $currentOrderColumns = $this->getOrderColumn();
        $currentFirstOrderColumn = $currentOrderColumns[0];
        $subButtons = [];

        if ($propertyModel instanceof GalleryTablePropertyModel && count($propertyModel->get_properties()) > 0)
        {
            $properties = $propertyModel->get_properties();

            foreach ($properties as $index => $property)
            {
                $propertyIndex = $index + ($hasFormActions ? 1 : 0);

                $queryParameters = [];
                $queryParameters[$this->getParameterName(TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION)] =
                    $this->getOrderDirection();
                $queryParameters[$this->getParameterName(TableParameterValues::PARAM_PAGE_NUMBER)] =
                    $this->getPageNumber();
                $queryParameters[$this->getParameterName(TableParameterValues::PARAM_ORDER_COLUMN_INDEX)] =
                    [$propertyIndex];
                $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

                $propertyUrl = new Redirect($queryParameters);

                $label = Translation::get(
                    (string) StringUtilities::getInstance()->createString($property->get_name())->upperCamelize()
                );
                $isSelected = $currentFirstOrderColumn == $propertyIndex;

                $subButtons[] = new SubButton(
                    $label, null, $propertyUrl->getUrl(), SubButton::DISPLAY_LABEL, null, [], null, $isSelected
                );
            }
        }

        return $subButtons;
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
     * @throws \QuickformException
     */
    public function renderTableFilters(
        TableParameterValues $parameterValues, array $parameterNames
    ): string
    {
        $html = [];

        $html[] = $this->renderNumberOfItemsPerPageSelector($parameterValues, $parameterNames);

        //$html[] = $this->renderPropertySorting();

        return implode(PHP_EOL, $html);
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
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    public function renderTableHeader(
        string $tableName, TableParameterValues $parameterValues, array $parameterNames,
        ?TableActions $tableActions = null
    ): string
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
        $html[] = $this->renderTableFilters($parameterValues, $parameterNames);
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string[] $parameterNames
     * @param string[] $headerAttributes
     *
     * @throws \TableException
     */
    public function setColumnHeader(
        HTML_Table $htmlTable, array $parameterNames, TableParameterValues $parameterValues, int $columnIndex,
        string $label, bool $isSortable = true, ?array $headerAttributes = null
    )
    {
        $header = $htmlTable->getHeader();

        if ($isSortable)
        {
            $currentOrderColumnIndex = $parameterValues->getOrderColumnIndex();
            $currentOrderColumnDirection = $parameterValues->getOrderColumnDirection();

            if ($columnIndex != $currentOrderColumnIndex)
            {
                $currentOrderColumnIndex = $columnIndex;
                $currentOrderColumnDirection = SORT_ASC;
                $glyph = '';
            }
            else
            {
                if ($currentOrderColumnDirection == SORT_ASC)
                {
                    $currentOrderColumnDirection = SORT_DESC;
                    $glyphType = 'arrow-down-long';
                }
                else
                {
                    $currentOrderColumnDirection = SORT_ASC;
                    $glyphType = 'arrow-up-long';
                }

                $glyph = new FontAwesomeGlyph($glyphType);
                $glyph = $glyph->render();
            }

            $queryParameters = [
                $parameterNames[TableParameterValues::PARAM_PAGE_NUMBER] => $parameterValues->getPageNumber(),
                $parameterNames[TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE] => $parameterValues->getNumberOfRowsPerPage(
                ),
                $parameterNames[TableParameterValues::PARAM_ORDER_COLUMN_INDEX] => $currentOrderColumnIndex,
                $parameterNames[TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION] => $currentOrderColumnDirection
            ];

            $content = '<a href="' . $this->getUrlGenerator()->fromRequest($queryParameters) . '">' . $label . '</a> ' .
                $glyph;
        }
        else
        {
            $content = $label;
        }

        $header->setHeaderContents(0, $columnIndex, $content);
        $header->setColAttributes($columnIndex, $headerAttributes);
    }
}