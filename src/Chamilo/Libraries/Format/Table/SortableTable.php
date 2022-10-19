<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class SortableTable extends HtmlTable
{

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
    public function filterData(array $row): array
    {
        $hasActions =
            $this->getTableFormActions() instanceof TableFormActions && $this->getTableFormActions()->hasFormActions();

        if ($hasActions)
        {
            if (strlen($row[0]) > 0)
            {
                $row[0] = $this->getCheckboxHtml($row[0]);
            }
        }

        foreach ($row as $index => & $value)
        {
            if (!is_numeric($value) && empty($value))
            {
                $value = '-';
            }
        }

        return $row;
    }

    /**
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::getFormClasses()
     */
    public function getFormClasses()
    {
        return 'form-table';
    }

    /**
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::getTableActionsJavascript()
     */
    public function getTableActionsJavascript()
    {
        return ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(StringUtilities::LIBRARIES, true) . 'SortableTable.js'
        );
    }

    /**
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::getTableClasses()
     */
    public function getTableClasses()
    {
        return 'table table-striped table-bordered table-hover table-data';
    }

    /**
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::getTableContainerClasses()
     */
    public function getTableContainerClasses()
    {
        return 'table-responsive';
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    public function setTableFormActions(TableFormActions $actions = null)
    {
        parent::setTableFormActions($actions);

        if ($actions instanceof TableFormActions && $actions->hasFormActions())
        {
            $columnHeaderHtml =
                '<div class="checkbox checkbox-primary"><input class="styled styled-primary sortableTableSelectToggle" type="checkbox" name="sortableTableSelectToggle" /><label></label></div>';
        }
        else
        {
            $columnHeaderHtml = '';
        }

        $this->setColumnHeader(0, $columnHeaderHtml, false);
    }
}