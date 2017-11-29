<?php
namespace Chamilo\Libraries\Calendar\Service\HtmlTable;

use Chamilo\Libraries\Calendar\Format\Renderer\ViewRenderer;
use Chamilo\Libraries\Calendar\HtmlTable\MonthCalendar;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service\HtmlTable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthCalendarBuilder extends CalendarBuilder
{

    /**
     *
     * @param integer $displayTime
     * @param string[] $classes
     * @return \Chamilo\Libraries\Calendar\HtmlTable\MonthCalendar
     */
    protected function getCalendar($displayTime, $classes = [])
    {
        return new MonthCalendar($this->getCalendarConfiguration(), $displayTime, $classes);
    }

    /**
     *
     * @param integer $displayTime
     * @param string[] $displayParameters
     * @param string[] $classes
     * @return \Chamilo\Libraries\Calendar\HtmlTable\MonthCalendar
     */
    public function buildCalendar($displayTime, $displayParameters = [], $classes = [])
    {
        $monthCalendar = $this->getCalendar($displayTime, $classes);

        $firstDay = mktime(0, 0, 0, date('m', $displayTime), 1, date('Y', $displayTime));
        $tableDate = $this->getFirstTableDate($firstDay);
        $cell = 0;

        while (date('Ym', $tableDate) <= date('Ym', $displayTime))
        {
            do
            {
                $row = intval($cell / 7);
                $column = $cell % 7;

                $monthCalendar->setCellMappingForKey(date('Ymd', $tableDate), array($row, $column));

                $classes = $this->determineCellClasses($tableDate, $displayTime);

                if (count($classes) > 0)
                {
                    $monthCalendar->updateCellAttributes($row, $column, 'class="' . implode(' ', $classes) . '"');
                }

                $monthCalendar->setCellContents($row, $column, $this->determineCellContent($tableDate));

                $cell ++;
                $tableDate = strtotime('+1 Day', $tableDate);
            }
            while ($cell % 7 != 0);
        }

        $this->setHeader($monthCalendar);

        return $monthCalendar;
    }

    /**
     *
     * @param integer $firstDay
     * @return integer
     */
    protected function getFirstTableDate($firstDay)
    {
        $firstDayOfTheWeek = $this->getFirstDayOfTheWeek();

        if ($firstDayOfTheWeek == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $firstDay));
        }
        else
        {
            return strtotime('Next Monday', strtotime('-1 Week', $firstDay));
        }
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Table\Type\MonthCalendar $monthCalendar
     */
    protected function setHeader(MonthCalendar $monthCalendar)
    {
        $header = $monthCalendar->getHeader();

        $firstDayOfTheWeek = $this->getFirstDayOfTheWeek();
        $translator = $this->getTranslator();

        if ($firstDayOfTheWeek == 'sunday')
        {
            $header->addRow(
                array(
                    $translator->trans('SundayShort', [], Utilities::COMMON_LIBRARIES),
                    $translator->trans('MondayShort', [], Utilities::COMMON_LIBRARIES),
                    $translator->trans('TuesdayShort', [], Utilities::COMMON_LIBRARIES),
                    $translator->trans('WednesdayShort', [], Utilities::COMMON_LIBRARIES),
                    $translator->trans('ThursdayShort', [], Utilities::COMMON_LIBRARIES),
                    $translator->trans('FridayShort', [], Utilities::COMMON_LIBRARIES),
                    $translator->trans('SaturdayShort', [], Utilities::COMMON_LIBRARIES)));
        }
        else
        {
            $header->addRow(
                array(
                    $translator->trans('MondayShort', [], Utilities::COMMON_LIBRARIES),
                    $translator->trans('TuesdayShort', [], Utilities::COMMON_LIBRARIES),
                    $translator->trans('WednesdayShort', [], Utilities::COMMON_LIBRARIES),
                    $translator->trans('ThursdayShort', [], Utilities::COMMON_LIBRARIES),
                    $translator->trans('FridayShort', [], Utilities::COMMON_LIBRARIES),
                    $translator->trans('SaturdayShort', [], Utilities::COMMON_LIBRARIES),
                    $translator->trans('SundayShort', [], Utilities::COMMON_LIBRARIES)));
        }

        $header->setRowType(0, 'th');
    }

    /**
     *
     * @param integer $tableDate
     * @param integer $displayTime
     * @return string[]
     */
    protected function determineCellClasses($tableDate, $displayTime)
    {
        $classes = [];

        // Is current table date today?
        if (date('Ymd', $tableDate) == date('Ymd'))
        {
            $classes[] = 'table-calendar-highlight';
        }

        // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
        if (date('w', $tableDate) % 6 == 0)
        {
            $classes[] = 'table-calendar-weekend';
        }

        // Is current table date in this month or another one?
        if (date('Ym', $tableDate) != date('Ym', $displayTime))
        {
            $classes[] = 'table-calendar-disabled';
        }

        return $classes;
    }

    /**
     *
     * @param integer $tableDate
     * @param string[] $displayParameters
     * @return string
     */
    protected function determineCellContent($tableDate, $displayParameters = [])
    {
        $dayLabel = date('j', $tableDate);
        $dayUrlTemplate = $this->getDayUrlTemplate($displayParameters);

        if (is_null($dayUrlTemplate))
        {
            return $dayLabel;
        }
        else
        {
            return '<a href="' . $this->getDayUrl($tableDate) . '">' . $dayLabel . '</a>';
        }
    }

    /**
     *
     * @param string[] $displayParameters
     * @return string
     */
    protected function getDayUrlTemplate($displayParameters = [])
    {
        $displayParameters[ViewRenderer::PARAM_TIME] = self::TIME_PLACEHOLDER;
        $displayParameters[ViewRenderer::PARAM_TYPE] = ViewRenderer::TYPE_DAY;

        $dayUrlTemplate = new Redirect($displayParameters);

        return $dayUrlTemplate->getUrl();
    }

    /**
     *
     * @param integer $time
     * @return string
     */
    protected function getDayUrl($time)
    {
        return str_replace(self::TIME_PLACEHOLDER, $time, $this->getDayUrlTemplate());
    }
}

