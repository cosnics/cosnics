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
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
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
        TableParameterValues $parameterValues, ?TableActions $tableActions = null
    ): string
    {
        if ($parameterValues->getTotalNumberOfItems() == 0)
        {
            return $this->getEmptyTable();
        }

        $this->processTableColumns(
            $tableColumns, $parameterNames, $parameterValues, $tableActions
        );

        $html = [];

        $html[] = $this->renderTableHeader(
            $tableName, $parameterValues, $parameterNames, $tableActions
        );

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        $html[] = '<div class="' . $this->getTableContainerClasses() . '">';
        $html[] = $this->renderTableBody($tableColumns, $tableRows, $tableActions);
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

        return $buttonToolBar;
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
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @throws \TableException
     */
    public function prepareTableData(
        array $tableColumns, ArrayCollection $tableRows, ?TableActions $tableActions = null
    )
    {
        $this->processSourceData($tableRows);
        $this->processCellAttributes($tableColumns, $tableActions);
        $this->processEmptyCells();
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @throws \TableException
     */
    public function processCellAttributes(array $tableColumns, ?TableActions $tableActions = null)
    {
        foreach ($tableColumns as $key => $tableColumn)
        {
            $cssClasses = $tableColumn->getCssClasses();

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT]))
            {
                $contentAttributes = ['class' => $cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]];

                $this->setColAttributes(
                    ($tableActions instanceof TableActions && $tableActions->hasActions() ? $key + 1 : $key),
                    $contentAttributes
                );
            }
        }
    }

    /**
     * @throws \TableException
     */
    protected function processEmptyCells()
    {
        $this->setAutoFill('-');
    }

    /**
     * @throws \TableException
     */
    public function processSourceData(
        ArrayCollection $tableRows
    )
    {
        foreach ($tableRows as $row)
        {
            $this->addRow($row);
        }
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     * @param string[] $parameterNames
     *
     * @throws \TableException
     */
    protected function processTableColumns(
        array $tableColumns, array $parameterNames, TableParameterValues $parameterValues,
        ?TableActions $tableActions = null
    )
    {
        if ($tableActions instanceof TableActions && $tableActions->hasActions())
        {
            $columnHeaderHtml =
                '<div class="checkbox checkbox-primary"><input class="styled styled-primary sortableTableSelectToggle" type="checkbox" name="sortableTableSelectToggle" /><label></label></div>';
            $this->setColumnHeader($parameterNames, $parameterValues, 0, $columnHeaderHtml, false);
        }

        foreach ($tableColumns as $key => $tableColumn)
        {
            $headerAttributes = [];

            $cssClasses = $tableColumn->getCssClasses();

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]))
            {
                $headerAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER];
            }

            $this->setColumnHeader(
                $parameterNames, $parameterValues,
                ($tableActions instanceof TableActions && $tableActions->hasActions() ? $key + 1 : $key),
                $this->getSecurity()->removeXSS($tableColumn->get_title()),
                $tableColumn instanceof AbstractSortableTableColumn && $tableColumn->is_sortable(), $headerAttributes
            );
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
     * @throws \TableException
     */
    public function renderTableBody(
        array $tableColumns, ArrayCollection $tableRows, ?TableActions $tableActions = null
    ): string
    {
        $this->prepareTableData($tableColumns, $tableRows, $tableActions);

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
        array $parameterNames, TableParameterValues $parameterValues, int $columnIndex, string $label,
        bool $isSortable = true, ?array $headerAttributes = null
    )
    {
        $header = $this->getHeader();

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
