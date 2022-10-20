<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\AbstractSortableTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
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
abstract class HtmlTable extends HTML_Table
{
    public const PARAM_NUMBER_OF_COLUMNS_PER_PAGE = 'columns_per_page';
    public const PARAM_NUMBER_OF_ROWS_PER_PAGE = 'per_page';
    public const PARAM_ORDER_COLUMN_DIRECTION = 'direction';
    public const PARAM_ORDER_COLUMN_INDEX = 'column';
    public const PARAM_PAGE_NUMBER = 'page_nr';
    public const PARAM_SELECT_ALL = 'selectall';
    public const PARAM_TOTAL_NUMBER_OF_ITEMS = 'total';

    /**
     * @var string[]
     */
    protected array $contentCellAttributes;

    /**
     * @var string[]
     */
    protected array $headerAttributes;

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
        parent::__construct(['class' => $this->getTableClasses()], 0, true);

        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->pager = $pager;
        $this->pagerRenderer = $pagerRenderer;
        $this->security = $security;

        $this->contentCellAttributes = [];
        $this->headerAttributes = [];
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
        TableParameterValues $parameterValues, ?TableFormActions $tableFormActions = null
    ): string
    {
        if ($parameterValues->getTotalNumberOfItems() == 0)
        {
            return $this->getEmptyTable();
        }

        $this->setupTableColumns(
            $tableColumns, $parameterNames, $parameterValues, $tableFormActions
        );

        $html = [];

        $html[] = $this->renderTableHeader(
            $tableName, $parameterValues, $parameterNames, $tableFormActions
        );

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        $html[] = '<div class="' . $this->getTableContainerClasses() . '">';
        $html[] = $this->renderTableBody($tableRows, $parameterValues, $tableFormActions);
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderTableFooter(
            $tableName, $parameterValues, $parameterNames, $tableFormActions
        );

        return implode(PHP_EOL, $html);
    }

    /**
     * Transform all data in a table-row, using the filters defined by the function set_column_filter(...) defined
     * elsewhere in this class.
     * If you've defined actions, the first element of the given row will be converted into a
     * checkbox
     *
     * @param string[] $row
     *
     * @return string[]
     */
    abstract public function filterData(
        array $row, TableParameterValues $parameterValues, ?TableFormActions $tableFormActions = null
    ): array;

    public function getActionsButtonToolbar(TableFormActions $tableFormActions): ButtonToolBar
    {
        $formActions = $tableFormActions->getFormActions();
        $formActionsCount = count($formActions);

        $firstAction = array_shift($formActions);

        $buttonToolBar = new ButtonToolBar();

        if ($formActionsCount > 1)
        {
            $button = new SplitDropdownButton(
                $firstAction->get_title(), null, $firstAction->get_action(), AbstractButton::DISPLAY_LABEL,
                $firstAction->getConfirmation(), ['btn-sm btn-table-action'], null, ['btn-table-action']
            );

            foreach ($formActions as $formAction)
            {
                $button->addSubButton(
                    new SubButton(
                        $formAction->get_title(), null, $formAction->get_action(), AbstractButton::DISPLAY_LABEL,
                        $formAction->getConfirmation()
                    )
                );
            }

            $buttonToolBar->addItem($button);
        }
        else
        {
            $buttonToolBar->addItem(
                new Button(
                    $firstAction->get_title(), null, $firstAction->get_action(), AbstractButton::DISPLAY_LABEL,
                    $firstAction->getConfirmation(), ['btn-sm', 'btn-table-action']
                )
            );
        }

        return $buttonToolBar;
    }

    public function getCheckboxHtml(
        TableFormActions $tableFormActions, TableParameterValues $parameterValues, string $value
    ): string
    {
        $html = [];

        $html[] = '<div class="checkbox checkbox-primary">';
        $html[] =
            '<input class="styled styled-primary" type="checkbox" name="' . $tableFormActions->getIdentifierName() .
            '[]" value="' . $value . '"';

        if ($parameterValues->getSelectAll())
        {
            $html[] = ' checked="checked"';
        }

        $html[] = '/>';
        $html[] = '<label></label>';
        $html[] = '</div>';

        return implode('', $html);
    }

    /**
     * @return string[]
     */
    public function getContentCellAttributes(): array
    {
        return $this->contentCellAttributes;
    }

    /**
     * @throws \TableException
     */
    public function getEmptyTable(): string
    {
        $cols = $this->getHeader()->getColCount();

        $this->setCellAttributes(0, 0, 'style="font-style: italic;text-align:center;" colspan=' . $cols);
        $this->setCellContents(
            0, 0, $this->getTranslator()->trans('NoSearchResults', [], StringUtilities::LIBRARIES)
        );

        $html = [];

        $html[] = '<div class="table-responsive">';
        $html[] = parent::toHtml();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    abstract public function getFormClasses(): string;

    /**
     * @return string[]
     */
    public function getHeaderAttributes(): array
    {
        return $this->headerAttributes;
    }

    public function getPagerRenderer(): PagerRenderer
    {
        return $this->pagerRenderer;
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    abstract public function getTableActionsJavascript(): string;

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
     * @throws \TableException
     */
    public function prepareTableData(
        ArrayCollection $tableRows, TableParameterValues $parameterValues, ?TableFormActions $tableFormActions = null
    )
    {
        $this->processSourceData($tableRows, $parameterValues, $tableFormActions);
        $this->processCellAttributes();
    }

    /**
     * @throws \TableException
     */
    public function processCellAttributes()
    {
        foreach ($this->headerAttributes as $column => $headerAttribute)
        {
            $this->setCellAttributes(0, $column, $headerAttribute);
        }

        foreach ($this->contentCellAttributes as $column => $contentCellAttribute)
        {
            $this->setColAttributes($column, $contentCellAttribute);
        }
    }

    /**
     * @throws \TableException
     */
    public function processSourceData(
        ArrayCollection $tableRows, TableParameterValues $parameterValues, ?TableFormActions $tableFormActions = null
    )
    {
        foreach ($tableRows as $row)
        {
            $row = $this->filterData($row, $parameterValues, $tableFormActions);
            $this->addRow($row);
        }
    }

    /**
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    public function renderActions(string $tableName, TableFormActions $tableFormActions): string
    {
        $buttonToolBarRenderer = new ButtonToolBarRenderer($this->getActionsButtonToolbar($tableFormActions));

        $html = [];

        $html[] = $buttonToolBarRenderer->render();
        $html[] =
            '<input type="hidden" name="' . $tableName . '_namespace" value="' . $tableFormActions->get_namespace() .
            '"/>';
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
     * @throws \TableException
     */
    public function renderTableBody(
        ArrayCollection $tableRows, TableParameterValues $parameterValues, ?TableFormActions $tableFormActions = null
    ): string
    {
        $this->prepareTableData($tableRows, $parameterValues, $tableFormActions);

        return HTML_Table::toHtml();
    }

    /**
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    public function renderTableFilters(
        TableParameterValues $parameterValues, array $parameterNames
    ): string
    {
        return $this->renderNumberOfItemsPerPageSelector($parameterValues, $parameterNames);
    }

    /**
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function renderTableFooter(
        string $tableName, TableParameterValues $parameterValues, array $parameterNames,
        ?TableFormActions $tableFormActions = null
    ): string
    {
        $hasFormActions = $tableFormActions instanceof TableFormActions && $tableFormActions->hasFormActions();

        $html = [];

        $html[] = '<div class="row">';

        if ($hasFormActions)
        {
            $html[] = '<div class="col-xs-12 col-md-6 table-navigation-actions">';
            $html[] = $this->renderActions($tableName, $tableFormActions);
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
        ?TableFormActions $tableFormActions = null
    ): string
    {
        $hasFormActions = $tableFormActions instanceof TableFormActions && $tableFormActions->hasFormActions();

        $html = [];

        if ($hasFormActions)
        {
            $formActions = $tableFormActions->getFormActions();
            $firstFormAction = array_shift($formActions);

            $html[] = '<form class="' . $this->getFormClasses() . '" method="post" action="' .
                $firstFormAction->get_action() . '" name="form_' . $tableName . '">';
        }

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-md-6 table-navigation-actions">';

        if ($hasFormActions)
        {
            $html[] = $this->renderActions($tableName, $tableFormActions);
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
     * @param string[] $cellAttributes
     *
     * @throws \TableException
     */
    public function setColumnHeader(
        array $parameterNames, TableParameterValues $parameterValues, int $columnIndex, string $label,
        bool $isSortable = true, ?array $headerAttributes = null, ?array $cellAttributes = null
    )
    {
        $header = $this->getHeader();

        $header->setColAttributes($columnIndex, $headerAttributes);

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

        if (!is_null($cellAttributes))
        {
            $this->contentCellAttributes[$columnIndex] = $cellAttributes;
        }

        if (!is_null($headerAttributes))
        {
            $this->headerAttributes[$columnIndex] = $headerAttributes;
        }
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     * @param string[] $parameterNames
     *
     * @throws \TableException
     */
    protected function setupTableColumns(
        array $tableColumns, array $parameterNames, TableParameterValues $parameterValues,
        ?TableFormActions $tableFormActions = null
    )
    {
        if ($tableFormActions instanceof TableFormActions && $tableFormActions->hasFormActions())
        {
            $columnHeaderHtml =
                '<div class="checkbox checkbox-primary"><input class="styled styled-primary sortableTableSelectToggle" type="checkbox" name="sortableTableSelectToggle" /><label></label></div>';
            $this->setColumnHeader($parameterNames, $parameterValues, 0, $columnHeaderHtml, false);
        }

        foreach ($tableColumns as $key => $tableColumn)
        {
            $headerAttributes = $contentAttributes = [];

            $cssClasses = $tableColumn->getCssClasses();

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]))
            {
                $headerAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER];
            }

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT]))
            {
                $contentAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT];
            }

            $this->setColumnHeader(
                $parameterNames, $parameterValues, ($tableFormActions instanceof TableFormActions ? $key + 1 : $key),
                $this->getSecurity()->removeXSS($tableColumn->get_title()),
                $tableColumn instanceof AbstractSortableTableColumn && $tableColumn->is_sortable(), $headerAttributes,
                $contentAttributes
            );
        }
    }
}
