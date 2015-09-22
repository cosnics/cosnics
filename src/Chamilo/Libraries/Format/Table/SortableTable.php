<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;
use Pager;

/**
 * This class allows you to display a sortable data-table. It is possible to split the data in several pages. Using this
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
    private $table_name;

    /**
     * The page to display
     */
    private $page_nr;

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
    private $per_page;

    /**
     * The default number of items to display per page
     */
    private $default_items_per_page;

    /**
     * A prefix for the URL-parameters, can be used on pages with multiple SortableTables
     */
    private $param_prefix;

    /**
     * The pager object to split the data in several pages
     */
    private $pager;

    /**
     * The total number of items in the table
     */
    private $total_number_of_items;

    /**
     * The function to get the total number of items
     */
    private $get_total_number_function;

    /**
     * The function to the the data to display
     */
    private $get_data_function;

    /**
     * An array with defined column-filters
     */
    private $column_filters;

    /**
     * A list of actions which will be available through a select list
     */
    private $form_actions;

    /**
     * Additional parameters to pass in the URL
     */
    private $additional_parameters;

    /**
     * Additional attributes for the th-tags
     */
    private $th_attributes;

    /**
     * Additional attributes for the td-tags
     */
    private $td_attributes;

    /**
     * Additional attributes for the tr-tags
     */
    private $tr_attributes;

    /**
     * Array with names of the other tables defined on the same page of this table
     */
    private $other_tables;

    private $allow_page_selection = true;

    /**
     * Create a new SortableTable
     *
     * @param $table_name string A name for the table (default = 'table')
     * @param $get_total_number_function string A user defined function to get the total number of items in the table
     * @param $get_data_function string A function to get the data to display on the current page
     * @param $default_column int The default column on which the data should be sorted
     * @param $default_items_per_page int The default number of items to show on one page
     * @param $default_order_direction int The default order direction; either the constant SORT_ASC or SORT_DESC
     */
    public function __construct($table_name = 'table', $get_total_number_function = null, $get_data_function = null,
        $default_column = 1, $default_items_per_page = 20, $default_order_direction = SORT_ASC, $ajax_enabled = false,
        $allow_page_selection = true)
    {
        parent :: __construct(array('class' => 'data_table', 'id' => $table_name), 0, true);
        $this->table_name = $table_name;
        $this->additional_parameters = array();
        $this->param_prefix = $table_name . '_';
        // $this->page_nr = isset($_SESSION[$this->param_prefix . 'page_nr']) ? $_SESSION[$this->param_prefix .
        // 'page_nr'] : 1;
        $this->page_nr = Request :: get($this->param_prefix . 'page_nr') ? Request :: get(
            $this->param_prefix . 'page_nr') : 1;
        // $this->column = isset($_SESSION[$this->param_prefix . 'column']) ? $_SESSION[$this->param_prefix . 'column']
        // : $default_column;
        $this->column = ! is_null(Request :: get($this->param_prefix . 'column')) ? Request :: get(
            $this->param_prefix . 'column') : $default_column;
        // $this->direction = isset($_SESSION[$this->param_prefix . 'direction']) ? $_SESSION[$this->param_prefix .
        // 'direction'] : $default_order_direction;
        $this->direction = Request :: get($this->param_prefix . 'direction') ? Request :: get(
            $this->param_prefix . 'direction') : $default_order_direction;
        // $this->per_page = isset($_SESSION[$this->param_prefix . 'per_page']) ? $_SESSION[$this->param_prefix .
        // 'per_page'] : $default_items_per_page;
        $this->per_page = Request :: get($this->param_prefix . 'per_page') ? Request :: get(
            $this->param_prefix . 'per_page') : $default_items_per_page;
        $this->allow_page_selection = $allow_page_selection;
//         $_SESSION[$this->param_prefix . 'per_page'] = $this->per_page;
//         $_SESSION[$this->param_prefix . 'direction'] = $this->direction;
//         $_SESSION[$this->param_prefix . 'page_nr'] = $this->page_nr;
//         $_SESSION[$this->param_prefix . 'column'] = $this->column;

        $this->pager = null;
        $this->default_items_per_page = $default_items_per_page;
        $this->total_number_of_items = - 1;
        $this->get_total_number_function = $get_total_number_function;
        $this->total_number_of_items = $this->get_total_number_of_items();
        $this->get_data_function = $get_data_function;

        if ($this->per_page == self :: DISPLAY_ALL)
        {
            $this->per_page = $this->total_number_of_items;
        }

        $this->ajax_enabled = $ajax_enabled;
        $this->column_filters = array();
        $this->form_actions = new TableFormActions(__NAMESPACE__);
        $this->checkbox_name = null;
        $this->td_attributes = array();
        $this->th_attributes = array();
        $this->other_tables = array();
    }

    /**
     * Get the Pager object to split the showed data in several pages
     */
    public function get_pager()
    {
        if (is_null($this->pager))
        {
            $total_number_of_items = $this->total_number_of_items;
            $params['mode'] = 'Sliding';
            $params['perPage'] = $this->per_page;
            $params['totalItems'] = $total_number_of_items;
            $params['urlVar'] = $this->param_prefix . 'page_nr';
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
            $params['currentPage'] = $this->page_nr;
            $query_vars = array_keys($_GET);
            $query_vars_needed = array(
                $this->param_prefix . 'column',
                $this->param_prefix . 'direction',
                $this->param_prefix . 'per_page');
            if (count($this->additional_parameters) > 0)
            {
                $query_vars_needed = array_merge($query_vars_needed, array_keys($this->additional_parameters));
            }
            $query_vars_exclude = array_diff($query_vars, $query_vars_needed);
            $params['excludeVars'] = $query_vars_exclude;

            $extra_variables = array();

            foreach ($this->additional_parameters as $key => $value)
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
     * Returns the complete table HTML. Alias of as_html().
     */
    public function toHTML()
    {
        return $this->as_html();
    }

    public function toHTML_export()
    {
        return $this->as_html(true);
    }

    /**
     * Returns the complete table HTML.
     */
    public function as_html($empty_table = false)
    {
        if ($this->total_number_of_items == 0)
        {
            $cols = $this->getHeader()->getColCount();
            $this->setCellAttributes(0, 0, 'style="font-style: italic;text-align:center;" colspan=' . $cols);
            $this->setCellContents(0, 0, Translation :: get('NoSearchResults', null, Utilities :: COMMON_LIBRARIES));
            $empty_table = true;
        }

        if (! $empty_table)
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
            if ($this->form_actions->has_form_actions())
            {
                $params = $this->get_additional_url_paramstring();
                $html[] = '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?' . $params . '" name="form_' .
                     $this->table_name . '" class="table_form">';
                $html[] = ResourceManager :: get_instance()->get_resource_html(
                    Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'SortableTable.js');
            }
        }

        $html[] = $this->get_table_html();

        if (! $empty_table)
        {
            $html[] = '<table style="width:100%;">';
            $html[] = '<tr>';
            $html[] = '<td colspan="2">';

            if ($this->form_actions->has_form_actions())
            {
                $html[] = '<div class="sortable_table_selection_controls">';
                $html[] = '<span class="sortable_table_selection_controls_options">';
                $html[] = '<a href="?' . $params . '&amp;' . $this->param_prefix .
                     'selectall=1" class="sortable_table_select_all">' .
                     Translation :: get('SelectAll', null, Utilities :: COMMON_LIBRARIES) . '</a>';
                $html[] = '&nbsp;-&nbsp;';
                $html[] = '<a href="?' . $params . '" class="sortable_table_select_none">' .
                     Translation :: get('UnselectAll', null, Utilities :: COMMON_LIBRARIES) . '</a> ';
                $html[] = '</span>';
                $html[] = '<select id="actions_' . $this->table_name . '" name="' . $this->table_name . '_action_value">';

                foreach ($this->form_actions->get_form_actions() as $form_action)
                {
                    if ($form_action instanceof TableFormAction)
                    {
                        $message = $form_action->getConfirmationMessage() ? $form_action->getConfirmationMessage() : Translation :: get(
                            'ConfirmYourSelectionAndAction',
                            null,
                            Utilities :: COMMON_LIBRARIES);

                        $html[] = '<option value="' . base64_encode(serialize($form_action->get_action())) . '"' .
                             ($form_action->get_confirm() ? ' class="confirm" data-message="' . $message . '"' : '') .
                             '>' . $form_action->get_title() . '</option>';
                    }
                }

                $html[] = '</select>';
                $html[] = '<input type="hidden" name="' . $this->table_name . '_namespace" value="' .
                     $this->form_actions->get_namespace() . '"/>';
                $html[] = '<input type="hidden" name="table_name" value="' . $this->table_name . '"/>';
                $html[] = ' <input type="submit" value="' . Translation :: get(
                    'Ok',
                    null,
                    Utilities :: COMMON_LIBRARIES) . '"/>';
            }
            else
            {
                $html[] = $form;
            }

            $html[] = '</td>';
            $html[] = '<td style="text-align:right;">';
            $html[] = $nav;
            $html[] = '</td>';
            $html[] = '</tr>';
            $html[] = '</table>';

            if ($this->form_actions->has_form_actions())
            {
                $html[] = '</form>';
            }

            if ($this->is_ajax_enabled())
            {
                $html[] = '<script type="text/javascript">';
                $html[] = '(function($){';
                $html[] = '';
                $html[] = '$(document).ready(function() {';
                $html[] = '    // Initialise the table';
                $html[] = '    $(".data_table").tableDnD({';
                $html[] = '    });';
                $html[] = '});';
                $html[] = '';
                $html[] = '})(jQuery);';
                $html[] = '</script>';
            }
        }
        return implode(PHP_EOL, $html);
    }

    /**
     * Get the HTML-code with the navigational buttons to browse through the data-pages.
     */
    public function get_navigation_html()
    {
        $pager = $this->get_pager();
        $pager_links = $pager->getLinks();
        $showed_items = $pager->getOffsetByPageId();
        return $pager_links['first'] . ' ' . $pager_links['back'] . ' ' . $pager->getCurrentPageId() . ' / ' .
             $pager->numPages() . ' ' . $pager_links['next'] . ' ' . $pager_links['last'];
    }

    /**
     * Get the HTML-code with the data-table.
     */
    public function get_table_html()
    {
        // Make sure the header isn't dragable or droppable
        // $this->setRowAttributes(0, array('class' => 'nodrag nodrop'), true);

        // Now process the rest of the table
        $pager = $this->get_pager();
        $offset = $pager->getOffsetByPageId();
        $from = $offset[0] - 1;
        $table_data = $this->get_table_data($from);

        foreach ($table_data as $index => & $row)
        {
            $row_id = $row[0];
            $row = $this->filter_data($row);
            $current_row = $this->addRow($row);
            $this->setRowAttributes($current_row, array('id' => 'row_' . $row_id), true);
        }

        $this->altRowAttributes(0, array('class' => 'row_even'), array('class' => 'row_odd'), true);

        foreach ($this->th_attributes as $column => & $attributes)
        {
            $this->setCellAttributes(0, $column, $attributes);
        }
        foreach ($this->td_attributes as $column => & $attributes)
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
        if ($this->allow_page_selection)
        {
            $total_number_of_items = $this->total_number_of_items;
            if ($total_number_of_items <= self :: DISPLAY_PER_INCREMENT)
            {
                return '';
            }
            $result[] = '<form method="get" action="' . $_SERVER['PHP_SELF'] . '" style="display:inline;">';
            $param[$this->param_prefix . 'direction'] = $this->direction;
            $param[$this->param_prefix . 'page_nr'] = $this->page_nr;
            $param[$this->param_prefix . 'column'] = $this->column;
            $param = array_merge($param, $this->additional_parameters);

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
            $result[] = '<select name="' . $this->param_prefix . 'per_page" onchange="javascript:this.form.submit();">';

            // calculate the roundup for the interval
            $total_number_of_items_upper_interval = ceil($total_number_of_items / self :: DISPLAY_PER_INCREMENT) *
                 self :: DISPLAY_PER_INCREMENT;

            $minimum = min(self :: DISPLAY_PER_INCREMENT_INTERVAL_LIMIT, $total_number_of_items_upper_interval);

            for ($nr = self :: DISPLAY_PER_INCREMENT; $nr <= $minimum; $nr += self :: DISPLAY_PER_INCREMENT)
            {
                $result[] = '<option value="' . $nr . '" ' . ($nr == $this->per_page ? 'selected="selected"' : '') . '>' .
                     $nr . '</option>';
            }
            if ($total_number_of_items < self :: DISPLAY_PER_PAGE_LIMIT)
            {
                $all_text = Translation :: get('All', Utilities :: COMMON_LIBRARIES);
                $result[] = '<option value="' . self :: DISPLAY_ALL . '" ' .
                     ($total_number_of_items == $this->per_page ? 'selected="selected"' : '') . '>' . $all_text .
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
        $showed_items = $this->get_pager()->getOffsetByPageId();
        return $showed_items[0] . ' - ' . $showed_items[1] . ' / ' . $this->total_number_of_items;
    }

    /**
     * Set the header-label
     *
     * @param $column int The column number
     * @param $label string The label
     * @param $sortable boolean Is the table sortable by this column? (defatult = true)
     * @param $th_attributes string Additional attributes for the th-tag of the table header
     * @param $td_attributes string Additional attributes for the td-tags of the column
     */
    public function set_header($column, $label, $sortable = true, $th_attributes = null, $td_attributes = null)
    {
        $header = $this->getHeader();
        for ($i = 0; $i < count($th_attributes); $i ++)
        {
            $header->setColAttributes($i, $th_attributes[$i]);
        }

        $param['direction'] = SORT_ASC;
        if ($this->column == $column && $this->direction == SORT_ASC)
        {
            $param['direction'] = SORT_DESC;
        }
        $param['page_nr'] = $this->page_nr;
        $param['per_page'] = $this->per_page;
        $param['column'] = $column;
        if ($sortable)
        {
            $link = '<a href="' . $_SERVER['PHP_SELF'] . '?';
            foreach ($param as $key => & $value)
            {
                $link .= $this->param_prefix . $key . '=' . urlencode($value) . '&amp;';
            }
            $link .= $this->get_additional_url_paramstring();
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
        if (! is_null($td_attributes))
        {
            $this->td_attributes[$column] = $td_attributes;
        }
        if (! is_null($th_attributes))
        {
            $this->th_attributes[$column] = $th_attributes;
        }

        return $link;
    }

    /**
     * Get the parameter-string with additional parameters to use in the URLs generated by this SortableTable
     */
    public function get_additional_url_paramstring()
    {
        $parameters = $this->additional_parameters;

        foreach ($this->other_tables as $index => & $tablename)
        {
            if (Request :: get($tablename . '_direction'))
                $parameters[$tablename . '_direction'] = Request :: get($tablename . '_direction');
            if (Request :: get($tablename . '_page_nr'))
                $parameters[$tablename . '_page_nr'] = Request :: get($tablename . '_page_nr');
            if (Request :: get($tablename . '_per_page'))
                $parameters[$tablename . '_per_page'] = Request :: get($tablename . '_per_page');
            if (Request :: get($tablename . '_column'))
                $parameters[$tablename . '_column'] = Request :: get($tablename . '_column');
        }
        return http_build_query($parameters, '', Redirect :: ARGUMENT_SEPARATOR);
    }

    /**
     * Get the parameter-string with the SortableTable-related parameters to use in URLs
     */
    public function get_sortable_table_param_string()
    {
        $param[$this->param_prefix . 'direction'] = $this->direction;
        $param[$this->param_prefix . 'page_nr'] = $this->page_nr;
        $param[$this->param_prefix . 'per_page'] = $this->per_page;
        $param[$this->param_prefix . 'column'] = $this->column;
        $param_string_parts = array();
        foreach ($param as $key => & $value)
        {
            $param_string_parts[] = urlencode($key) . '=' . urlencode($value);
        }
        return implode('&amp;', $param_string_parts);
    }

    /**
     * Add a filter to a column. If another filter was allready defined for the given column, it will be overwritten.
     *
     * @param $column int The number of the column
     * @param $function string The name of the filter-function. This should be a function wich requires 1 parameter and
     *            returns the filtered value.
     */
    public function set_column_filter($column, $function)
    {
        $this->column_filters[$column] = $function;
    }

    /**
     * Define a list of actions which can be performed on the table-date. If you define a list of actions, the first
     * column of the table will be converted into checkboxes.
     *
     * @param $actions array A list of actions. The key is the name of the action. The value is the label to show in the
     *            select-box
     * @param $checkbox_name string The name of the generated checkboxes. The value of the checkbox will be the value of
     *            the first column.
     */
    public function set_form_actions($actions, $checkbox_name = 'id', $select_name = 'action')
    {
        $this->form_actions = $actions;
        $this->checkbox_name = $checkbox_name;
        $this->form_actions_select_name = $select_name;
    }

    /**
     * Define a list of additional parameters to use in the generated URLs
     *
     * @param $parameters array
     */
    public function set_additional_parameters($parameters)
    {
        $this->additional_parameters = $parameters;
    }

    /**
     * Set other tables on the same page. If you have other sortable tables on the page displaying this sortable tables,
     * you can define those other tables with this function. If you don't define the other tables, there sorting and
     * pagination will return to their default state when sorting this table.
     *
     * @param $tablenames array An array of table names.
     */
    public function set_other_tables($tablenames)
    {
        $this->other_tables = $tablenames;
    }

    /**
     * Transform all data in a table-row, using the filters defined by the function set_column_filter(...) defined
     * elsewhere in this class. If you've defined actions, the first element of the given row will be converted into a
     * checkbox
     *
     * @param $row array A row from the table.
     */
    public function filter_data($row)
    {
        $url_params = $this->get_sortable_table_param_string() . '&amp;' . $this->get_additional_url_paramstring();
        foreach ($this->column_filters as $column => $function)
        {
            $row[$column] = call_user_func($function, $row[$column], $url_params);
        }
        if ($this->form_actions->has_form_actions())
        {
            if (strlen($row[0]) > 0)
            {
                $row[0] = '<input class="' . $this->checkbox_name . '" type="checkbox" name="' . $this->checkbox_name .
                     '[]" value="' . $row[0] . '"';
                if (Request :: get($this->param_prefix . 'selectall'))
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
     * Get the total number of items. This function calls the function given as 2nd argument in the constructor of a
     * SortableTable. Make sure your function has the same parameters as defined here.
     */
    public function get_total_number_of_items()
    {
        if ($this->total_number_of_items == - 1 && ! is_null($this->get_total_number_function))
        {
            $this->total_number_of_items = call_user_func($this->get_total_number_function);
        }
        return $this->total_number_of_items;
    }

    /**
     * Get the data to display. This function calls the function given as 2nd argument in the constructor of a
     * SortableTable. Make sure your function has the same parameters as defined here.
     *
     * @param $from int Index of the first item to return.
     * @param $per_page int The number of items to return
     * @param $column int The number of the column on which the data should be sorted
     * @param $direction string In which order should the data be sorted (ASC or DESC)
     */
    public function get_table_data($from = null, $per_page = null, $column = null, $direction = null)
    {
        if (! is_null($this->get_data_function))
        {
            return call_user_func($this->get_data_function, $from, $this->per_page, $this->column, $this->direction);
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
        return $this->ajax_enabled;
    }

    /**
     * Sets the table's AJAX status to true
     */
    public function enable_ajax()
    {
        $this->ajax_enabled = true;
    }

    /**
     * Sets the table's AJAX status to false
     */
    public function disable_ajax()
    {
        $this->ajax_enabled = false;
    }

    public function get_per_page()
    {
        return $this->per_page;
    }

    public function get_column()
    {
        return $this->column;
    }

    public function get_direction()
    {
        return $this->direction;
    }
}