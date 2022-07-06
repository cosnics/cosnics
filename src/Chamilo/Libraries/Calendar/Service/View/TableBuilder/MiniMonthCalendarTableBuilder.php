<?php
namespace Chamilo\Libraries\Calendar\Service\View\TableBuilder;

use Exception;
use HTML_Table;

/**
 * @package Chamilo\Libraries\Calendar\Service\View\TableBuilder
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthCalendarTableBuilder extends MonthCalendarTableBuilder
{
    protected function addEventItems(HTML_Table $table, $time, $row, $column, $items)
    {
        $tooltip = htmlentities(implode(PHP_EOL, $items));

        if (date('Ymd', $time) != date('Ymd'))
        {
            try
            {
                $table->setCellContents(
                    $row, $column,
                    '<span class="badge" data-toggle="tooltip" data-placement="top" data-content="' . $tooltip . '">' .
                    $table->getCellContents($row, $column) . '</span>'
                );
            }
            catch (Exception $exception)
            {
            }
        }
    }

    protected function determineCellContent(int $tableDate, ?string $dayUrlTemplate = null): string
    {
        $cellContent = parent::determineCellContent($tableDate);

        // Is current table date today?
        if (date('Ymd', $tableDate) == date('Ymd'))
        {
            $cellContent = '<span class="badge">' . $cellContent . '</span>';
        }

        return $cellContent;
    }
}
