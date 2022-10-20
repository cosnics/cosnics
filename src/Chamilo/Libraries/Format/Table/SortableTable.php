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
     * @param string[] $row
     *
     * @return string[]
     */
    public function filterData(
        array $row, TableParameterValues $parameterValues, ?TableFormActions $tableFormActions = null
    ): array
    {
        $hasActions = $tableFormActions instanceof TableFormActions && $tableFormActions->hasFormActions();

        if ($hasActions)
        {
            if (strlen($row[0]) > 0)
            {
                $row[0] = $this->getCheckboxHtml($tableFormActions, $parameterValues, $row[0]);
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

    public function getFormClasses(): string
    {
        return 'form-table';
    }

    public function getTableActionsJavascript(): string
    {
        return ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(StringUtilities::LIBRARIES, true) . 'SortableTable.js'
        );
    }

    public function getTableClasses(): string
    {
        return 'table table-striped table-bordered table-hover table-data';
    }

    public function getTableContainerClasses(): string
    {
        return 'table-responsive';
    }
}