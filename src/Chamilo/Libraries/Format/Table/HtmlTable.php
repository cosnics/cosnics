<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Platform\Session\Request;
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
    public const PARAM_NUMBER_OF_ITEMS_PER_PAGE = 'per_page';
    public const PARAM_ORDER_COLUMN = 'column';
    public const PARAM_ORDER_DIRECTION = 'direction';
    public const PARAM_PAGE_NUMBER = 'page_nr';
    public const PARAM_SELECT_ALL = 'selectall';

    /**
     * Additional parameters to pass in the URL
     *
     * @var string[]
     */
    private $additionalParameters;

    /**
     * @var bool
     */
    private $allowPageNavigation = true;

    /**
     * @var string[]
     */
    private $contentCellAttributes;

    /**
     * @var int
     */
    private $defaultOrderColumn;

    /**
     * @var int
     */
    private $defaultOrderDirection;

    /**
     * Additional attributes for the th-tags
     *
     * @var string[]
     */
    private $headerAttributes;

    /**
     * Number of items to display per page
     *
     * @var int
     */
    private $numberOfItemsPerPage;

    /**
     * @var int
     */
    private $orderColumn;

    /**
     * SORT_ASC or SORT_DESC
     *
     * @var int
     */
    private $orderDirection;

    /**
     * @var int
     */
    private $pageNumber;

    private Pager $pager;

    private PagerRenderer $pagerRenderer;

    /**
     * The function to get the total number of items
     *
     * @var string[]
     */
    private $sourceCountFunction;

    /**
     * @var string[][]
     */
    private $sourceData;

    /**
     * The total number of items in the table
     *
     * @var int
     */
    private $sourceDataCount;

    /**
     * The function to the the data to display
     *
     * @var string[]
     */
    private $sourceDataFunction;

    /**
     * A list of actions which will be available through a select list
     *
     * @var \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    private $tableFormActions;

    /**
     * @var string
     */
    private $tableName;

    private Translator $translator;

    public function __construct(Translator $translator, Pager $pager, PagerRenderer $pagerRenderer)
    {
        parent::__construct(['class' => $this->getTableClasses()], 0, true);

        $this->translator = $translator;
        $this->pager = $pager;
        $this->pagerRenderer = $pagerRenderer;

        $this->contentCellAttributes = [];
        $this->headerAttributes = [];
    }

    /**
     * @throws \TableException
     */
    public function render(
        int $numberOfItems, ArrayCollection $tableRows, string $tableName, int $numberOfRows, int $numberOfColumns,
        array $parameterNames, int $currentPageNumber, ?TableFormActions $tableFormActions = null
    ): string
    {
        if ($numberOfItems == 0)
        {
            return $this->getEmptyTable();
        }

        $html = [];

        $html[] = $this->renderTableHeader(
            $tableName, $numberOfItems, $numberOfRows, $numberOfColumns, $parameterNames, $tableFormActions
        );

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        $html[] = '<div class="' . $this->getTableContainerClasses() . '">';
        //$html[] = $this->renderTableBody();
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderTableFooter(
            $tableName, $currentPageNumber, $numberOfRows, $numberOfColumns, $numberOfItems, $parameterNames,
            $tableFormActions
        );

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     * @deprecated Use render() now
     */
    public function as_html(): string
    {
        return $this->render();
    }

    /**
     * @param int $selectedOrderColumn
     *
     * @return int[]
     */
    protected function determineOrderColumnQueryParameters($selectedOrderColumn): array
    {
        $currentOrderColumn = $this->getOrderColumn();
        $currentOrderDirection = $this->getOrderDirection();

        if ($selectedOrderColumn != $currentOrderColumn)
        {
            $currentOrderColumn = $selectedOrderColumn;
            $currentOrderDirection = SORT_ASC;
        }
        elseif ($currentOrderDirection == SORT_ASC)
        {
            $currentOrderDirection = SORT_DESC;
        }
        else
        {
            $currentOrderDirection = SORT_ASC;
        }

        return [$currentOrderColumn, $currentOrderDirection];
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
    abstract public function filterData(array $row): array;

    public function getActionsButtonToolbar(TableFormActions $tableFormActions): ButtonToolBar
    {
        $formActions = $tableFormActions->getFormActions();
        $formActionsCount = count($formActions);

        $firstAction = array_shift($formActions);

        $buttonToolBar = new ButtonToolBar();

        if ($formActionsCount > 1)
        {
            $button = new SplitDropdownButton(
                $firstAction->get_title(), null, $firstAction->get_action(), Button::DISPLAY_LABEL,
                $firstAction->getConfirmation(), ['btn-sm btn-table-action'], null, ['btn-table-action']
            );

            foreach ($formActions as $formAction)
            {
                $button->addSubButton(
                    new SubButton(
                        $formAction->get_title(), null, $formAction->get_action(), Button::DISPLAY_LABEL,
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
                    $firstAction->get_title(), null, $firstAction->get_action(), Button::DISPLAY_LABEL,
                    $firstAction->getConfirmation(), ['btn-sm', 'btn-table-action']
                )
            );
        }

        return $buttonToolBar;
    }

    /**
     * @return string[]
     */
    public function getAdditionalParameters(): array
    {
        return $this->additionalParameters;
    }

    /**
     * @param string [] $parameters
     */
    public function setAdditionalParameters(array $parameters)
    {
        $this->additionalParameters = $parameters;
    }

    public function getCheckboxHtml(string $value): string
    {
        $html = [];

        $html[] = '<div class="checkbox checkbox-primary">';
        $html[] = '<input class="styled styled-primary" type="checkbox" name="' .
            $this->getTableFormActions()->getIdentifierName() . '[]" value="' . $value . '"';

        if (Request::get($this->getParameterName(self::PARAM_SELECT_ALL)))
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
    public function getContentCellAttributes()
    {
        return $this->contentCellAttributes;
    }

    public function getDefaultOrderColumn(): int
    {
        return $this->defaultOrderColumn;
    }

    /**
     * @return int
     */
    public function getDefaultOrderDirection()
    {
        return $this->defaultOrderDirection;
    }

    /**
     * @return string
     * @throws \TableException
     */
    public function getEmptyTable()
    {
        $cols = $this->getHeader()->getColCount();

        $this->setCellAttributes(0, 0, 'style="font-style: italic;text-align:center;" colspan=' . $cols);
        $this->setCellContents(
            0, 0, $this->getTranslator()->trans('NoSearchResults', null, StringUtilities::LIBRARIES)
        );

        $html = [];

        $html[] = '<div class="table-responsive">';
        $html[] = parent::toHtml();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    abstract public function getFormClasses();

    /**
     * @return string[]
     */
    public function getHeaderAttributes()
    {
        return $this->headerAttributes;
    }

    /**
     * @return int
     */
    public function getNumberOfItemsPerPage()
    {
        return $this->numberOfItemsPerPage;
    }

    /**
     * @return int|int[]
     */
    public function getOrderColumn()
    {
        return $this->orderColumn;
    }

    /**
     * @return int
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     * @return int
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * Get the Pager object to split the shown data into several pages
     *
     * @return \Chamilo\Libraries\Format\Table\Pager
     */
    public function getPager()
    {
        return $this->pager;
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\PagerRenderer
     */
    public function getPagerRenderer()
    {
        return $this->pagerRenderer;
    }

    /**
     * @return string
     */
    abstract public function getTableActionsJavascript();

    /**
     * @return string
     */
    abstract public function getTableClasses();

    /**
     * @return string
     */
    abstract public function getTableContainerClasses();

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @return bool
     */
    public function isPageNavigationAllowed()
    {
        return $this->allowPageNavigation;
    }

    public function prepareTableData()
    {
        $this->processSourceData();
        $this->processCellAttributes();
    }

    public function processCellAttributes()
    {
        foreach ($this->headerAttributes as $column => & $attributes)
        {
            $this->setCellAttributes(0, $column, $attributes);
        }

        foreach ($this->contentCellAttributes as $column => $attributes)
        {
            $this->setColAttributes($column, $attributes);
        }
    }

    public function processSourceData()
    {
        $pager = $this->getPager();

        try
        {
            $offset = $pager->getCurrentRangeOffset();
        }
        catch (InvalidPageNumberException $exception)
        {
            $offset = 0;
        }

        $table_data = $this->getSourceData($offset);

        foreach ($table_data as $index => $row)
        {
            $row = $this->filterData($row);
            $this->addRow($row);
        }
    }

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

    public function renderNavigation(
        int $currentPageNumber, int $numberOfRows, int $numberOfColumns, int $numberOfItems, array $parameterNames
    ): string
    {
        //        $queryParameters = $this->getQueryParameters(
        //            null, $this->getNumberOfItemsPerPage(), $this->getOrderColumn(), $this->getOrderDirection()
        //        );

        return $this->getPagerRenderer()->renderPaginationWithPageLimit(
            $currentPageNumber, $numberOfRows, $numberOfColumns, $numberOfItems, [],
            $parameterNames[self::PARAM_PAGE_NUMBER]
        );
    }

    public function renderNumberOfItemsPerPageSelector(
        int $totalNumberOfitems, int $numberOfRows, int $numberOfColumns, array $parameterNames,
        ?int $orderColumnIndex = null, ?int $orderDirection = SORT_ASC
    ): string
    {

        if ($totalNumberOfitems <= Pager::DISPLAY_PER_INCREMENT)
        {
            return '';
        }

        //        $queryParameters = $this->getQueryParameters(
        //            null, null, $this->getOrderColumn(), $this->getOrderDirection()
        //        );

        return $this->getPagerRenderer()->renderItemsPerPageSelector(
            $totalNumberOfitems, $numberOfRows, $numberOfColumns, [],
            $parameterNames[self::PARAM_NUMBER_OF_ITEMS_PER_PAGE]
        );
    }

    /**
     * Get the HTML-code with the data-table.
     *
     * @return string
     * @throws \TableException
     */
    public function renderTableBody()
    {
        $this->prepareTableData();

        return HTML_Table::toHtml();
    }

    public function renderTableFilters(
        int $totalNumberOfitems, int $numberOfRows, int $numberOfColumns, array $parameterNames,
        ?int $orderColumnIndex = null, ?int $orderDirection = SORT_ASC
    ): string
    {
        return $this->renderNumberOfItemsPerPageSelector(
            $totalNumberOfitems, $numberOfRows, $numberOfColumns, $parameterNames, $orderColumnIndex, $orderDirection
        );
    }

    public function renderTableFooter(
        string $tableName, int $currentPageNumber, int $numberOfRows, int $numberOfColumns, int $numberOfItems,
        array $parameterNames, ?TableFormActions $tableFormActions = null
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
        $html[] = $this->renderNavigation(
            $currentPageNumber, $numberOfRows, $numberOfColumns, $numberOfItems, $parameterNames
        );
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
     * @return string
     */
    public function renderTableHeader(
        string $tableName, int $totalNumberOfitems, int $numberOfRows, int $numberOfColumns, array $parameterNames,
        ?TableFormActions $tableFormActions = null, ?int $orderColumnIndex = null, ?int $orderDirection = SORT_ASC
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

        $html[] = $this->renderTableFilters(
            $totalNumberOfitems, $numberOfRows, $numberOfColumns, $parameterNames
        );
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param int $orderColumn
     * @param string $label
     * @param bool $isSortable
     * @param string[] $headerAttributes
     * @param string[] $cellAttributes
     *
     * @return string
     */
    public function setColumnHeader(
        $orderColumn, $label, $isSortable = true, $headerAttributes = null, $cellAttributes = null
    )
    {
        $header = $this->getHeader();

        $header->setColAttributes($orderColumn, $headerAttributes);

        $requestedOrderColumn = $this->getOrderColumn();
        $requestedOrderDirection = $this->getOrderDirection();

        $isOrdercolumn = $orderColumn == $requestedOrderColumn;

        if ($isOrdercolumn)
        {
            if ($requestedOrderDirection == SORT_ASC)
            {
                $isOrderColumnAndAscending = true;
            }
            else
            {
                $isOrderColumnAndAscending = false;
            }
        }
        else
        {
            $isOrderColumnAndAscending = false;
        }

        // TODO: Make sure these parameters RETAIN the already selected sorting columns

        $orderColumnQueryParameters = $this->determineOrderColumnQueryParameters($orderColumn);

        $queryParameters = $this->getQueryParameters(
            $this->getPageNumber(), $this->getNumberOfItemsPerPage(), $orderColumnQueryParameters[0],
            $orderColumnQueryParameters[1]
        );

        if ($isSortable)
        {
            $headerUrl = new Redirect($queryParameters);

            $link = '<a href="' . $headerUrl->getUrl() . '">' . $label . '</a>';

            if ($isOrdercolumn)
            {
                if ($isOrderColumnAndAscending)
                {
                    $glyphType = 'arrow-down-long';
                }
                else
                {
                    $glyphType = 'arrow-up-long';
                }

                $glyph = new FontAwesomeGlyph($glyphType);
                $link .= ' ' . $glyph->render();
            }
        }
        else
        {
            $link = $label;
        }

        $header->setHeaderContents(0, $orderColumn, $link);

        if (!is_null($cellAttributes))
        {
            $this->contentCellAttributes[$orderColumn] = $cellAttributes;
        }

        if (!is_null($headerAttributes))
        {
            $this->headerAttributes[$orderColumn] = $headerAttributes;
        }

        return $link;
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\FormAction\TableFormActions $actions
     */
    public function setTableFormActions(TableFormActions $actions = null)
    {
        $this->tableFormActions = $actions;
    }

    /**
     * @deprecated User render() now
     */
    public function toHtml(bool $emptyTable = false): string
    {
        return $this->render($emptyTable);
    }
}
