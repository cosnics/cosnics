<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SortableTable extends HtmlTable
{

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
            $tableName,
            $sourceCountFunction,
            $sourceDataFunction,
            $defaultOrderColumn,
            $defaultNumberOfItemsPerPage,
            $defaultOrderDirection,
            $allowPageSelection,
            $allowPageNavigation);
    }

    /**
     *
     * @return string
     */
    public function getTableClasses()
    {
        return 'table table-striped table-bordered table-hover table-data';
    }

    /**
     *
     * @return integer
     */
    public function getColumnCount()
    {
        return 1;
    }

    /**
     *
     * @return string
     */
    public function getFormClasses()
    {
        return 'form-table';
    }

    public function getTableActionsJavascript()
    {
        return ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(Utilities :: COMMON_LIBRARIES, true) . 'SortableTable.js');
    }

    public function getTableContainerClasses()
    {
        return 'table-responsive';
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

        return \HTML_Table :: toHTML();
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Table\FormAction\TableFormActions $actions
     */
    public function setTableFormActions(TableFormActions $actions = null)
    {
        parent :: setTableFormActions($actions);

        if ($actions instanceof TableFormActions && $actions->has_form_actions())
        {
            $columnHeaderHtml = '<div class="checkbox checkbox-primary"><input class="styled styled-primary sortableTableSelectToggle" type="checkbox" name="sortableTableSelectToggle" /><label></label></div>';
        }
        else
        {
            $columnHeaderHtml = '';
        }

        $this->setColumnHeader(0, $columnHeaderHtml, false);
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
                $row[0] = '<div class="checkbox checkbox-primary"><input class="styled styled-primary" type="checkbox" name="' .
                     $this->getTableFormActions()->getIdentifierName() . '[]" value="' . $row[0] . '"';

                if (Request :: get($this->getParameterName('selectall')))
                {
                    $row[0] .= ' checked="checked"';
                }

                $row[0] .= '/><label></label></div>';
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
}