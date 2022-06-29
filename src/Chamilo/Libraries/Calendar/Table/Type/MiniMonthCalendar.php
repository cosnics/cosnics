<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Exception;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthCalendar extends MonthCalendar
{
    public const PERIOD_DAY = 2;
    public const PERIOD_MONTH = 0;
    public const PERIOD_WEEK = 1;

    public function __construct(int $displayTime)
    {
        parent::__construct($displayTime, null, ['table-calendar-mini']);
    }

    public function render(): string
    {
        $this->addEvents();

        return $this->toHtml();
    }

    public function addEvents()
    {
        $events = $this->getEventsToShow();
        $cellMapping = $this->getCellMapping();

        foreach ($events as $time => $items)
        {
            $cellMappingKey = date('Ymd', $time);

            $row = $cellMapping[$cellMappingKey][0];
            $column = $cellMapping[$cellMappingKey][1];

            if (is_null($row) || is_null($column))
            {
                continue;
            }

            $tooltip = htmlentities(implode(PHP_EOL, $items));

            if (date('Ymd', $time) != date('Ymd'))
            {
                try
                {
                    $this->setCellContents(
                        $row, $column,
                        '<span class="badge" data-toggle="tooltip" data-placement="top" data-content="' . $tooltip .
                        '">' . $this->getCellContents($row, $column) . '</span>'
                    );
                }
                catch (Exception $exception)
                {
                }
            }
        }
    }

    public function addNavigationLinks(string $urlFormat)
    {
        $day = $this->getStartTime();
        $row = 0;
        $maxRows = $this->getRowCount();

        while ($row < $maxRows)
        {
            for ($col = 0; $col < 7; $col ++)
            {
                $url = str_replace(self::TIME_PLACEHOLDER, $day, $urlFormat);
                $content = $this->getCellContents($row, $col);
                $content = '<a href="' . $url . '">' . $content . '</a>';
                $this->setCellContents($row, $col, $content);
                $day = strtotime('+24 Hours', $day);
            }

            $row ++;
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
