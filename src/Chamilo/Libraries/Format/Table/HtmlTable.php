<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class HtmlTable extends \HTML_Table
{

    /**
     *
     * @var string
     */
    private $tableName;

    /**
     *
     * @var integer
     */
    private $pageNumber;

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
     * Number of items to display per page
     */
    private $numberOfItemsPerPage;

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
     * The total number of items in the table
     *
     * @var integer
     */
    private $sourceDataCount;

    /**
     *
     * @var string[]
     */
    private $sourceData;

    /**
     * The function to get the total number of items
     *
     * @var string[]
     */
    private $sourceCountFunction;

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
     * Additional parameters to pass in the URL
     *
     * @var string[]
     */
    private $additionalParameters;

    /**
     * Additional attributes for the th-tags
     *
     * @var string[]
     */
    private $headerAttributes;

    /**
     *
     * @var string[]
     */
    private $contentCellAttributes;

    /**
     * Additional attributes for the td-tags
     *
     * @var string[]
     */
    private $cellAttributes;

    /**
     *
     * @var boolean
     */
    private $allowPageSelection = true;

    /**
     *
     * @var boolean
     */
    private $allowPageNavigation = true;

    /**
     *
     * @param string $tableName
     * @param string[] $sourceCountFunction
     * @param string[] $sourceDataFunction
     * @param integer $defaultOrderColumn
     * @param integer $defaultNumberOfItemsPerPage
     * @param string $defaultOrderDirection
     * @param boolean $allowPageSelection
     * @param boolean $allowPageNavigation
     */
    public function __construct($tableName = 'table', $sourceCountFunction = null, $sourceDataFunction = null, $defaultOrderColumn = 1,
        $defaultNumberOfItemsPerPage = 20, $defaultOrderDirection = SORT_ASC, $allowPageSelection = true, $allowPageNavigation = true)
    {
        parent :: __construct(array('class' => $this->getTableClasses(), 'id' => $tableName), 0, true);

        $this->tableName = $tableName;
        $this->additionalParameters = array();

        $this->pageNumber = $this->determinePageNumber();
        $this->orderColumn = $this->determineOrderColumn($defaultOrderColumn);
        $this->orderDirection = $this->determineOrderDirection($defaultOrderDirection);
        $this->numberOfItemsPerPage = $this->determineNumberOfItemsPerPage($defaultNumberOfItemsPerPage);

        $this->allowPageSelection = $allowPageSelection;
        $this->allowPageNavigation = $allowPageNavigation;

        $this->sourceCountFunction = $sourceCountFunction;
        $this->sourceDataFunction = $sourceDataFunction;

        $this->pager = null;
        $this->sourceDataCount = null;

        $this->tableFormActions = null;
        $this->contentCellAttributes = array();
        $this->headerAttributes = array();
    }

    /**
     *
     * @return string
     */
    abstract public function getTableClasses();

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
     * @param string $parameter
     * @return string
     */
    protected function getParameterName($parameter)
    {
        return $this->getTableName() . '_' . $parameter;
    }

    /**
     *
     * @return integer
     */
    protected function determinePageNumber()
    {
        $variableName = $this->getParameterName('page_nr');
        $requestedPageNumber = Request :: get($variableName);

        return $requestedPageNumber ? $requestedPageNumber : 1;
    }

    /**
     *
     * @param integer $defaultOrderColumn
     * @return integer
     */
    protected function determineOrderColumn($defaultOrderColumn)
    {
        $variableName = $this->getParameterName('column');
        $requestedOrderColumn = Request :: get($variableName);

        return ! is_null($requestedOrderColumn) ? $requestedOrderColumn : $defaultOrderColumn;
    }

    /**
     *
     * @param integer $defaultOrderDirection
     * @return integer
     */
    protected function determineOrderDirection($defaultOrderDirection)
    {
        $variableName = $this->getParameterName('direction');
        $requestedOrderDirection = Request :: get($variableName);

        return $requestedOrderDirection ? $requestedOrderDirection : $defaultOrderDirection;
    }

    /**
     *
     * @param integer $defaultNumberOfItemsPerPage
     * @return integer
     */
    protected function determineNumberOfItemsPerPage($defaultNumberOfItemsPerPage)
    {
        $variableName = $this->getParameterName('per_page');
        $requestedNumberOfItemsPerPage = Request :: get($variableName);

        if ($requestedNumberOfItemsPerPage == Pager :: DISPLAY_ALL)
        {
            return $this->countSourceData();
        }
        else
        {
            return $requestedNumberOfItemsPerPage ? $requestedNumberOfItemsPerPage : $defaultNumberOfItemsPerPage;
        }
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
            $this->pager = new Pager(
                $this->getNumberOfItemsPerPage(),
                $this->getColumnCount(),
                $this->countSourceData(),
                $this->getPageNumber());
        }

        return $this->pager;
    }

    /**
     *
     * @return integer
     */
    abstract public function getColumnCount();

    /**
     *
     * @return string
     */
    abstract public function getFormClasses();

    /**
     *
     * @return string
     * @deprecated Use toHtml() now
     */
    public function as_html()
    {
        return $this->toHtml();
    }

    /**
     *
     * @return string
     */
    public function getEmptyTable()
    {
        $cols = $this->getHeader()->getColCount();

        $this->setCellAttributes(0, 0, 'style="font-style: italic;text-align:center;" colspan=' . $cols);
        $this->setCellContents(0, 0, Translation :: get('NoSearchResults', null, Utilities :: COMMON_LIBRARIES));

        $html = array();

        $html[] = '<div class="table-responsive">';
        $html[] = parent :: toHTML();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderTableHeader()
    {
        $html = array();

        if ($this->getTableFormActions() instanceof TableFormActions && $this->getTableFormActions()->has_form_actions())
        {
            $tableFormActions = $this->getTableFormActions()->get_form_actions();
            $firstFormAction = array_shift($tableFormActions);

            $html[] = '<form class="' . $this->getFormClasses() . '" method="post" action="' .
                 $firstFormAction->get_action() . '" name="form_' . $this->tableName . '">';
        }

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-md-6 table-navigation-actions">';

        if ($this->getTableFormActions() instanceof TableFormActions && $this->getTableFormActions()->has_form_actions())
        {
            $html[] = $this->renderActions();
        }

        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-md-6 table-navigation-search">';
        $html[] = $this->renderTableFilters();
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderTableFilters()
    {
        return $this->renderNumberOfItemsPerPageSelector();
    }

    public function getActionsButtonToolbar()
    {
        $formActions = $this->getTableFormActions()->get_form_actions();
        $firstAction = array_shift($formActions);

        $buttonToolBar = new ButtonToolBar();

        if (count($formActions) > 1)
        {
            $button = new SplitDropdownButton(
                $firstAction->get_title(),
                null,
                $firstAction->get_action(),
                Button :: DISPLAY_LABEL,
                $firstAction->getConfirmation(),
                'btn-sm btn-table-action');
            $button->setDropdownClasses('btn-table-action');

            foreach ($formActions as $formAction)
            {
                $button->addSubButton(
                    new SubButton(
                        $formAction->get_title(),
                        null,
                        $formAction->get_action(),
                        Button :: DISPLAY_LABEL,
                        $formAction->getConfirmation()));
            }

            $buttonToolBar->addItem($button);
        }
        else
        {
            $buttonToolBar->addItem(
                new Button(
                    $firstAction->get_title(),
                    null,
                    $firstAction->get_action(),
                    Button :: DISPLAY_LABEL,
                    $firstAction->getConfirmation(),
                    'btn-sm btn-table-action'));
        }

        return $buttonToolBar;
    }

    public function renderActions()
    {
        $buttonToolBarRenderer = new ButtonToolBarRenderer($this->getActionsButtonToolbar());

        $html = array();

        $html[] = $buttonToolBarRenderer->render();
        $html[] = '<input type="hidden" name="' . $this->tableName . '_namespace" value="' .
             $this->getTableFormActions()->get_namespace() . '"/>';
        $html[] = '<input type="hidden" name="table_name" value="' . $this->tableName . '"/>';

        return implode(PHP_EOL, $html);
    }

    public function renderTableFooter()
    {
        $html = array();

        if ($this->allowPageSelection || $this->allowPageNavigation)
        {
            $html[] = '<div class="row">';
            $html[] = '<div class="col-xs-12 col-md-6 table-navigation-actions">';

            if ($this->getTableFormActions() instanceof TableFormActions &&
                 $this->getTableFormActions()->has_form_actions())
            {
                $html[] = $this->renderActions();
            }

            $html[] = '</div>';

            $queryParameters = array();
            $queryParameters[$this->getParameterName('direction')] = $this->getOrderDirection();
            $queryParameters[$this->getParameterName('per_page')] = $this->getNumberOfItemsPerPage();
            $queryParameters[$this->getParameterName('column')] = $this->getOrderColumn();
            $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

            $html[] = '<div class="col-xs-12 col-md-6 table-navigation-pagination">';
            $html[] = $this->getPagerRenderer()->renderPaginationWithPageLimit(
                $queryParameters,
                $this->getParameterName('page_nr'));
            $html[] = '</div>';

            $html[] = '</div>';
        }

        if ($this->getTableFormActions() instanceof TableFormActions && $this->getTableFormActions()->has_form_actions())
        {
            $html[] = '<input type="submit" name="Submit" value="Submit" style="display:none;" />';
            $html[] = '</form>';
            $html[] = $this->getTableActionsJavascript();
        }

        return implode(PHP_EOL, $html);
    }

    abstract public function getTableActionsJavascript();

    /**
     * Returns the complete table HTML.
     *
     * @return string
     */
    public function toHtml($empty_table = false)
    {
        if ($this->countSourceData() == 0)
        {
            return $this->getEmptyTable();
        }

        $html = array();

        if (! $empty_table)
        {
            $html[] = $this->renderTableHeader();
        }

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        $html[] = '<div class="' . $this->getTableContainerClasses() . '">';
        $html[] = $this->getBodyHtml();
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        if (! $empty_table)
        {
            $html[] = $this->renderTableFooter();
        }

        return implode(PHP_EOL, $html);
    }

    abstract public function getTableContainerClasses();

    /**
     * Get the HTML-code with the navigational buttons to browse through the data-pages.
     *
     * @return string
     */
    public function getNavigationHtml()
    {
        if ($this->allowPageNavigation)
        {
            $pager = $this->getPager();
            $pagerRenderer = new PagerRenderer($pager);

            if ($pager->getNumberOfPages() > 1)
            {
                return $pagerRenderer->renderPaginationWithPageLimit();
            }
        }
    }

    public function getPagerRenderer()
    {
        if (! isset($this->pagerRenderer))
        {
            $this->pagerRenderer = new PagerRenderer($this->getPager());
        }

        return $this->pagerRenderer;
    }

    /**
     * Get the HTML-code with the data-table.
     *
     * @return string
     */
    public function getBodyHtml()
    {
        $pager = $this->getPager();
        $offset = $pager->getCurrentRangeOffset();
        $table_data = $this->getSourceData($offset);

        foreach ($table_data as $index => $row)
        {
            $row_id = $row[0];
            $row = $this->filterData($row);
            $current_row = $this->addRow($row);

            $this->processRowAttributes($row_id, $current_row);
        }

        $this->processContentAttributes();

        foreach ($this->headerAttributes as $column => & $attributes)
        {
            $this->setCellAttributes(0, $column, $attributes);
        }

        foreach ($this->contentCellAttributes as $column => & $attributes)
        {
            $this->setColAttributes($column, $attributes);
        }

        return \HTML_Table :: toHTML();
    }

    /**
     *
     * @param integer $rowIdentifier
     * @param integer $currentRow
     */
    abstract public function processRowAttributes($rowIdentifier, $currentRow);

    abstract public function processContentAttributes();

    /**
     * Get the HTML-code wich represents a form to select how many items a page should contain.
     *
     * @return string
     */
    public function renderNumberOfItemsPerPageSelector()
    {
        if ($this->allowPageSelection)
        {
            $sourceDataCount = $this->countSourceData();

            if ($sourceDataCount <= Pager :: DISPLAY_PER_INCREMENT)
            {
                return '';
            }

            $queryParameters = array();
            $queryParameters[$this->getParameterName('direction')] = $this->getOrderDirection();
            $queryParameters[$this->getParameterName('page_nr')] = $this->getPageNumber();
            $queryParameters[$this->getParameterName('column')] = $this->getOrderColumn();
            $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

            return $this->getPagerRenderer()->renderItemsPerPageSelector(
                $queryParameters,
                $this->getParameterName('per_page'));
        }

        return '';
    }

    /**
     *
     * @param integer $orderColumn
     * @param string $label
     * @param boolean $sortable
     * @param string[] $headerAttributes
     * @param string[] $cellAttributes
     * @return string
     */
    public function setColumnHeader($orderColumn, $label, $sortable = true, $headerAttributes = null, $cellAttributes = null)
    {
        $header = $this->getHeader();

        for ($i = 0; $i < count($headerAttributes); $i ++)
        {
            $header->setColAttributes($i, $headerAttributes[$i]);
        }

        $param['direction'] = SORT_ASC;

        if ($this->getOrderColumn() == $orderColumn && $this->getOrderDirection() == SORT_ASC)
        {
            $param['direction'] = SORT_DESC;
        }

        $param['page_nr'] = $this->getPageNumber();
        $param['per_page'] = $this->getNumberOfItemsPerPage();
        $param['column'] = $orderColumn;

        if ($sortable)
        {
            $link = '<a href="' . $_SERVER['PHP_SELF'] . '?';

            foreach ($param as $key => & $value)
            {
                $link .= $this->getParameterName($key) . '=' . urlencode($value) . '&amp;';
            }

            $link .= http_build_query($this->getAdditionalParameters(), '', Redirect :: ARGUMENT_SEPARATOR);
            $link .= '">' . $label . '</a>';

            if ($this->getOrderColumn() == $orderColumn)
            {
                $link .= $this->getOrderDirection() == SORT_ASC ? ' &#8595;' : ' &#8593;';
            }
        }
        else
        {
            $link = $label;
        }

        $header->setHeaderContents(0, $orderColumn, $link);

        if (! is_null($cellAttributes))
        {
            $this->contentCellAttributes[$orderColumn] = $cellAttributes;
        }

        if (! is_null($headerAttributes))
        {
            $this->headerAttributes[$orderColumn] = $headerAttributes;
        }

        return $link;
    }

    /**
     *
     * @return string
     */
    public function getParameterString()
    {
        $param = array();

        $param[$this->getParameterName('direction')] = $this->getOrderDirection();
        $param[$this->getParameterName('page_nr')] = $this->getPageNumber();
        $param[$this->getParameterName('per_page')] = $this->getNumberOfItemsPerPage();
        $param[$this->getParameterName('column')] = $this->getOrderColumn();

        $param_string_parts = array();

        foreach ($param as $key => & $value)
        {
            $param_string_parts[] = urlencode($key) . '=' . urlencode($value);
        }

        return implode('&amp;', $param_string_parts);
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
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    public function getTableFormActions()
    {
        return $this->tableFormActions;
    }

    /**
     *
     * @param string[]
     */
    public function setAdditionalParameters($parameters)
    {
        $this->additionalParameters = $parameters;
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
     * Get the data to display.
     * This function calls the function given as 2nd argument in the constructor of a
     * SortableTable. Make sure your function has the same parameters as defined here.
     *
     * @param integer
     * @return string[]
     */
    public function getSourceData($offset = null)
    {
        if (! is_null($this->getSourceDataFunction()))
        {
            if (is_null($this->sourceData))
            {
                $this->sourceData = call_user_func(
                    $this->getSourceDataFunction(),
                    $offset,
                    $this->getPager()->getNumberOfItemsPerPage(),
                    $this->getOrderColumn(),
                    $this->getOrderDirection());
            }

            return $this->sourceData;
        }

        return array();
    }

    /**
     *
     * @return boolean
     */
    public function isPageSelectionAllowed()
    {
        return $this->allowPageSelection;
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
     *
     * @return string[]
     */
    public function getSourceDataFunction()
    {
        return $this->sourceDataFunction;
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
     *
     * @return string[]
     */
    public function getContentCellAttributes()
    {
        return $this->contentCellAttributes;
    }

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
     * @param integer $value
     * @return string
     */
    public function getCheckboxHtml($value)
    {
        $html = array();

        $html[] = '<div class="checkbox checkbox-primary">';
        $html[] = '<input class="styled styled-primary" type="checkbox" name="' .
             $this->getTableFormActions()->getIdentifierName() . '[]" value="' . $value . '"';

        if (Request :: get($this->getParameterName('selectall')))
        {
            $html[] = ' checked="checked"';
        }

        $html[] = '/>';
        $html[] = '<label></label>';
        $html[] = '</div>';

        return implode('', $html);
    }

    /**
     * Transform all data in a table-row, using the filters defined by the function set_column_filter(...) defined
     * elsewhere in this class.
     * If you've defined actions, the first element of the given row will be converted into a
     * checkbox
     *
     * @param string[] $row
     * @return string[]
     */
    abstract public function filterData($row);
}