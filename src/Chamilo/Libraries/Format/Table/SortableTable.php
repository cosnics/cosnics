<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;
use Pager;
use Chamilo\Libraries\File\Redirect;

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
     * @var integer
     */
    private $pager;

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
        parent :: __construct(array('class' => 'data_table', 'id' => $tableName), 0, true);

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
     * @return \Pager_Common
     */
    public function getPager()
    {
        if (is_null($this->pager))
        {
            $params['mode'] = 'Sliding';
            $params['perPage'] = $this->getNumberOfItemsPerPage();
            $params['totalItems'] = $this->countSourceData();
            $params['urlVar'] = $this->getParameterName('page_nr');
            $params['prevImg'] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Prev') .
                 '"  style="vertical-align: middle;"/>';
            $params['nextImg'] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Next') .
                 '"  style="vertical-align: middle;"/>';
            $params['firstPageText'] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/First') .
                 '"  style="vertical-align: middle;"/>';
            $params['lastPageText'] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Last') .
                 '"  style="vertical-align: middle;"/>';
            $params['firstPagePre'] = '';
            $params['lastPagePre'] = '';
            $params['firstPagePost'] = '';
            $params['lastPagePost'] = '';
            $params['spacesBeforeSeparator'] = '';
            $params['spacesAfterSeparator'] = '';
            $params['currentPage'] = $this->getPageNumber();

            $query_vars = array_keys($_GET);
            $query_vars_needed = array(
                $this->getParameterName('column'),
                $this->getParameterName('direction'),
                $this->getParameterName('per_page'));

            if (count($this->getAdditionalParameters()) > 0)
            {
                $query_vars_needed = array_merge($query_vars_needed, array_keys($this->getAdditionalParameters()));
            }

            $query_vars_exclude = array_diff($query_vars, $query_vars_needed);
            $params['excludeVars'] = $query_vars_exclude;

            $extra_variables = array();

            foreach ($this->getAdditionalParameters() as $key => $value)
            {
                if (! is_null($value))
                {
                    $extra_variables[$key] = $value;
                }
            }

            $params['extraVars'] = $extra_variables;
            $this->pager = Pager :: factory($params);
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

        return parent :: toHTML();
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

        if (! $empty_table)
        {
            if ($this->allowPageSelection || $this->allowPageNavigation)
            {
                $form = $this->getPageSelectForm();
                $nav = $this->getNavigationHtml();

                $html[] = '<table style="width:100%;">';
                $html[] = '<tr>';
                $html[] = '<td style="width:25%;">';
                $html[] = $form;
                $html[] = '</td>';
                $html[] = '<td style="text-align:center;">';
                $html[] = $this->getTableTitle();
                $html[] = '</td>';
                $html[] = '<td style="text-align:right;width:25%;">';
                $html[] = $nav;
                $html[] = '</td>';
                $html[] = '</tr>';
                $html[] = '</table>';
            }

            if ($this->getTableFormActions() instanceof TableFormActions &&
                 $this->getTableFormActions()->has_form_actions())
            {
                $tableFormActions = $this->getTableFormActions()->get_form_actions();
                $firstFormAction = array_shift($tableFormActions);

                $html[] = '<form method="post" action="' . $firstFormAction->get_action() . '" name="form_' .
                     $this->tableName . '" class="table_form">';
                $html[] = ResourceManager :: get_instance()->get_resource_html(
                    Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'SortableTable.js');
            }
        }

        $html[] = $this->getBodyHtml();

        if (! $empty_table)
        {
            if ($this->allowPageSelection || $this->allowPageNavigation)
            {
                $html[] = '<table style="width:100%;">';
                $html[] = '<tr>';
                $html[] = '<td colspan="2">';
            }

            if ($this->getTableFormActions() instanceof TableFormActions &&
                 $this->getTableFormActions()->has_form_actions())
            {
                $html[] = '<div class="sortable_table_selection_controls">';
                $html[] = '<span class="sortable_table_selection_controls_options">';
                $html[] = '<a href="#" class="sortable_table_select_all">' .
                     Translation :: get('SelectAll', null, Utilities :: COMMON_LIBRARIES) . '</a>';
                $html[] = '&nbsp;-&nbsp;';
                $html[] = '<a href="#" class="sortable_table_select_none">' .
                     Translation :: get('UnselectAll', null, Utilities :: COMMON_LIBRARIES) . '</a> ';
                $html[] = '</span>';
                $html[] = '<select id="actions_' . $this->tableName . '" name="' . $this->tableName . '_action_value">';

                foreach ($this->getTableFormActions()->get_form_actions() as $form_action)
                {
                    if ($form_action instanceof TableFormAction)
                    {
                        $message = $form_action->getConfirmationMessage() ? $form_action->getConfirmationMessage() : Translation :: get(
                            'ConfirmYourSelectionAndAction',
                            null,
                            Utilities :: COMMON_LIBRARIES);

                        $html[] = '<option value="' . $form_action->get_action() . '"' .
                             ($form_action->get_confirm() ? ' class="confirm" data-message="' . $message . '"' : '') .
                             '>' . $form_action->get_title() . '</option>';
                    }
                }

                $html[] = '</select>';
                $html[] = '<input type="hidden" name="' . $this->tableName . '_namespace" value="' .
                     $this->getTableFormActions()->get_namespace() . '"/>';
                $html[] = '<input type="hidden" name="table_name" value="' . $this->tableName . '"/>';
                $html[] = ' <input type="submit" value="' . Translation :: get(
                    'Ok',
                    null,
                    Utilities :: COMMON_LIBRARIES) . '"/>';
            }
            elseif ($this->allowPageSelection || $this->allowPageNavigation)
            {
                $html[] = $form;
            }

            if ($this->allowPageSelection || $this->allowPageNavigation)
            {
                $html[] = '</td>';
                $html[] = '<td style="text-align:right;">';
                $html[] = $nav;
                $html[] = '</td>';
                $html[] = '</tr>';
                $html[] = '</table>';
            }

            if ($this->getTableFormActions() instanceof TableFormActions &&
                 $this->getTableFormActions()->has_form_actions())
            {
                $html[] = '</form>';
            }
        }

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
            $pager_links = $pager->getLinks();
            $showed_items = $pager->getOffsetByPageId();

            return $pager_links['first'] . ' ' . $pager_links['back'] . ' ' . $pager->getCurrentPageId() . ' / ' .
                 $pager->numPages() . ' ' . $pager_links['next'] . ' ' . $pager_links['last'];
        }
    }

    /**
     * Get the HTML-code with the data-table.
     *
     * @return string
     */
    public function getBodyHtml()
    {
        $pager = $this->getPager();
        $offset = $pager->getOffsetByPageId();
        $from = $offset[0] - 1;
        $table_data = $this->getSourceData($from);

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

            $result[] = '<form method="get" action="' . $_SERVER['PHP_SELF'] . '" style="display:inline;">';

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

            $result[] = '<select name="' . $this->getParameterName('per_page') .
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
                $all_text = Translation :: get('All', Utilities :: COMMON_LIBRARIES);
                $result[] = '<option value="' . self :: DISPLAY_ALL . '" ' .
                     ($sourceDataCount == $this->getNumberOfItemsPerPage() ? 'selected="selected"' : '') . '>' .
                     $all_text . '</option>';
            }

            $result[] = '</select>';
            $result[] = '<noscript>';
            $result[] = '<button class="normal" type="submit" value="' .
                 Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES) . '">' .
                 Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES) . '</button>';
            $result[] = '</noscript>';
            $result[] = '</form>';
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
            $showed_items = $this->getPager()->getOffsetByPageId();
            return $showed_items[0] . ' - ' . $showed_items[1] . ' / ' . $this->countSourceData();
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