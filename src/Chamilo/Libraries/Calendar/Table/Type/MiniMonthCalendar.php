<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthCalendar extends MonthCalendar
{

    /**
     *
     * @param string $urlFormat
     */
    public function addNavigationLinks($urlFormat)
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

    /**
     *
     * @param integer $time
     * @param string[] $items
     * @param integer $row
     * @param integer $column
     */
    protected function handleItems($time, $items, $row, $column)
    {
        $tooltip = htmlentities(implode("\n", $items));

        if (date('Ymd', $time) != date('Ymd'))
        {
            try
            {
                $this->setCellContents(
                    $row,
                    $column,
                    '<span class="badge" data-toggle="tooltip" data-placement="top" data-content="' . $tooltip . '">' .
                         $this->getCellContents($row, $column) . '</span>');
            }
            catch (\Exception $exception)
            {
            }
        }
    }
}
