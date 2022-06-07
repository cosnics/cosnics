<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_Table;

/**
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class HtmlTable extends HTML_Table
{
    const PARAM_NUMBER_OF_ITEMS_PER_PAGE = 'per_page';
    const PARAM_ORDER_COLUMN = 'column';
    const PARAM_ORDER_DIRECTION = 'direction';
    const PARAM_PAGE_NUMBER = 'page_nr';
    const PARAM_SELECT_ALL = 'selectall';

    /**
     *
     * @var boolean
     */
    protected $allowMultiSort = false;

    /**
     * Additional parameters to pass in the URL
     *
     * @var string[]
     */
    private $additionalParameters;

    /**
     *
     * @var boolean
     */
    private $allowPageNavigation = true;

    /**
     *
     * @var boolean
     */
    private $allowPageSelection = true;

    /**
     *
     * @var string[]
     */
    private $contentCellAttributes;

    /**
     *
     * @var integer
     */
    private $defaultOrderColumn;

    /**
     *
     * @var integer
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
     * @var integer
     */
    private $numberOfItemsPerPage;

    /**
     *
     * @var integer
     */
    private $orderColumn;

    /**
     * SORT_ASC or SORT_DESC
     *
     * @var integer
     */
    private $orderDirection;

    /**
     *
     * @var integer
     */
    private $pageNumber;

    /**
     * The pager object to split the data in several pages
     *
     * @var \Chamilo\Libraries\Format\Table\Pager
     */
    private $pager;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\PagerRenderer
     */
    private $pagerRenderer;

    /**
     * The function to get the total number of items
     *
     * @var string[]
     */
    private $sourceCountFunction;

    /**
     *
     * @var string[][]
     */
    private $sourceData;

    /**
     * The total number of items in the table
     *
     * @var integer
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
     *
     * @var string
     */
    private $tableName;

    /**
     *
     * @param string $tableName
     * @param string[] $sourceCountFunction
     * @param string[] $sourceDataFunction
     * @param integer $defaultOrderColumn
     * @param integer $defaultNumberOfItemsPerPage
     * @param integer $defaultOrderDirection
     * @param boolean $allowPageSelection
     * @param boolean $allowPageNavigation
     * @param boolean $allowMultiSort
     */
    public function __construct(
        $tableName = 'table', $sourceCountFunction = null, $sourceDataFunction = null, $defaultOrderColumn = 1,
        $defaultNumberOfItemsPerPage = 20, $defaultOrderDirection = SORT_ASC, $allowPageSelection = true,
        $allowPageNavigation = true, $allowMultiSort = false
    )
    {
        parent::__construct(array('class' => $this->getTableClasses(), 'id' => $tableName), 0, true);

        $this->tableName = $tableName;
        $this->additionalParameters = [];

        $this->defaultOrderColumn = $defaultOrderColumn;
        $this->defaultOrderDirection = $defaultOrderDirection;

        $this->pageNumber = $this->determinePageNumber();
        $this->orderColumn = $this->determineOrderColumn();
        $this->orderDirection = $this->determineOrderDirection();
        $this->numberOfItemsPerPage = $this->determineNumberOfItemsPerPage($defaultNumberOfItemsPerPage);

        $this->allowPageSelection = $allowPageSelection;
        $this->allowPageNavigation = $allowPageNavigation;
        $this->allowMultiSort = $allowMultiSort;

        $this->sourceCountFunction = $sourceCountFunction;
        $this->sourceDataFunction = $sourceDataFunction;

        $this->pager = null;
        $this->sourceDataCount = null;

        $this->tableFormActions = null;
        $this->contentCellAttributes = [];
        $this->headerAttributes = [];
    }

    /**
     * Returns the complete table HTML.
     *
     * @param boolean $empty_table
     *
     * @return string
     */
    public function render($empty_table = false)
    {
        if ($this->countSourceData() == 0)
        {
            return $this->getEmptyTable();
        }

        $html = [];

        if (!$empty_table)
        {
            $html[] = $this->renderTableHeader();
        }

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        $html[] = '<div class="' . $this->getTableContainerClasses() . '">';
        $html[] = $this->renderTableBody();
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        if (!$empty_table)
        {
            $html[] = $this->renderTableFooter();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     * @deprecated Use render() now
     */
    public function as_html()
    {
        return $this->render();
    }

    /**
     *
     * @return integer
     */
    public function countSourceData()
    {
        if (is_null($this->sourceDataCount))
        {
            $this->sourceDataCount = call_user_func($this->getSourceCountFunction());
        }

        return $this->sourceDataCount;
    }

    /**
     * @param integer $defaultNumberOfItemsPerPage
     *
     * @return integer
     */
    protected function determineNumberOfItemsPerPage($defaultNumberOfItemsPerPage)
    {
        $variableName = $this->getParameterName(self::PARAM_NUMBER_OF_ITEMS_PER_PAGE);
        $requestedNumberOfItemsPerPage = Request::get($variableName);

        return $requestedNumberOfItemsPerPage ?: $defaultNumberOfItemsPerPage;
    }

    /**
     *
     * @return integer[]
     */
    protected function determineOrderColumn()
    {
        $variableName = $this->getParameterName(self::PARAM_ORDER_COLUMN);
        $requestedOrderColumn = Request::get($variableName, []);

        if (!is_array($requestedOrderColumn))
        {
            $requestedOrderColumn = array($requestedOrderColumn);
        }

        return !empty($requestedOrderColumn) ? $requestedOrderColumn : array($this->getDefaultOrderColumn());
    }

    /**
     *
     * @param integer $selectedOrderColumn
     *
     * @return integer[][]
     */
    protected function determineOrderColumnQueryParameters($selectedOrderColumn)
    {
        $currentOrderColumns = $this->getOrderColumn();
        $currentOrderDirections = $this->getOrderDirection();

        if (!in_array($selectedOrderColumn, $currentOrderColumns))
        {
            if ($this->allowMultiSort)
            {
                $currentOrderColumns[] = $selectedOrderColumn;
                $currentOrderDirections[] = SORT_ASC;
            }
            else
            {
                $currentOrderColumns = $selectedOrderColumn;
                $currentOrderDirections = SORT_ASC;
            }
        }
        else
        {
            $selectedOrderColumnIndex = array_search($selectedOrderColumn, $currentOrderColumns);

            // If the column was sorted ascending, now sort it descending. If it was sorted descending, remove the
            // sorting for that column
            if ($currentOrderDirections[$selectedOrderColumnIndex] == SORT_ASC)
            {
                $currentOrderDirections[$selectedOrderColumnIndex] = SORT_DESC;
            }
            else
            {
                if ($selectedOrderColumn == $this->getDefaultOrderColumn() && count($currentOrderColumns) == 1)
                {
                    $currentOrderDirections[$selectedOrderColumnIndex] = SORT_ASC;
                }
                else
                {
                    unset($currentOrderColumns[$selectedOrderColumnIndex]);
                    unset($currentOrderDirections[$selectedOrderColumnIndex]);

                    $currentOrderColumns = array_values($currentOrderColumns);
                    $currentOrderDirections = array_values($currentOrderDirections);
                }
            }
        }

        return array($currentOrderColumns, $currentOrderDirections);
    }

    /**
     *
     * @return integer[]
     */
    protected function determineOrderDirection()
    {
        $variableName = $this->getParameterName(self::PARAM_ORDER_DIRECTION);
        $requestedOrderDirection = Request::get($variableName, []);

        if (!is_array($requestedOrderDirection))
        {
            $requestedOrderDirection = array($requestedOrderDirection);
        }

        return !empty($requestedOrderDirection) ? $requestedOrderDirection : array($this->getDefaultOrderDirection());
    }

    /**
     *
     * @return integer
     */
    protected function determinePageNumber()
    {
        $variableName = $this->getParameterName(self::PARAM_PAGE_NUMBER);
        $requestedPageNumber = Request::get($variableName);

        return $requestedPageNumber ?: 1;
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
    abstract public function filterData($row);

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar
     */
    public function getActionsButtonToolbar()
    {
        $formActions = $this->getTableFormActions()->get_form_actions();
        $formActionsCount = count($formActions);

        $firstAction = array_shift($formActions);

        $buttonToolBar = new ButtonToolBar();

        if ($formActionsCount > 1)
        {
            $button = new SplitDropdownButton(
                $firstAction->get_title(), null, $firstAction->get_action(), Button::DISPLAY_LABEL,
                $firstAction->getConfirmation(), 'btn-sm btn-table-action'
            );
            $button->setDropdownClasses('btn-table-action');

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
                    $firstAction->getConfirmation(), 'btn-sm btn-table-action'
                )
            );
        }

        return $buttonToolBar;
    }

    /**
     *
     * @return string[]
     */
    public function getAdditionalParameters()
    {
        return $this->additionalParameters;
    }

    /**
     *
     * @param string []
     */
    public function setAdditionalParameters($parameters)
    {
        $this->additionalParameters = $parameters;
    }

    /**
     *
     * @param integer $value
     *
     * @return string
     */
    public function getCheckboxHtml($value)
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
     *
     * @return integer
     */
    abstract public function getColumnCount();

    /**
     *
     * @return string[]
     */
    public function getContentCellAttributes()
    {
        return $this->contentCellAttributes;
    }

    /**
     *
     * @return integer
     */
    public function getDefaultOrderColumn()
    {
        return $this->defaultOrderColumn;
    }

    /**
     *
     * @return integer
     */
    public function getDefaultOrderDirection()
    {
        return $this->defaultOrderDirection;
    }

    /**
     *
     * @return string
     */
    public function getEmptyTable()
    {
        $cols = $this->getHeader()->getColCount();

        $this->setCellAttributes(0, 0, 'style="font-style: italic;text-align:center;" colspan=' . $cols);
        $this->setCellContents(0, 0, Translation::get('NoSearchResults', null, StringUtilities::LIBRARIES));

        $html = [];

        $html[] = '<div class="table-responsive">';
        $html[] = parent::toHTML();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    abstract public function getFormClasses();

    /**
     *
     * @return string[]
     */
    public function getHeaderAttributes()
    {
        return $this->headerAttributes;
    }

    /**
     *
     * @return integer
     */
    public function getNumberOfItemsPerPage()
    {
        return $this->numberOfItemsPerPage;
    }

    /**
     *
     * @return integer
     */
    public function getOrderColumn()
    {
        return $this->orderColumn;
    }

    /**
     *
     * @return integer
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     *
     * @return integer
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
        if (is_null($this->pager))
        {
            $numberOfItemsPerPage = $this->getNumberOfItemsPerPage();
            $actualNumberOfItemsPerPage =
                $numberOfItemsPerPage == Pager::DISPLAY_ALL ? $this->countSourceData() : $numberOfItemsPerPage;

            $this->pager = new Pager(
                $actualNumberOfItemsPerPage, $this->getColumnCount(), $this->countSourceData(), $this->getPageNumber()
            );
        }

        return $this->pager;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\PagerRenderer
     */
    public function getPagerRenderer()
    {
        if (!isset($this->pagerRenderer))
        {
            $this->pagerRenderer = new PagerRenderer($this->getPager());
        }

        return $this->pagerRenderer;
    }

    /**
     *
     * @param string $parameter
     *
     * @return string
     */
    public function getParameterName($parameter)
    {
        return $this->getTableName() . '_' . $parameter;
    }

    /**
     *
     * @param integer $pageNumber
     * @param integer $numberOfItemsPerPage
     * @param integer $orderColumn
     * @param integer $orderDirection
     *
     * @return string[]
     */
    public function getQueryParameters(
        $pageNumber = null, $numberOfItemsPerPage = null, $orderColumn = null, $orderDirection = null
    )
    {
        $queryParameters = [];

        if (!is_null($pageNumber))
        {
            $queryParameters[$this->getParameterName(self::PARAM_PAGE_NUMBER)] = $pageNumber;
        }

        if (!is_null($numberOfItemsPerPage))
        {
            $queryParameters[$this->getParameterName(self::PARAM_NUMBER_OF_ITEMS_PER_PAGE)] = $numberOfItemsPerPage;
        }

        if (!is_null($orderColumn))
        {
            $queryParameters[$this->getParameterName(self::PARAM_ORDER_COLUMN)] = $orderColumn;
        }

        if (!is_null($orderDirection))
        {
            $queryParameters[$this->getParameterName(self::PARAM_ORDER_DIRECTION)] = $orderDirection;
        }

        return array_merge($queryParameters, $this->getAdditionalParameters());
    }

    /**
     *
     * @return string[]
     */
    public function getSourceCountFunction()
    {
        return $this->sourceCountFunction;
    }

    /**
     * Get the data to display.
     * This function calls the function given as 2nd argument in the constructor of a
     * SortableTable. Make sure your function has the same parameters as defined here.
     *
     * @param integer $offset
     *
     * @return string[]
     */
    public function getSourceData($offset = null)
    {
        if (!is_null($this->getSourceDataFunction()))
        {
            if (is_null($this->sourceData))
            {
                $this->sourceData = call_user_func(
                    $this->getSourceDataFunction(), $offset, $this->getPager()->getNumberOfItemsPerPage(),
                    $this->getOrderColumn(), $this->getOrderDirection()
                );
            }

            return $this->sourceData;
        }

        return [];
    }

    /**
     *
     * @return string[]
     */
    public function getSourceDataFunction()
    {
        return $this->sourceDataFunction;
    }

    /**
     *
     * @return string
     */
    abstract public function getTableActionsJavascript();

    /**
     *
     * @return string
     */
    abstract public function getTableClasses();

    /**
     *
     * @return string
     */
    abstract public function getTableContainerClasses();

    /**
     * Returns the filter parameters for this table
     *
     * @return string[]
     */
    public function getTableFilterParameters()
    {
        return array(
            $this->getParameterName(self::PARAM_PAGE_NUMBER) => $this->getPageNumber(),
            $this->getParameterName(self::PARAM_ORDER_COLUMN) => $this->getOrderColumn(),
            $this->getParameterName(self::PARAM_ORDER_DIRECTION) => $this->getOrderDirection(),
            $this->getParameterName(self::PARAM_NUMBER_OF_ITEMS_PER_PAGE) => $this->getNumberOfItemsPerPage()
        );
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    public function getTableFormActions()
    {
        return $this->tableFormActions;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Table\FormAction\TableFormActions $actions
     */
    public function setTableFormActions(TableFormActions $actions = null)
    {
        $this->tableFormActions = $actions;
    }

    /**
     *
     * @return string
     */
    protected function getTableName()
    {
        return $this->tableName;
    }

    /**
     *
     * @return boolean
     */
    public function isPageNavigationAllowed()
    {
        return $this->allowPageNavigation;
    }

    /**
     *
     * @return boolean
     */
    public function isPageSelectionAllowed()
    {
        return $this->allowPageSelection;
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

    /**
     *
     * @return string
     */
    public function renderActions()
    {
        $buttonToolBarRenderer = new ButtonToolBarRenderer($this->getActionsButtonToolbar());

        $html = [];

        $html[] = $buttonToolBarRenderer->render();
        $html[] = '<input type="hidden" name="' . $this->tableName . '_namespace" value="' .
            $this->getTableFormActions()->get_namespace() . '"/>';
        $html[] = '<input type="hidden" name="table_name" value="' . $this->tableName . '"/>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderNavigation()
    {
        $queryParameters = $this->getQueryParameters(
            null, $this->getNumberOfItemsPerPage(), $this->getOrderColumn(), $this->getOrderDirection()
        );

        if ($this->isPageNavigationAllowed())
        {
            return $this->getPagerRenderer()->renderPaginationWithPageLimit(
                $queryParameters, $this->getParameterName(self::PARAM_PAGE_NUMBER)
            );
        }

        return '';
    }

    /**
     * Get the HTML-code wich represents a form to select how many items a page should contain.
     *
     * @return string
     */
    public function renderNumberOfItemsPerPageSelector()
    {
        if ($this->isPageSelectionAllowed())
        {
            $sourceDataCount = $this->countSourceData();

            if ($sourceDataCount <= Pager::DISPLAY_PER_INCREMENT)
            {
                return '';
            }

            $queryParameters = $this->getQueryParameters(
                null, null, $this->getOrderColumn(), $this->getOrderDirection()
            );

            return $this->getPagerRenderer()->renderItemsPerPageSelector(
                $queryParameters, $this->getParameterName(self::PARAM_NUMBER_OF_ITEMS_PER_PAGE)
            );
        }

        return '';
    }

    /**
     * Get the HTML-code with the data-table.
     *
     * @return string
     */
    public function renderTableBody()
    {
        $this->prepareTableData();

        return HTML_Table::toHTML();
    }

    /**
     *
     * @return string
     */
    public function renderTableFilters()
    {
        return $this->renderNumberOfItemsPerPageSelector();
    }

    /**
     *
     * @return string
     */
    public function renderTableFooter()
    {
        $hasFormActions = $this->getTableFormActions() instanceof TableFormActions &&
            $this->getTableFormActions()->has_form_actions();
        $allowNavigation = $this->isPageSelectionAllowed() || $this->isPageNavigationAllowed();

        $html = [];

        if ($hasFormActions || $allowNavigation)
        {
            $html[] = '<div class="row">';

            if ($hasFormActions)
            {
                $classes = 'col-xs-12';

                if ($allowNavigation)
                {
                    $classes .= ' col-md-6';
                }

                $html[] = '<div class="' . $classes . ' table-navigation-actions">';
                $html[] = $this->renderActions();
                $html[] = '</div>';
            }

            if ($allowNavigation)
            {
                $classes = 'col-xs-12';

                if ($hasFormActions)
                {
                    $classes .= ' col-md-6';
                }

                $html[] = '<div class="' . $classes . ' table-navigation-pagination">';
                $html[] = $this->renderNavigation();
                $html[] = '</div>';
            }

            $html[] = '</div>';
        }

        if ($hasFormActions)
        {
            $html[] = '<input type="submit" name="Submit" value="Submit" style="display:none;" />';
            $html[] = '</form>';
            $html[] = $this->getTableActionsJavascript();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderTableHeader()
    {
        $hasFormActions = $this->getTableFormActions() instanceof TableFormActions &&
            $this->getTableFormActions()->has_form_actions();

        $html = [];

        if ($hasFormActions)
        {
            $tableFormActions = $this->getTableFormActions()->get_form_actions();
            $firstFormAction = array_shift($tableFormActions);

            $html[] = '<form class="' . $this->getFormClasses() . '" method="post" action="' .
                $firstFormAction->get_action() . '" name="form_' . $this->tableName . '">';
        }

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-md-6 table-navigation-actions">';

        if ($hasFormActions)
        {
            $html[] = $this->renderActions();
        }

        $html[] = '</div>';

        $classes = 'col-xs-12';

        if ($hasFormActions)
        {
            $classes .= ' col-md-6';
        }

        $html[] = '<div class="' . $classes . ' table-navigation-search">';
        $html[] = $this->renderTableFilters();
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param integer $orderColumn
     * @param string $label
     * @param boolean $isSortable
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

        // TODO: Make sure the order column index and direction index match

        $requestedOrderColumns = $this->getOrderColumn();
        $requestedOrderDirections = $this->getOrderDirection();

        $isOrdercolumn = in_array($orderColumn, $requestedOrderColumns);

        if ($isOrdercolumn)
        {
            $orderColumnIndex = array_search($orderColumn, $requestedOrderColumns);
            $requestedOrderDirection = $requestedOrderDirections[$orderColumnIndex];

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
                    $link .= ' &#8595;';
                }
                else
                {
                    $link .= ' &#8593;';
                }
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
     * @param boolean $emptyTable
     *
     * @return string
     * @deprecated User render() now
     */
    public function toHtml($emptyTable = false)
    {
        return $this->render($emptyTable);
    }
}
