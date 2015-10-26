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
 * This class allows you to display a sortable data-table.
 * It is possible to split the data in several pages. Using this
 * class you can: - automatically create checkboxes of the first table column - a "select all" and "deselect all" link
 * is added - only if you provide a list of actions for the selected items - click on the table header to sort the data
 * - choose how many items you see per page - navigate through all data-pages
 */
class SortableTable extends HTML_Table
{
    const DISPLAY_PER_PAGE_LIMIT = 500;
    const DISPLAY_PER_INCREMENT = 10;
    const DISPLAY_PER_INCREMENT_INTERVAL_LIMIT = 50;
    const DISPLAY_ALL = 'all';

    /**
     * A name for this table
     */
    private $tableName;

    /**
     * The page to display
     */
    private $pageNumber;

    /**
     * The column to sort the data
     */
    private $column;

    /**
     * The sorting direction (SORT_ASC or SORT_DESC)
     */
    private $direction;

    /**
     * Number of items to display per page
     */
    private $numberOfItemsPerPage;

    /**
     * The default number of items to display per page
     */
    private $defaultPerPage;

    /**
     * A prefix for the URL-parameters, can be used on pages with multiple SortableTables
     */
    private $parameterPrefix;

    /**
     * The pager object to split the data in several pages
     */
    private $pager;

    /**
     * The total number of items in the table
     */
    private $itemCount;

    /**
     * The function to get the total number of items
     */
    private $itemCountFunction;

    /**
     * The function to the the data to display
     */
    private $itemDataFunction;

    /**
     * An array with defined column-filters
     */
    private $columnFilters;

    /**
     * A list of actions which will be available through a select list
     */
    private $formActions;

    /**
     * Additional parameters to pass in the URL
     */
    private $additionalParameters;

    /**
     * Additional attributes for the th-tags
     */
    private $headerAttributes;

    /**
     * Additional attributes for the td-tags
     */
    private $cellAttributes;

    private $allowPageSelection = true;

    private $allowPageNavigation = true;

    /**
     * Create a new SortableTable
     *
     * @param $tableName string A name for the table (default = 'table')
     * @param $itemCountFunction string A user defined function to get the total number of items in the table
     * @param $itemDataFunction string A function to get the data to display on the current page
     * @param $defaultOrderColumn int The default column on which the data should be sorted
     * @param $defaultPerPage int The default number of items to show on one page
     * @param $defaultOrderDirection int The default order direction; either the constant SORT_ASC or SORT_DESC
     */
    public function __construct($tableName = 'table', $itemCountFunction = null, $itemDataFunction = null, $defaultOrderColumn = 1,
        $defaultNumberOfItemsPerPage = 20, $defaultOrderDirection = SORT_ASC, $allowPageSelection = true, $allowPageNavigation = true)
    {
        parent :: __construct(array('class' => 'data_table', 'id' => $tableName), 0, true);

        $this->tableName = $tableName;
        $this->additionalParameters = array();
        $this->parameterPrefix = $tableName . '_';

        $this->pageNumber = $this->getPageNumber();
        $this->column = $this->getOrderColumn($defaultOrderColumn);
        $this->direction = $this->getOrderDirection($defaultOrderDirection);
        $this->numberOfItemsPerPage = $this->getNumberOfItemsPerPage($defaultNumberOfItemsPerPage);

        $this->allowPageSelection = $allowPageSelection;
        $this->allowPageNavigation = $allowPageNavigation;

        $this->itemCountFunction = $itemCountFunction;
        $this->itemDataFunction = $itemDataFunction;

        $this->pager = null;
        $this->itemCount = null;

        $this->columnFilters = array();
        $this->formActions = null;
        $this->cellAttributes = array();
        $this->headerAttributes = array();
    }

    private function getTableName()
    {
        return $this->tableName;
    }

    private function getParameterName($parameter)
    {
        return $this->getTableName() . '_' . $parameter;
    }

    private function getPageNumber()
    {
        $variableName = $this->getParameterName('page_nr');
        $requestedPageNumber = Request :: get($variableName);

        return $requestedPageNumber ? $requestedPageNumber : 1;
    }

    private function getOrderColumn($defaultOrderColumn)
    {
        $variableName = $this->getParameterName('column');
        $requestedOrderColumn = Request :: get($variableName);

        return ! is_null($requestedOrderColumn) ? $requestedOrderColumn : $defaultOrderColumn;
    }

    private function getOrderDirection($defaultOrderDirection)
    {
        $variableName = $this->getParameterName('direction');
        $requestedOrderDirection = Request :: get($variableName);

        return $requestedOrderDirection ? $requestedOrderDirection : $defaultOrderDirection;
    }

    private function getNumberOfItemsPerPage($defaultNumberOfItemsPerPage)
    {
        $variableName = $this->getParameterName('per_page');
        $requestedNumberOfItemsPerPage = Request :: get($variableName);

        if ($requestedNumberOfItemsPerPage == self :: DISPLAY_ALL)
        {
            return $this->countItems();
        }
        else
        {
            return $requestedNumberOfItemsPerPage ? $requestedNumberOfItemsPerPage : $defaultNumberOfItemsPerPage;
        }
    }

    /**
     * Get the Pager object to split the showed data in several pages
     */
    public function getPager()
    {
        if (is_null($this->pager))
        {
            $itemCount = $this->countItems();
            $params['mode'] = 'Sliding';
            $params['perPage'] = $this->numberOfItemsPerPage;
            $params['totalItems'] = $itemCount;
            $params['urlVar'] = $this->parameterPrefix . 'page_nr';
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
            $params['currentPage'] = $this->pageNumber;
            $query_vars = array_keys($_GET);
            $query_vars_needed = array(
                $this->parameterPrefix . 'column',
                $this->parameterPrefix . 'direction',
                $this->parameterPrefix . 'per_page');
            if (count($this->additionalParameters) > 0)
            {
                $query_vars_needed = array_merge($query_vars_needed, array_keys($this->additionalParameters));
            }
            $query_vars_exclude = array_diff($query_vars, $query_vars_needed);
            $params['excludeVars'] = $query_vars_exclude;

            $extra_variables = array();

            foreach ($this->additionalParameters as $key => $value)
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

    public function getEmptyTable()
    {
        $cols = $this->getHeader()->getColCount();

        $this->setCellAttributes(0, 0, 'style="font-style: italic;text-align:center;" colspan=' . $cols);
        $this->setCellContents(0, 0, Translation :: get('NoSearchResults', null, Utilities :: COMMON_LIBRARIES));

        return parent :: toHTML();
    }

    /**
     * Returns the complete table HTML.
     */
    public function toHtml($empty_table = false)
    {
        if ($this->countItems() == 0)
        {
            return $this->getEmptyTable();
        }

        if (! $empty_table)
        {
            if ($this->allowPageSelection || $this->allowPageNavigation)
            {
                $form = $this->get_page_select_form();
                $nav = $this->get_navigation_html();

                $html[] = '<table style="width:100%;">';
                $html[] = '<tr>';
                $html[] = '<td style="width:25%;">';
                $html[] = $form;
                $html[] = '</td>';
                $html[] = '<td style="text-align:center;">';
                $html[] = $this->get_table_title();
                $html[] = '</td>';
                $html[] = '<td style="text-align:right;width:25%;">';
                $html[] = $nav;
                $html[] = '</td>';
                $html[] = '</tr>';
                $html[] = '</table>';
            }

            if ($this->formActions instanceof TableFormActions && $this->formActions->has_form_actions())
            {
                $formActions = $this->formActions->get_form_actions();
                $firstFormAction = array_shift($formActions);

                $html[] = '<form method="post" action="' . $firstFormAction->get_action() . '" name="form_' .
                     $this->tableName . '" class="table_form">';
                $html[] = ResourceManager :: get_instance()->get_resource_html(
                    Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'SortableTable.js');
            }
        }

        $html[] = $this->get_table_html();

        if (! $empty_table)
        {
            if ($this->allowPageSelection || $this->allowPageNavigation)
            {
                $html[] = '<table style="width:100%;">';
                $html[] = '<tr>';
                $html[] = '<td colspan="2">';
            }

            if ($this->formActions instanceof TableFormActions && $this->formActions->has_form_actions())
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

                foreach ($this->formActions->get_form_actions() as $form_action)
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
                     $this->formActions->get_namespace() . '"/>';
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

            if ($this->formActions instanceof TableFormActions && $this->formActions->has_form_actions())
            {
                $html[] = '</form>';
            }
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Get the HTML-code with the navigational buttons to browse through the data-pages.
     */
    public function get_navigation_html()
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
     */
    public function get_table_html()
    {
        // Make sure the header isn't dragable or droppable
        // $this->setRowAttributes(0, array('class' => 'nodrag nodrop'), true);

        // Now process the rest of the table
        $pager = $this->getPager();
        $offset = $pager->getOffsetByPageId();
        $from = $offset[0] - 1;
        $table_data = $this->getItemData($from);

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

        foreach ($this->cellAttributes as $column => & $attributes)
        {
            $this->setColAttributes($column, $attributes);
        }

        return parent :: toHTML();
    }

    /**
     * Get the HTML-code wich represents a form to select how many items a page should contain.
     */
    public function get_page_select_form()
    {
        $result = array();
        if ($this->allowPageSelection)
        {
            $itemCount = $this->countItems();
            if ($itemCount <= self :: DISPLAY_PER_INCREMENT)
            {
                return '';
            }
            $result[] = '<form method="get" action="' . $_SERVER['PHP_SELF'] . '" style="display:inline;">';
            $param[$this->parameterPrefix . 'direction'] = $this->direction;
            $param[$this->parameterPrefix . 'page_nr'] = $this->pageNumber;
            $param[$this->parameterPrefix . 'column'] = $this->column;
            $param = array_merge($param, $this->additionalParameters);

            foreach ($param as $key => & $value)
            {
                if (! is_null($value))
                {
                    if (is_array($value))
                    {
                        $ser = self :: serialize_array($value, $key);
                        $result = array_merge($result, $ser);
                    }
                    else
                    {
                        $result[] = '<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
                    }
                }
            }
            $result[] = '<select name="' . $this->parameterPrefix .
                 'per_page" onchange="javascript:this.form.submit();">';

            // calculate the roundup for the interval
            $itemCount_upper_interval = ceil($itemCount / self :: DISPLAY_PER_INCREMENT) * self :: DISPLAY_PER_INCREMENT;

            $minimum = min(self :: DISPLAY_PER_INCREMENT_INTERVAL_LIMIT, $itemCount_upper_interval);

            for ($nr = self :: DISPLAY_PER_INCREMENT; $nr <= $minimum; $nr += self :: DISPLAY_PER_INCREMENT)
            {
                $result[] = '<option value="' . $nr . '" ' .
                     ($nr == $this->numberOfItemsPerPage ? 'selected="selected"' : '') . '>' . $nr . '</option>';
            }
            if ($itemCount < self :: DISPLAY_PER_PAGE_LIMIT)
            {
                $all_text = Translation :: get('All', Utilities :: COMMON_LIBRARIES);
                $result[] = '<option value="' . self :: DISPLAY_ALL . '" ' .
                     ($itemCount == $this->numberOfItemsPerPage ? 'selected="selected"' : '') . '>' . $all_text .
                     '</option>';
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
     */
    public function get_table_title()
    {
        if ($this->allowPageNavigation)
        {
            $showed_items = $this->getPager()->getOffsetByPageId();
            return $showed_items[0] . ' - ' . $showed_items[1] . ' / ' . $this->countItems();
        }
    }

    /**
     * Set the header-label
     *
     * @param $column int The column number
     * @param $label string The label
     * @param $sortable boolean Is the table sortable by this column? (defatult = true)
     * @param $headerAttributes string Additional attributes for the th-tag of the table header
     * @param $cellAttributes string Additional attributes for the td-tags of the column
     */
    public function set_header($column, $label, $sortable = true, $headerAttributes = null, $cellAttributes = null)
    {
        $header = $this->getHeader();
        for ($i = 0; $i < count($headerAttributes); $i ++)
        {
            $header->setColAttributes($i, $headerAttributes[$i]);
        }

        $param['direction'] = SORT_ASC;
        if ($this->column == $column && $this->direction == SORT_ASC)
        {
            $param['direction'] = SORT_DESC;
        }
        $param['page_nr'] = $this->pageNumber;
        $param['per_page'] = $this->numberOfItemsPerPage;
        $param['column'] = $column;
        if ($sortable)
        {
            $link = '<a href="' . $_SERVER['PHP_SELF'] . '?';
            foreach ($param as $key => & $value)
            {
                $link .= $this->parameterPrefix . $key . '=' . urlencode($value) . '&amp;';
            }
            $link .= http_build_query($this->additionalParameters, '', Redirect :: ARGUMENT_SEPARATOR);
            $link .= '">' . $label . '</a>';
            if ($this->column == $column)
            {
                $link .= $this->direction == SORT_ASC ? ' &#8595;' : ' &#8593;';
            }
        }
        else
        {
            $link = $label;
        }

        $header->setHeaderContents(0, $column, $link);
        if (! is_null($cellAttributes))
        {
            $this->cellAttributes[$column] = $cellAttributes;
        }
        if (! is_null($headerAttributes))
        {
            $this->headerAttributes[$column] = $headerAttributes;
        }

        return $link;
    }

    /**
     * Get the parameter-string with the SortableTable-related parameters to use in URLs
     */
    public function get_sortable_table_param_string()
    {
        $param[$this->parameterPrefix . 'direction'] = $this->direction;
        $param[$this->parameterPrefix . 'page_nr'] = $this->pageNumber;
        $param[$this->parameterPrefix . 'per_page'] = $this->numberOfItemsPerPage;
        $param[$this->parameterPrefix . 'column'] = $this->column;

        $param_string_parts = array();

        foreach ($param as $key => & $value)
        {
            $param_string_parts[] = urlencode($key) . '=' . urlencode($value);
        }

        return implode('&amp;', $param_string_parts);
    }

    /**
     * Add a filter to a column.
     * If another filter was allready defined for the given column, it will be overwritten.
     *
     * @param $column int The number of the column
     * @param $function string The name of the filter-function. This should be a function wich requires 1 parameter and
     *        returns the filtered value.
     */
    public function set_column_filter($column, $function)
    {
        $this->columnFilters[$column] = $function;
    }

    /**
     * Define a list of actions which can be performed on the table-date.
     * If you define a list of actions, the first
     * column of the table will be converted into checkboxes.
     *
     * @param $actions array A list of actions. The key is the name of the action. The value is the label to show in the
     *        select-box
     */
    public function set_form_actions($actions)
    {
        $this->formActions = $actions;
    }

    /**
     * Define a list of additional parameters to use in the generated URLs
     *
     * @param $parameters array
     */
    public function setAdditionalParameters($parameters)
    {
        $this->additionalParameters = $parameters;
    }

    /**
     * Transform all data in a table-row, using the filters defined by the function set_column_filter(...) defined
     * elsewhere in this class.
     * If you've defined actions, the first element of the given row will be converted into a
     * checkbox
     *
     * @param $row array A row from the table.
     */
    public function filterData($row)
    {
        $url_params = $this->get_sortable_table_param_string() . '&amp;' .
             http_build_query($this->additionalParameters, '', Redirect :: ARGUMENT_SEPARATOR);

        foreach ($this->columnFilters as $column => $function)
        {
            $row[$column] = call_user_func($function, $row[$column], $url_params);
        }

        if ($this->formActions instanceof TableFormActions && $this->formActions->has_form_actions())
        {
            if (strlen($row[0]) > 0)
            {
                $row[0] = '<input type="checkbox" name="' . $this->formActions->getIdentifierName() . '[]" value="' .
                     $row[0] . '"';

                if (Request :: get($this->parameterPrefix . 'selectall'))
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
     * Get the total number of items.
     * This function calls the function given as 2nd argument in the constructor of a
     * SortableTable. Make sure your function has the same parameters as defined here.
     */
    public function countItems()
    {
        if (is_null($this->itemCount))
        {
            $this->itemCount = call_user_func($this->itemCountFunction);
        }

        return $this->itemCount;
    }

    /**
     * Get the data to display.
     * This function calls the function given as 2nd argument in the constructor of a
     * SortableTable. Make sure your function has the same parameters as defined here.
     *
     * @param $from int Index of the first item to return.
     * @param $numberOfItemsPerPage int The number of items to return
     * @param $column int The number of the column on which the data should be sorted
     * @param $direction string In which order should the data be sorted (ASC or DESC)
     */
    public function getItemData($from = null, $numberOfItemsPerPage = null, $column = null, $direction = null)
    {
        if (! is_null($this->itemDataFunction))
        {
            return call_user_func(
                $this->itemDataFunction,
                $from,
                $this->numberOfItemsPerPage,
                $this->column,
                $this->direction);
        }
        return array();
    }

    /**
     * Serializes a URL parameter passed as an array into a query string or hidden inputs.
     *
     * @param $params array The parameter's value.
     * @param $key string The parameter's name.
     * @param $as_query_string boolean True to format the result as a query string, false for hidden inputs.
     * @return array The query string parts (to be joined by ampersands or another separator), or the hidden inputs as
     *         HTML, each array element containing a single input.
     */
    private function serialize_array($params, $key, $as_query_string = false)
    {
        $out = array();
        foreach ($params as $k => & $v)
        {
            if (is_array($v))
            {
                $ser = self :: serialize_array($v, $key . '[' . $k . ']', $as_query_string);
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

            // $k = urlencode($key . '[' . $k . ']');
            // $out[] = ($as_query_string ? $k . '=' . $v : '<input type="hidden" name="' . $k . '" value="' . $v .
            // '"/>');
        }
        return $out;
    }

    /**
     * Gets the AJAX status of the table
     *
     * @return boolean Whether or not the table should have AJAX functionality
     */
    public function is_ajax_enabled()
    {
        return $this->ajaxEnabled;
    }

    /**
     * Sets the table's AJAX status to true
     */
    public function enable_ajax()
    {
        $this->ajaxEnabled = true;
    }

    /**
     * Sets the table's AJAX status to false
     */
    public function disable_ajax()
    {
        $this->ajaxEnabled = false;
    }

    /**
     *
     * @return boolean
     */
    public function isPageSelectionAllowed()
    {
        return $this->allowPageSelection;
    }

    public function get_per_page()
    {
        return $this->numberOfItemsPerPage;
    }

    public function get_column()
    {
        return $this->column;
    }

    public function get_direction()
    {
        return $this->direction;
    }

    public function getCellAttributes()
    {
        return $this->cellAttributes;
    }

    public function getHeaderAttributes()
    {
        return $this->headerAttributes;
    }
}