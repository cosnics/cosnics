<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;

/**
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SortableTable extends HTML_Table
{
    const DISPLAY_PER_PAGE_LIMIT = 500;
    const DISPLAY_PER_INCREMENT = 10;
    const DISPLAY_PER_INCREMENT_INTERVAL_LIMIT = 50;
    const DISPLAY_ALL = 'all';

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
        parent :: __construct(
            array(
                'class' => 'table table-striped table-bordered table-hover table-responsive table-data',
                'id' => $tableName),
            0,
            true);

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
    private function getTableName()
    {
        return $this->tableName;
    }

    /**
     *
     * @param string $parameter
     * @return string
     */
    private function getParameterName($parameter)
    {
        return $this->getTableName() . '_' . $parameter;
    }

    /**
     *
     * @return integer
     */
    private function determinePageNumber()
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
    private function determineOrderColumn($defaultOrderColumn)
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
    private function determineOrderDirection($defaultOrderDirection)
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
    private function determineNumberOfItemsPerPage($defaultNumberOfItemsPerPage)
    {
        $variableName = $this->getParameterName('per_page');
        $requestedNumberOfItemsPerPage = Request :: get($variableName);

        if ($requestedNumberOfItemsPerPage == self :: DISPLAY_ALL)
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
            $this->pager = new Pager($this->getNumberOfItemsPerPage(), $this->countSourceData(), $this->getPageNumber());
        }

        return $this->pager;
    }

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

            $html[] = '<form class="table_form form-inline" method="post" action="' . $firstFormAction->get_action() .
                 '" name="form_' . $this->tableName . '">';
            $html[] = ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'SortableTable.js');
        }

        $html[] = '<div class="row">';
        $html[] = '<div class="col-md-4 table-navigation-actions">';

        if ($this->getTableFormActions() instanceof TableFormActions && $this->getTableFormActions()->has_form_actions())
        {
            $html[] = $this->renderActions();
        }

        $html[] = '</div>';
        $html[] = '<div class="col-md-8 table-navigation-search">';

        $html[] = $this->getPageSelectForm();

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderActions()
    {
        $html = array();

        $html[] = '<div class="btn-toolbar">';
        $html[] = '<div class="btn-group">';

        $html[] = '<select class="form-group form-control input-sm" id="actions_' . $this->tableName . '" name="' .
             $this->tableName . '_action_value">';

        foreach ($this->getTableFormActions()->get_form_actions() as $form_action)
        {
            if ($form_action instanceof TableFormAction)
            {
                $message = $form_action->getConfirmationMessage() ? $form_action->getConfirmationMessage() : Translation :: get(
                    'ConfirmYourSelectionAndAction',
                    null,
                    Utilities :: COMMON_LIBRARIES);

                $html[] = '<option value="' . $form_action->get_action() . '"' .
                     ($form_action->get_confirm() ? ' class="confirm" data-message="' . $message . '"' : '') . '>' .
                     $form_action->get_title() . '</option>';
            }
        }

        $html[] = '</select>';
        $html[] = '</div>';

        $submitLabel = Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES);

        $html[] = '<div class="btn-group btn-group-sm">';
        $html[] = '<button class="btn btn-default" type="submit" value="' . $submitLabel . '"/>' . $submitLabel .
             '</button>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<input type="hidden" name="' . $this->tableName . '_namespace" value="' .
             $this->getTableFormActions()->get_namespace() . '"/>';
        $html[] = '<input type="hidden" name="table_name" value="' . $this->tableName . '"/>';

        return implode(PHP_EOL, $html);
    }

    public function renderTableFooter()
    {
        $html = array();

        if ($this->getTableFormActions() instanceof TableFormActions && $this->getTableFormActions()->has_form_actions())
        {
            $html[] = '</form>';
        }

        $form = $this->getPageSelectForm();

        if ($this->allowPageSelection || $this->allowPageNavigation)
        {
            $html[] = '<div class="row">';
            $html[] = '<div class="col-md-4 table-navigation-actions">';

            if ($this->getTableFormActions() instanceof TableFormActions &&
                 $this->getTableFormActions()->has_form_actions())
            {
                $html[] = $this->renderActions();
            }

            $html[] = '</div>';

            $html[] = '<div class="col-md-8 table-navigation-pagination">';
            $html[] = $this->getPagerRenderer()->renderPaginationWithPageLimit();
            $html[] = '</div>';

            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

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

        $html[] = '<div class="container-fluid">';

        if (! $empty_table)
        {
            $html[] = $this->renderTableHeader();
        }

        $html[] = '<div class="row">';
        $html[] = '<div class="col-md-12">';

        $html[] = '<div class="table-responsive">';
        $html[] = $this->getBodyHtml();
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        if (! $empty_table)
        {
            $html[] = $this->renderTableFooter();
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

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

        foreach ($table_data as $index => & $row)
        {
            $row_id = $row[0];
            $row = $this->filterData($row);
            $current_row = $this->addRow($row);
            $this->setRowAttributes($current_row, array('id' => 'row_' . $row_id), true);
        }

        $this->altRowAttributes(0, array('class' => 'row_even'), array('class' => 'row_odd'), true);

        foreach ($this->headerAttributes as $column => & $attributes)
        {
            $this->setCellAttributes(0, $column, $attributes);
        }

        foreach ($this->contentCellAttributes as $column => & $attributes)
        {
            $this->setColAttributes($column, $attributes);
        }

        return parent :: toHTML();
    }

    /**
     * Get the HTML-code wich represents a form to select how many items a page should contain.
     *
     * @return string
     */
    public function getPageSelectForm()
    {
        $result = array();

        if ($this->allowPageSelection)
        {
            $sourceDataCount = $this->countSourceData();

            if ($sourceDataCount <= self :: DISPLAY_PER_INCREMENT)
            {
                return '';
            }

            $result[] = '<div class="pull-right">';
            $result[] = '<form class="form-inline" method="get" action="' . $_SERVER['PHP_SELF'] .
                 '" style="display:inline;">';

            $param = array();
            $param[$this->getParameterName('direction')] = $this->getOrderDirection();
            $param[$this->getParameterName('page_nr')] = $this->getPageNumber();
            $param[$this->getParameterName('column')] = $this->getOrderColumn();
            $param = array_merge($param, $this->getAdditionalParameters());

            foreach ($param as $key => & $value)
            {
                if (! is_null($value))
                {
                    if (is_array($value))
                    {
                        $ser = $this->serializeArray($value, $key);
                        $result = array_merge($result, $ser);
                    }
                    else
                    {
                        $result[] = '<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
                    }
                }
            }

            $result[] = '<select class="form-group form-control input-sm" name="' . $this->getParameterName('per_page') .
                 '" onchange="javascript:this.form.submit();">';

            // calculate the roundup for the interval
            $sourceDataCountUpperInterval = ceil($sourceDataCount / self :: DISPLAY_PER_INCREMENT) *
                 self :: DISPLAY_PER_INCREMENT;

            $minimum = min(self :: DISPLAY_PER_INCREMENT_INTERVAL_LIMIT, $sourceDataCountUpperInterval);

            for ($nr = self :: DISPLAY_PER_INCREMENT; $nr <= $minimum; $nr += self :: DISPLAY_PER_INCREMENT)
            {
                $result[] = '<option value="' . $nr . '" ' .
                     ($nr == $this->getNumberOfItemsPerPage() ? 'selected="selected"' : '') . '>' . $nr . '</option>';
            }

            if ($sourceDataCount < self :: DISPLAY_PER_PAGE_LIMIT)
            {
                $all_text = Translation :: get('AllEntries', Utilities :: COMMON_LIBRARIES);
                $result[] = '<option value="' . self :: DISPLAY_ALL . '" ' .
                     ($sourceDataCount == $this->getNumberOfItemsPerPage() ? 'selected="selected"' : '') . '>' .
                     $all_text . '</option>';
            }

            $result[] = '</select> ';
            $result[] = Translation :: get('SelectEntriesPerPage');
            $result[] = '<noscript>';
            $result[] = '<button class="btn btn-default btn-sm" type="submit" value="' .
                 Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES) . '">' .
                 Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES) . '</button>';
            $result[] = '</noscript>';
            $result[] = '</form>';
            $result[] = '</div>';
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * Get the table title.
     *
     * @return string
     */
    public function getTableTitle()
    {
        if ($this->allowPageNavigation)
        {
            return $this->getPagerRenderer()->renderCurrentRange();
        }
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

        if ($actions instanceof TableFormActions && $actions->has_form_actions())
        {
            $columnHeaderHtml = '<input type="checkbox" name="sortableTableSelectToggle" class="sortableTableSelectToggle" />';
        }
        else
        {
            $columnHeaderHtml = '';
        }

        $this->setColumnHeader(0, $columnHeaderHtml, false);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    private function getTableFormActions()
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
     * Transform all data in a table-row, using the filters defined by the function set_column_filter(...) defined
     * elsewhere in this class.
     * If you've defined actions, the first element of the given row will be converted into a
     * checkbox
     *
     * @param string[]
     * @return string[]
     */
    public function filterData($row)
    {
        $url_params = $this->getParameterString() . '&amp;' .
             http_build_query($this->getAdditionalParameters(), '', Redirect :: ARGUMENT_SEPARATOR);

        if ($this->getTableFormActions() instanceof TableFormActions && $this->getTableFormActions()->has_form_actions())
        {
            if (strlen($row[0]) > 0)
            {
                $row[0] = '<input type="checkbox" name="' . $this->getTableFormActions()->getIdentifierName() .
                     '[]" value="' . $row[0] . '"';

                if (Request :: get($this->getParameterName('selectall')))
                {
                    $row[0] .= ' checked="checked"';
                }

                $row[0] .= '/>';
            }
        }

        foreach ($row as $index => & $value)
        {
            if (! is_numeric($value) && empty($value))
            {
                $value = '-';
            }
        }

        return $row;
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
                    $this->getNumberOfItemsPerPage(),
                    $this->getOrderColumn(),
                    $this->getOrderDirection());
            }

            return $this->sourceData;
        }

        return array();
    }

    /**
     * Serializes a URL parameter passed as an array into a query string or hidden inputs.
     *
     * @param string[] The parameter's value.
     * @param string $key The parameter's name.
     * @param boolean $as_query_string True to format the result as a query string, false for hidden inputs.
     * @return string[] The query string parts (to be joined by ampersands or another separator), or the hidden inputs
     *         as HTML, each array element containing a single input.
     */
    private function serializeArray($params, $key, $as_query_string = false)
    {
        $out = array();

        foreach ($params as $k => & $v)
        {
            if (is_array($v))
            {
                $ser = $this->serializeArray($v, $key . '[' . $k . ']', $as_query_string);
                $out = array_merge($out, $ser);
            }
            else
            {
                $v = urlencode($v);
            }

            if ($as_query_string)
            {
                $k = urlencode($key . '[' . $k . ']');
                $out[] = $k . '=' . $v;
            }
            else
            {
                $k = $key . '[' . $k . ']';
                $out[] = '<input type="hidden" name="' . $k . '" value="' . $v . '"/>';
            }
        }

        return $out;
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
}