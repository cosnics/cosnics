<?php
namespace Chamilo\Libraries\Calendar\Service\View\Table;

use Exception;

/**
 * @package Chamilo\Libraries\Calendar\Service\View\Table
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthCalendarTable extends MonthCalendarTable
{
    public function __construct(int $displayTime, ?string $dayUrlTemplate = null)
    {
        parent::__construct($displayTime, $dayUrlTemplate, ['table-calendar-mini']);
    }

    protected function addEventItems($time, $row, $column, $items)
    {
        $tooltip = htmlentities(implode(PHP_EOL, $items));

        if (date('Ymd', $time) != date('Ymd'))
        {
            try
            {
                $this->setCellContents(
                    $row, $column,
                    '<span class="badge" data-toggle="tooltip" data-placement="top" data-content="' . $tooltip . '">' .
                    $this->getCellContents($row, $column) . '</span>'
                );
            }
            catch (Exception $exception)
            {
            }
        }
    }

    protected function determineCellContent(int $tableDate): string
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
